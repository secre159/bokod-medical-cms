<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;

class UrlService
{
    /**
     * Get the application base URL
     */
    public static function getBaseUrl(): string
    {
        return config('app.url');
    }
    
    /**
     * Get the patient portal login URL
     */
    public static function getPatientLoginUrl(): string
    {
        return self::getBaseUrl() . '/login';
    }
    
    /**
     * Get the patient dashboard URL
     */
    public static function getPatientDashboardUrl(): string
    {
        return route('patient.dashboard.index');
    }
    
    /**
     * Get the patient profile URL
     */
    public static function getPatientProfileUrl(): string
    {
        return route('patient.profile.show');
    }
    
    /**
     * Get the patient profile edit URL
     */
    public static function getPatientProfileEditUrl(): string
    {
        return route('patient.profile.edit');
    }
    
    /**
     * Get the admin dashboard URL
     */
    public static function getAdminDashboardUrl(): string
    {
        return route('dashboard.index');
    }
    
    /**
     * Get appointment details URL
     */
    public static function getAppointmentUrl(int $appointmentId): string
    {
        return route('appointments.show', $appointmentId);
    }
    
    /**
     * Get appointment booking URL
     */
    public static function getAppointmentBookingUrl(): string
    {
        return route('appointments.create');
    }
    
    /**
     * Get prescription details URL
     */
    public static function getPrescriptionUrl(int $prescriptionId): string
    {
        return route('prescriptions.show', $prescriptionId);
    }
    
    /**
     * Get password reset URL
     */
    public static function getPasswordResetUrl(): string
    {
        return route('password.request');
    }
    
    /**
     * Get contact/support URL
     */
    public static function getSupportUrl(): string
    {
        return self::getBaseUrl() . '/contact';
    }
    
    /**
     * Get FAQ URL
     */
    public static function getFaqUrl(): string
    {
        return self::getBaseUrl() . '/faq';
    }
    
    /**
     * Generate a patient-specific action URL
     */
    public static function getPatientActionUrl(string $action, array $parameters = []): string
    {
        $routes = [
            'appointments' => 'patient.appointments.index',
            'prescriptions' => 'patient.prescriptions.index',
            'medical-history' => 'patient.medical-history.index',
            'profile' => 'patient.profile.show',
            'edit-profile' => 'patient.profile.edit',
        ];
        
        if (!isset($routes[$action])) {
            return self::getPatientDashboardUrl();
        }
        
        return route($routes[$action], $parameters);
    }
    
    /**
     * Generate email tracking URLs with parameters
     */
    public static function getEmailTrackingUrl(string $type, array $parameters = []): string
    {
        $baseUrl = self::getBaseUrl();
        $trackingPath = '/email-tracking/' . $type;
        
        if (!empty($parameters)) {
            $trackingPath .= '?' . http_build_query($parameters);
        }
        
        return $baseUrl . $trackingPath;
    }
    
    /**
     * Get unsubscribe URL for email notifications
     */
    public static function getUnsubscribeUrl(string $email, string $token): string
    {
        return self::getBaseUrl() . '/unsubscribe?' . http_build_query([
            'email' => $email,
            'token' => $token,
        ]);
    }
    
    /**
     * Generate asset URLs that work with the current domain
     */
    public static function getAssetUrl(string $path): string
    {
        return asset($path);
    }
    
    /**
     * Generate logo URL for emails
     */
    public static function getLogoUrl(): string
    {
        return self::getAssetUrl('images/logo.png');
    }
    
    /**
     * Generate social media links (if applicable)
     */
    public static function getSocialMediaUrls(): array
    {
        return [
            'website' => self::getBaseUrl(),
            // Add social media links here if needed
            // 'facebook' => 'https://facebook.com/bokodcms',
            // 'twitter' => 'https://twitter.com/bokodcms',
        ];
    }
    
    /**
     * Check if the current environment supports secure URLs
     */
    public static function shouldUseHttps(): bool
    {
        return app()->environment('production') || request()->isSecure();
    }
    
    /**
     * Force HTTPS on generated URLs if appropriate
     */
    public static function forceSecureUrls(): void
    {
        if (self::shouldUseHttps()) {
            URL::forceScheme('https');
        }
    }
    
    /**
     * Generate a secure route with proper scheme
     */
    public static function secureRoute(string $routeName, array $parameters = []): string
    {
        $url = route($routeName, $parameters);
        
        if (self::shouldUseHttps() && !str_starts_with($url, 'https://')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
    
    /**
     * Get all important URLs for email footers
     */
    public static function getEmailFooterUrls(): array
    {
        return [
            'portal' => self::getPatientLoginUrl(),
            'support' => self::getSupportUrl(),
            'faq' => self::getFaqUrl(),
            'password_reset' => self::getPasswordResetUrl(),
            'base' => self::getBaseUrl(),
        ];
    }
}