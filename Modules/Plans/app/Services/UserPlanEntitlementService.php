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
     * Atomically consumes 1 contact unlock if available.
     */
    public function consumeContactUnlock(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $userPlan = UserPlan::query()
                ->with('plan')
                ->where('user_id', $user->id)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->orderByDesc('expires_at')
                ->lockForUpdate()
                ->first();

            if (!$userPlan) {
                return false;
            }

            $limit = $userPlan->plan?->contact_limit;
            if ($limit !== null && $userPlan->contacts_used >= $limit) {
                return false;
            }

            $userPlan->increment('contacts_used');
            return true;
        }, 3);
    }
}

