<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                View::share('villageProfile', Setting::villageProfile());
            } else {
                $this->shareDefaultVillageProfile();
            }
        } catch (\Throwable $e) {
            $this->shareDefaultVillageProfile();
        }
    }

    private function shareDefaultVillageProfile(): void
    {
        View::share('villageProfile', (object) [
            'village_name' => 'Desa Silapandu',
            'district_name' => null,
            'regency_name' => null,
            'office_address' => null,
            'village_head_name' => null,
            'village_head_nip' => null,
            'service_officer_name' => null,
            'service_officer_nip' => null,
            'kua_head_name' => null,
            'kua_head_nip' => null,
        ]);
    }
}
