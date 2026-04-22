<?php

namespace Modules\Properties\Services;

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
}

