<?php

namespace App\Console\Commands;

use App\Services\EnhancedEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendHealthTips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:health-tips {--test : Run in test mode without sending emails} {--season= : Specify season (rainy/dry)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send seasonal health tips emails to all patients';

    protected $emailService;

    public function __construct(EnhancedEmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testMode = $this->option('test');
        $season = $this->option('season');
        
        $this->info('Sending health tips emails...');
        if ($testMode) {
            $this->warn('Running in TEST MODE - no actual emails will be sent');
        }
        
        // Default health tips array (empty to use template defaults)
        $healthTips = [];
        
        // Additional data for vaccination reminders based on season
        $additionalData = [];
        if ($season === 'rainy' || (!$season && $this->isRainySeason())) {
            $additionalData['vaccination_reminders'] = [
                'Flu vaccination',
                'Hepatitis A vaccination',
                'Typhoid vaccination'
            ];
        } else {
            $additionalData['vaccination_reminders'] = [
                'Travel vaccinations if planning trips',
                'Regular health check-ups'
            ];
        }
        
        try {
            $result = $this->emailService->sendHealthTips(null, $healthTips, $season, $additionalData, $testMode);
            
            if ($result['success']) {
                $this->info($result['message']);
                
                if (isset($result['sent_count'])) {
                    $this->table(['Metric', 'Count'], [
                        ['Sent', $result['sent_count']],
                        ['Failed', $result['failed_count'] ?? 0],
                    ]);
                }
                
                Log::info('Health tips command completed', $result);
                
                return Command::SUCCESS;
            } else {
                $this->error($result['message']);
                Log::error('Health tips command failed', $result);
                
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('Error sending health tips: ' . $e->getMessage());
            Log::error('Health tips command exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Check if it's currently rainy season in the Philippines
     */
    private function isRainySeason(): bool
    {
        $month = date('n'); // 1-12
        return ($month >= 6 && $month <= 11); // June to November
    }
}
