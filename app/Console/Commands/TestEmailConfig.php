<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEmailConfig extends Command
{
    protected $signature = 'test:email-config';
    protected $description = 'Test email configuration without database';

    public function handle()
    {
        $this->info('Testing email configuration...');
        
        // Check email config
        $this->info("MAIL_MAILER: " . (config('mail.default') ?? 'NULL'));
        $this->info("MAIL_FROM_ADDRESS: " . (config('mail.from.address') ?? 'NULL'));
        $this->info("MAIL_FROM_NAME: " . (config('mail.from.name') ?? 'NULL'));
        
        // Test if FROM values are null
        if (empty(config('mail.from.address'))) {
            $this->error('❌ MAIL_FROM_ADDRESS is empty or null!');
        } else {
            $this->info('✓ MAIL_FROM_ADDRESS is set: ' . config('mail.from.address'));
        }
        
        if (empty(config('mail.from.name'))) {
            $this->error('❌ MAIL_FROM_NAME is empty or null!');
        } else {
            $this->info('✓ MAIL_FROM_NAME is set: ' . config('mail.from.name'));
        }
        
        $this->info("\nEnvironment values:");
        $this->info("APP_NAME: " . (env('APP_NAME') ?? 'NULL'));
        $this->info("MAIL_FROM_ADDRESS: " . (env('MAIL_FROM_ADDRESS') ?? 'NULL'));
        $this->info("MAIL_FROM_NAME: " . (env('MAIL_FROM_NAME') ?? 'NULL'));
        
        return 0;
    }
}