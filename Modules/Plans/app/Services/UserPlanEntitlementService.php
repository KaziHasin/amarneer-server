<?php

namespace Modules\Plans\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Plans\Models\UserPlan;

class UserPlanEntitlementService
{
    public function getActiveUserPlan(User $user): ?UserPlan
    {
        return UserPlan::query()
            ->with('plan')
            ->where('user_id', $user->id)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->orderByDesc('expires_at')
            ->first();
    }

    public function canUnlockContact(User $user): bool
    {
        $userPlan = $this->getActiveUserPlan($user);
        if (!$userPlan) {
            return false;
        }

        $limit = $userPlan->plan?->contact_limit;
        if ($limit === null) {
            return true;
        }

        return $userPlan->contacts_used < $limit;
    }

    /**
     * Check if a user has already unlocked a specific property under their current active plan.
     * @param User $user
     * @param int $propertyId
     * @return bool
     */
    public function hasAlreadyUnlockedProperty(User $user, int $propertyId): bool
    {
        $userPlan = $this->getActiveUserPlan($user);
        if (!$userPlan) {
            return false;
        }

        return DB::table('contact_unlock_logs')
            ->where('user_plan_id', $userPlan->id)
            ->where('property_id', $propertyId)
            ->exists();
    }

    /**
     * Atomically consumes 1 contact unlock if available.
     * If the user has already unlocked this property under the current plan, returns true
     * without consuming an additional credit (idempotent).
     *
     * @return array{consumed: bool, already_unlocked: bool}
     */
    public function consumeContactUnlock(User $user, int $propertyId): array
    {
        return DB::transaction(function () use ($user, $propertyId) {
            $userPlan = UserPlan::query()
                ->with('plan')
                ->where('user_id', $user->id)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->orderByDesc('expires_at')
                ->lockForUpdate()
                ->first();

            if (!$userPlan) {
                return ['consumed' => false, 'already_unlocked' => false];
            }

            // Check if this property was already unlocked under this plan period
            $alreadyUnlocked = DB::table('contact_unlock_logs')
                ->where('user_plan_id', $userPlan->id)
                ->where('property_id', $propertyId)
                ->exists();

            if ($alreadyUnlocked) {
                // Don't consume a credit — just return the contact
                return ['consumed' => true, 'already_unlocked' => true];
            }

            // First time unlock — check the limit
            $limit = $userPlan->plan?->contact_limit;
            if ($limit !== null && $userPlan->contacts_used >= $limit) {
                return ['consumed' => false, 'already_unlocked' => false];
            }

            // Consume 1 credit and log the unlock
            $userPlan->increment('contacts_used');

            DB::table('contact_unlock_logs')->insert([
                'user_id' => $user->id,
                'user_plan_id' => $userPlan->id,
                'property_id' => $propertyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ['consumed' => true, 'already_unlocked' => false];
        }, 3);
    }
}
