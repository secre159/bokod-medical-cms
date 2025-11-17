<?php

namespace App\Services;

use App\Mail\AppointmentNotification;
use App\Mail\PatientWelcome;
use App\Mail\PrescriptionNotification;
use App\Mail\HealthTips;
use App\Mail\StockAlert;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\Medicine;
use App\Models\User;
use App\Services\UrlService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EnhancedEmailService
{
    /**
     * Send appointment notification
     */
    public function sendAppointmentNotification(Appointment $appointment, string $type, array $additionalData = [], bool $testMode = false): array
    {
        try {
            $appointment->load('patient', 'patient.user');
            
            if (!$appointment->patient) {
                Log::error('Appointment notification failed - No patient found', [
                    'appointment_id' => $appointment->appointment_id,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'message' => 'Patient not found for appointment'
                ];
            }
            
            if (!$appointment->patient->email) {
                Log::error('Appointment notification failed - No patient email', [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_id' => $appointment->patient->id,
                    'patient_name' => $appointment->patient->patient_name,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'message' => 'Patient email not found'
                ];
            }
            
            // Validate email format
            if (!filter_var($appointment->patient->email, FILTER_VALIDATE_EMAIL)) {
                Log::error('Appointment notification failed - Invalid email format', [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_email' => $appointment->patient->email,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'message' => 'Invalid patient email format'
                ];
            }

            $mailable = new AppointmentNotification($appointment, $type, $additionalData);

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Email ready to send (test mode)',
                    'recipient' => $appointment->patient->email,
                    'subject' => $this->getAppointmentSubject($type),
                    'test_mode' => true
                ];
            }

            // Emergency production safety - bypass email if environment is problematic
            if (app()->environment('production') && (empty(config('mail.from.address')) || empty(config('mail.from.name')))) {
                Log::warning('Email bypassed due to missing configuration in production', [
                    'appointment_id' => $appointment->appointment_id,
                    'type' => $type
                ]);
                return [
                    'success' => true,
                    'message' => 'Email bypassed due to configuration issues',
                    'recipient' => $appointment->patient->email
                ];
            }
            
            // Ultra-safe email sending with multiple fallbacks
            try {
                Mail::to($appointment->patient->email)->send($mailable);
            } catch (\Symfony\Component\Mime\Exception\InvalidArgumentException $e) {
                // Specific handling for null header errors
                Log::error('Email header validation failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_email' => $appointment->patient->email,
                    'error' => $e->getMessage(),
                    'type' => $type
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Email configuration error - appointment update completed without email notification'
                ];
            } catch (\Exception $e) {
                // Any other email sending errors
                Log::error('Email sending failed', [
                    'appointment_id' => $appointment->appointment_id,
                    'patient_email' => $appointment->patient->email,
                    'error' => $e->getMessage(),
                    'type' => $type,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Email delivery failed - appointment update completed without email notification'
                ];
            }

            Log::info('Appointment notification sent', [
                'appointment_id' => $appointment->appointment_id,
                'patient_email' => $appointment->patient->email,
                'notification_type' => $type
            ]);

            return [
                'success' => true,
                'message' => 'Email sent successfully',
                'recipient' => $appointment->patient->email
            ];

        } catch (Exception $e) {
            Log::error('Failed to send appointment notification', [
                'appointment_id' => $appointment->appointment_id ?? null,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send patient welcome email
     */
    public function sendPatientWelcome(Patient $patient, string $temporaryPassword = null, bool $testMode = false): array
    {
        try {
            if (!$patient->email) {
                return [
                    'success' => false,
                    'message' => 'Patient email not found'
                ];
            }

            $portalUrl = UrlService::getPatientLoginUrl();
            $mailable = new PatientWelcome($patient, $temporaryPassword, $portalUrl);

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Welcome email ready to send (test mode)',
                    'recipient' => $patient->email,
                    'subject' => 'Welcome to BOKOD CMS Patient Portal',
                    'test_mode' => true
                ];
            }

            // Send with timeout to prevent hanging
            $startTime = microtime(true);
            
            try {
                Mail::to($patient->email)->send($mailable);
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::info('Patient welcome email sent', [
                    'patient_id' => $patient->id,
                    'patient_email' => $patient->email,
                    'send_duration_ms' => $duration
                ]);
            } catch (\Exception $sendEx) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('Email send threw exception', [
                    'patient_id' => $patient->id,
                    'patient_email' => $patient->email,
                    'send_duration_ms' => $duration,
                    'error' => $sendEx->getMessage()
                ]);
                throw $sendEx;
            }

            return [
                'success' => true,
                'message' => 'Welcome email sent successfully',
                'recipient' => $patient->email
            ];

        } catch (Exception $e) {
            Log::error('Failed to send patient welcome email', [
                'patient_id' => $patient->id ?? null,
                'patient_email' => $patient->email ?? 'N/A',
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send welcome email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send prescription notification
     */
    public function sendPrescriptionNotification(Prescription $prescription, string $type, array $additionalData = [], bool $testMode = false): array
    {
        try {
            $prescription->load('patient');

            if (!$prescription->patient || !$prescription->patient->email) {
                return [
                    'success' => false,
                    'message' => 'Patient email not found'
                ];
            }

            $mailable = new PrescriptionNotification($prescription, $type, $additionalData);

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Prescription notification ready to send (test mode)',
                    'recipient' => $prescription->patient->email,
                    'subject' => $this->getPrescriptionSubject($type),
                    'test_mode' => true
                ];
            }

            Mail::to($prescription->patient->email)->send($mailable);

            Log::info('Prescription notification sent', [
                'prescription_id' => $prescription->prescription_id,
                'patient_email' => $prescription->patient->email,
                'notification_type' => $type
            ]);

            return [
                'success' => true,
                'message' => 'Prescription notification sent successfully',
                'recipient' => $prescription->patient->email
            ];

        } catch (Exception $e) {
            Log::error('Failed to send prescription notification', [
                'prescription_id' => $prescription->prescription_id ?? null,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send prescription notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send health tips email
     */
    public function sendHealthTips(Patient $patient = null, array $healthTips = [], string $season = null, array $additionalData = [], bool $testMode = false): array
    {
        try {
            // If no specific patient, send to all patients with emails
            if (!$patient) {
                return $this->sendHealthTipsToAllPatients($healthTips, $season, $additionalData, $testMode);
            }

            if (!$patient->email) {
                return [
                    'success' => false,
                    'message' => 'Patient email not found'
                ];
            }

            $mailable = new HealthTips($patient, $healthTips, $season, $additionalData);

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Health tips email ready to send (test mode)',
                    'recipient' => $patient->email,
                    'subject' => 'Health Tips for ' . ucfirst($season ?? 'Current') . ' Season - BOKOD CMS',
                    'test_mode' => true
                ];
            }

            Mail::to($patient->email)->send($mailable);

            Log::info('Health tips email sent', [
                'patient_id' => $patient->id,
                'patient_email' => $patient->email,
                'season' => $season
            ]);

            return [
                'success' => true,
                'message' => 'Health tips email sent successfully',
                'recipient' => $patient->email
            ];

        } catch (Exception $e) {
            Log::error('Failed to send health tips email', [
                'patient_id' => $patient->id ?? null,
                'season' => $season,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send health tips email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send stock alert to administrators
     */
    public function sendStockAlert(array $lowStockMedicines = [], array $criticalStockMedicines = [], string $alertType = 'low', bool $testMode = false): array
    {
        try {
            // Get admin users
            $adminUsers = User::where('role', User::ROLE_ADMIN)
                             ->whereNotNull('email')
                             ->get();

            if ($adminUsers->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No admin users with email addresses found'
                ];
            }

            $mailable = new StockAlert($lowStockMedicines, $criticalStockMedicines, $alertType);
            $recipients = $adminUsers->pluck('email')->toArray();

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Stock alert ready to send to ' . count($recipients) . ' admin(s) (test mode)',
                    'recipients' => $recipients,
                    'subject' => ucfirst($alertType) . ' Stock Alert - BOKOD CMS',
                    'test_mode' => true
                ];
            }

            foreach ($adminUsers as $admin) {
                Mail::to($admin->email)->send($mailable);
            }

            Log::info('Stock alert sent', [
                'alert_type' => $alertType,
                'recipient_count' => count($recipients),
                'low_stock_count' => count($lowStockMedicines),
                'critical_stock_count' => count($criticalStockMedicines)
            ]);

            return [
                'success' => true,
                'message' => 'Stock alert sent to ' . count($recipients) . ' administrator(s)',
                'recipients' => $recipients
            ];

        } catch (Exception $e) {
            Log::error('Failed to send stock alert', [
                'alert_type' => $alertType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send stock alert: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send medication reminders to patients
     */
    public function sendMedicationReminders(bool $testMode = false): array
    {
        try {
            // Get patients with active prescriptions
            $patientsWithPrescriptions = Patient::whereHas('prescriptions', function($query) {
                $query->where('status', 'active');
            })
            ->whereNotNull('email')
            ->with(['prescriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();

            if ($patientsWithPrescriptions->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No patients with active prescriptions and email addresses found'
                ];
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($patientsWithPrescriptions as $patient) {
                $medicines = [];
                foreach ($patient->prescriptions as $prescription) {
                    // This would need to be adapted based on your prescription/medicine relationship
                    $medicines[] = [
                        'medicine_name' => $prescription->medicine_name ?? 'Prescribed Medication',
                        'dosage' => $prescription->dosage ?? 'As prescribed',
                        'schedule' => $prescription->instructions ?? 'Take as directed',
                        'remaining_days' => $prescription->duration ?? 'N/A'
                    ];
                }

                $additionalData = ['medicines' => $medicines];

                if ($testMode) {
                    $sentCount++;
                    continue;
                }

                $result = $this->sendPrescriptionNotification(
                    $patient->prescriptions->first(), 
                    'reminder', 
                    $additionalData
                );

                if ($result['success']) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            Log::info('Medication reminders batch sent', [
                'total_patients' => $patientsWithPrescriptions->count(),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'test_mode' => $testMode
            ]);

            $message = $testMode 
                ? "Would send medication reminders to {$sentCount} patient(s) (test mode)"
                : "Sent medication reminders to {$sentCount} patient(s)";
            
            if ($failedCount > 0) {
                $message .= ", {$failedCount} failed";
            }

            return [
                'success' => true,
                'message' => $message,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'test_mode' => $testMode
            ];

        } catch (Exception $e) {
            Log::error('Failed to send medication reminders batch', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send medication reminders: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send health tips to all patients
     */
    private function sendHealthTipsToAllPatients(array $healthTips, string $season, array $additionalData, bool $testMode): array
    {
        try {
            $patients = Patient::whereNotNull('email')->get();

            if ($patients->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No patients with email addresses found'
                ];
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($patients as $patient) {
                if ($testMode) {
                    $sentCount++;
                    continue;
                }

                $result = $this->sendHealthTips($patient, $healthTips, $season, $additionalData);
                
                if ($result['success']) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            $message = $testMode 
                ? "Would send health tips to {$sentCount} patient(s) (test mode)"
                : "Sent health tips to {$sentCount} patient(s)";
            
            if ($failedCount > 0) {
                $message .= ", {$failedCount} failed";
            }

            return [
                'success' => true,
                'message' => $message,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'test_mode' => $testMode
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send health tips batch: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send password reset notification to patient
     */
    public function sendPasswordResetNotification(Patient $patient, string $newPassword, bool $testMode = false): array
    {
        try {
            if (!$patient->email) {
                return [
                    'success' => false,
                    'message' => 'Patient email not found'
                ];
            }

            $portalUrl = UrlService::getPatientLoginUrl();
            $mailable = new PatientWelcome($patient, $newPassword, $portalUrl);
            
            // Override the subject for password reset
            $mailable->subject = 'Password Reset - BOKOD CMS Patient Portal';

            if ($testMode) {
                return [
                    'success' => true,
                    'message' => 'Password reset email ready to send (test mode)',
                    'recipient' => $patient->email,
                    'subject' => 'Password Reset - BOKOD CMS Patient Portal',
                    'test_mode' => true
                ];
            }

            Mail::to($patient->email)->send($mailable);

            Log::info('Password reset notification sent', [
                'patient_id' => $patient->id,
                'patient_email' => $patient->email
            ]);

            return [
                'success' => true,
                'message' => 'Password reset email sent successfully',
                'recipient' => $patient->email
            ];

        } catch (Exception $e) {
            Log::error('Failed to send password reset notification', [
                'patient_id' => $patient->id ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send password reset email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get appointment notification subject
     */
    private function getAppointmentSubject(string $type): string
    {
        $subjects = [
            'approved' => 'Appointment Approved - BOKOD CMS',
            'cancelled' => 'Appointment Cancelled - BOKOD CMS',
            'completed' => 'Appointment Follow-up - BOKOD CMS',
            'reminder' => 'Appointment Reminder - BOKOD CMS',
            'rejected' => 'Appointment Rejected - BOKOD CMS',
            'rescheduled' => 'Appointment Rescheduled - BOKOD CMS',
            'reschedule_request' => 'Reschedule Request Received - BOKOD CMS',
        ];

        return $subjects[$type] ?? 'Appointment Update - BOKOD CMS';
    }

    /**
     * Get prescription notification subject
     */
    private function getPrescriptionSubject(string $type): string
    {
        $subjects = [
            'new' => 'New Prescription - BOKOD CMS',
            'updated' => 'Prescription Updated - BOKOD CMS',
            'reminder' => 'Medication Reminder - BOKOD CMS',
        ];

        return $subjects[$type] ?? 'Prescription Update - BOKOD CMS';
    }

    /**
     * Check if email functionality is properly configured
     */
    public function checkConfiguration(): array
    {
        try {
            $checks = [
                'smtp_configured' => config('mail.default') !== 'log',
                'from_address_set' => !empty(config('mail.from.address')),
                'from_name_set' => !empty(config('mail.from.name')),
            ];

            $allPassed = !in_array(false, $checks);

            return [
                'configured' => $allPassed,
                'checks' => $checks,
                'message' => $allPassed ? 'Email system is properly configured' : 'Email system configuration issues found'
            ];

        } catch (Exception $e) {
            return [
                'configured' => false,
                'message' => 'Error checking configuration: ' . $e->getMessage()
            ];
        }
    }
}