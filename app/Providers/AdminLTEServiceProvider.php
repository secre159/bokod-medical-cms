<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class AdminLTEServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Update AdminLTE configuration with database settings
        $this->updateAdminLTEConfig();
        
        // Define gates for AdminLTE menu permissions
        Gate::define('admin', function ($user) {
            return $user && $user->isAdmin();
        });
        
        Gate::define('patient', function ($user) {
            return $user && $user->isPatient();
        });
        
        Gate::define('active', function ($user) {
            return $user && $user->isActive();
        });
        
        // Specific admin permission gates
        Gate::define('manage-patients', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
        
        Gate::define('manage-appointments', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
        
        Gate::define('manage-medicines', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
        
        Gate::define('view-reports', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
        
        Gate::define('manage-users', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
        
        Gate::define('manage-settings', function ($user) {
            return $user && $user->isAdmin() && $user->isActive();
        });
    }
    
    /**
     * Update AdminLTE configuration with dynamic settings from database
     */
    private function updateAdminLTEConfig(): void
    {
        try {
            // Get settings from database
            $appName = Setting::get('app_name', 'BOKOD CMS');
            $appLogo = Setting::get('app_logo', 'images/logo.png');
            $appFavicon = Setting::get('app_favicon', '');
            
            // Update title
            Config::set('adminlte.title', $appName);
            Config::set('adminlte.title_postfix', ' - Patient Management System');
            
            // Update logo configuration
            Config::set('adminlte.logo', '<b>' . strtoupper(explode(' ', $appName)[0]) . '</b> ' . 
                       (count(explode(' ', $appName)) > 1 ? explode(' ', $appName)[1] : ''));
            
            // If custom logo is uploaded, use storage path, otherwise use default
            if ($appLogo && $appLogo !== 'images/logo.png') {
                Config::set('adminlte.logo_img', 'storage/' . $appLogo);
                Config::set('adminlte.auth_logo.img.path', 'storage/' . $appLogo);
                Config::set('adminlte.preloader.img.path', 'storage/' . $appLogo);
            } else {
                Config::set('adminlte.logo_img', 'images/logo.png');
                Config::set('adminlte.auth_logo.img.path', 'images/logo.png');
                Config::set('adminlte.preloader.img.path', 'images/logo.png');
            }
            
            // Update auth logo alt text
            Config::set('adminlte.auth_logo.img.alt', $appName);
            Config::set('adminlte.logo_img_alt', $appName . ' Logo');
            Config::set('adminlte.preloader.img.alt', $appName . ' Loading');
            
            // Enable favicon if configured
            if ($appFavicon) {
                Config::set('adminlte.use_full_favicon', true);
                // Store favicon path for use in views
                Config::set('app.favicon', 'storage/' . $appFavicon);
            } else {
                Config::set('adminlte.use_full_favicon', false);
            }
            
        } catch (\Exception $e) {
            // If database is not available or settings table doesn't exist,
            // keep default values
            \Log::warning('Could not load dynamic AdminLTE settings: ' . $e->getMessage());
        }
    }
}
