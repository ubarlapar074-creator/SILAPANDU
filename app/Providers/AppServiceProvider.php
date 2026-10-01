<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if (app()->environment('local') && file_exists(public_path('hot'))) {
            $hotTime = filemtime(public_path('hot'));
            $manifest = public_path('build/manifest.json');
            $stale = !file_exists($manifest) || $hotTime < filemtime($manifest) - 5;
            $viteRunning = false;
            if (!$stale) {
                $hotUrl = trim((string) file_get_contents(public_path('hot')));
                if ($hotUrl !== '') {
                    $ctx = stream_context_create(['http' => ['timeout' => 0.5, 'ignore_errors' => true]]);
                    $headers = @get_headers($hotUrl, false, $ctx);
                    $viteRunning = $headers && str_contains((string) $headers[0], '200');
                }
            }
            if ($stale || !$viteRunning) {
                @unlink(public_path('hot'));
            }
        }
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            return $src !== null ? ['data-vite-src' => $src] : [];
        });
    }
}
