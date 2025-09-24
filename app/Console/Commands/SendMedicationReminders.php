<?php

namespace App\Console\Commands;

use App\Services\EnhancedEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendMedicationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:medication-reminders {--test : Run in test mode without sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send medication reminder emails to patients with active prescriptions';

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
        
        $this->info('Sending medication reminders...');
        if ($testMode) {
            $this->warn('Running in TEST MODE - no actual emails will be sent');
        }
        
        try {
            $result = $this->emailService->sendMedicationReminders($testMode);
            
            if ($result['success']) {
                $this->info($result['message']);
                
                if (isset($result['sent_count'])) {
                    $this->table(['Metric', 'Count'], [
                        ['Sent', $result['sent_count']],
                        ['Failed', $result['failed_count'] ?? 0],
                    ]);
                }
                
                Log::info('Medication reminders command completed', $result);
                
                return Command::SUCCESS;
            } else {
                $this->error($result['message']);
                Log::error('Medication reminders command failed', $result);
                
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('Error sending medication reminders: ' . $e->getMessage());
            Log::error('Medication reminders command exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}
