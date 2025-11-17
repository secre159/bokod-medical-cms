<?php

namespace App\Jobs;

use App\Models\Patient;
use App\Services\EnhancedEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPatientWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $patientId;
    public $password;

    /**
     * Create a new job instance.
     */
    public function __construct(int $patientId, string $password)
    {
        $this->patientId = $patientId;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(EnhancedEmailService $emailService): void
    {
        try {
            $patient = Patient::find($this->patientId);
            
            if (!$patient) {
                Log::error('Patient not found for welcome email', ['patient_id' => $this->patientId]);
                return;
            }

            Log::info('Starting welcome email job', [
                'patient_id' => $this->patientId,
                'patient_email' => $patient->email
            ]);

            $result = $emailService->sendPatientWelcome($patient, $this->password);

            Log::info('Welcome email job completed', [
                'patient_id' => $this->patientId,
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'No message'
            ]);

        } catch (\Throwable $e) {
            Log::error('Welcome email job failed', [
                'patient_id' => $this->patientId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'password' => $this->password
            ]);
        }
    }
}
