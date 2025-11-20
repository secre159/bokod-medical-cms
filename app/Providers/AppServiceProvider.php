<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\UpdateLastLoginAt;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

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
        // Gates are defined in AdminLTEServiceProvider
        
        // Register login event listener to update last login timestamp
        Event::listen(Login::class, UpdateLastLoginAt::class);
        
        // Automatically detect and force the correct URL scheme
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        
        // Auto-detect URL from request if not in console
        if (!app()->runningInConsole() && request()) {
            $url = $this->detectApplicationUrl();
            if ($url && $this->isValidUrl($url)) {
                config(['app.url' => $url]);
                URL::forceRootUrl($url);
            }
        }

        // Apply mail configuration from settings at runtime
        $this->applyMailConfigFromSettings();
    }
    
    /**
     * Detect the application URL from the current request
     */
    private function detectApplicationUrl(): ?string
    {
        $request = request();
        
        if (!$request) {
            return null;
        }
        
        // Check for reverse proxy headers (like when behind CloudFlare, AWS ALB, etc.)
        $scheme = $request->header('X-Forwarded-Proto', 
                 $request->header('X-Forwarded-Protocol',
                 $request->isSecure() ? 'https' : 'http'));
        
        // Get the host (handles X-Forwarded-Host for proxies)
        $host = $request->header('X-Forwarded-Host',
               $request->header('Host',
               $request->getHttpHost()));
        
        // Clean up the scheme and host
        $scheme = strtolower($scheme);
        if (!in_array($scheme, ['http', 'https'])) {
            $scheme = $request->isSecure() ? 'https' : 'http';
        }
        
        return $scheme . '://' . $host;
    }
    
    /**
     * Validate if the detected URL is acceptable
     */
    private function isValidUrl(string $url): bool
    {
        // Parse the URL
        $parsed = parse_url($url);
        
        if (!$parsed || !isset($parsed['host'])) {
            return false;
        }
        
        $host = $parsed['host'];
        
        // Allow common development hosts
        $allowedHosts = [
            'localhost',
            '127.0.0.1',
            '::1'
        ];
        
        // Allow local network IPs (192.168.x.x, 10.x.x.x, 172.16-31.x.x)
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE) === false) {
                return true; // It's a private IP, allow it
            }
        }
        
        // Allow if it's in our allowed list
        if (in_array($host, $allowedHosts)) {
            return true;
        }
        
        // Allow any domain with a proper TLD in production
        if (app()->environment('production')) {
            return preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/', $host) === 1;
        }
        
        // In development, be more permissive
        return !app()->environment('production');
    }

    /**
     * Apply mail configuration from DB settings at runtime.
     */
    private function applyMailConfigFromSettings(): void
    {
        try {
            $driver = Setting::get('mail_driver', config('mail.default', 'smtp'));
            if (!in_array($driver, ['smtp','ses','postmark','log','array','failover'])) {
                $driver = 'smtp';
            }
            config(['mail.default' => $driver]);

            // Common FROM settings
            $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName    = Setting::get('mail_from_name', config('mail.from.name'));
            if ($fromAddress) config(['mail.from.address' => $fromAddress]);
            if ($fromName)    config(['mail.from.name' => $fromName]);

            if ($driver === 'smtp') {
                $host = Setting::get('mail_host', config('mail.mailers.smtp.host'));
                $port = (int) Setting::get('mail_port', (int) config('mail.mailers.smtp.port'));
                $enc  = Setting::get('mail_encryption', config('mail.mailers.smtp.encryption')) ?: null;
                $user = Setting::get('mail_username', config('mail.mailers.smtp.username'));
                // Get password raw (may be encrypted)
                $raw  = DB::table('settings')->where('key','mail_password')->value('value');
                $pass = null;
                if ($raw !== null && $raw !== '') {
                    try { $pass = decrypt($raw); } catch (\Throwable $e) { $pass = $raw; }
                }
                config([
                    'mail.mailers.smtp.host' => $host,
                    'mail.mailers.smtp.port' => $port ?: 587,
                    'mail.mailers.smtp.encryption' => $enc,
                    'mail.mailers.smtp.username' => $user,
                    'mail.mailers.smtp.password' => $pass,
                    'mail.mailers.smtp.timeout' => 10,
                ]);
            }
        } catch (\Throwable $e) {
            // Don't break the app if settings are missing; leave defaults
            // You can inspect logs if mail config fails to apply
        }
    }
}
