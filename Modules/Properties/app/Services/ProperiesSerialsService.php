<?php

namespace Modules\Properties\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ProperiesSerialsService
{
    /**
     * Build a browser URL for a public disk path. Uses a root-relative URL so the
     * current host (e.g. Valet .test) is used even when APP_URL in .env differs.
     * @param string $filePath 
     * @return ?string
     */
    public function getPublicPath(?string $filePath): ?string
    {
        if ($filePath === null) {
            return null;
        }

        $raw = trim(str_replace('\\', '/', (string) $filePath));
        if ($raw === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        $path = ltrim($raw, '/');
        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        return '/storage/' . $path;
    }

    /**
     * Create a new user with type owner
     * @param array $ownerData
     * @return User
     */
    public function createOrUpdatePropertyOwner(array $ownerData): User
    {
        $owner = User::where('mobile', $ownerData['mobile'])->first();
        if ($owner) {
            $owner->name = $ownerData['name'];
            $owner->email = $ownerData['email'];
            $owner->save();
        } else {
            $owner = User::create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'] ?? ($ownerData['mobile'] . '@amarneer.in'),
                'mobile' => $ownerData['mobile'],
                'password' => Hash::make(Str::random(12)),
                'type' => 'owner',
            ]);
        }

        return $owner;
    }
}

