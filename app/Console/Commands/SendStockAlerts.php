<?php

namespace App\Console\Commands;

use App\Models\Medicine;
use App\Services\EnhancedEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendStockAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:stock-alerts {--test : Run in test mode without sending emails} {--low-threshold=20 : Low stock threshold} {--critical-threshold=10 : Critical stock threshold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send stock alert emails to administrators for medicines with low or critical stock levels';

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
        $lowThreshold = (int) $this->option('low-threshold');
        $criticalThreshold = (int) $this->option('critical-threshold');
        
        $this->info('Checking stock levels and sending alerts...');
        if ($testMode) {
            $this->warn('Running in TEST MODE - no actual emails will be sent');
        }
        
        try {
            // Get medicines with low stock (between critical and low threshold)
            $lowStockMedicines = Medicine::where('current_stock', '>', $criticalThreshold)
                ->where('current_stock', '<=', $lowThreshold)
                ->get()
                ->map(function ($medicine) {
                    return [
                        'medicine_name' => $medicine->medicine_name,
                        'current_stock' => $medicine->current_stock,
                        'reorder_level' => $medicine->reorder_level ?? $lowThreshold,
                        'days_until_reorder' => $this->calculateDaysUntilReorder($medicine)
                    ];
                })
                ->toArray();
            
            // Get medicines with critical stock (at or below critical threshold)
            $criticalStockMedicines = Medicine::where('current_stock', '<=', $criticalThreshold)
                ->get()
                ->map(function ($medicine) use ($criticalThreshold) {
                    return [
                        'medicine_name' => $medicine->medicine_name,
                        'current_stock' => $medicine->current_stock,
                        'reorder_level' => $medicine->reorder_level ?? $criticalThreshold,
                        'status' => $medicine->current_stock == 0 ? 'OUT_OF_STOCK' : 'CRITICAL'
                    ];
                })
                ->toArray();
            
            // Determine alert type
            $alertType = 'low';
            if (count($criticalStockMedicines) > 0) {
                $hasOutOfStock = collect($criticalStockMedicines)->where('current_stock', 0)->count() > 0;
                $alertType = $hasOutOfStock ? 'out_of_stock' : 'critical';
            }
            
            // Only send if there are items to alert about
            if (count($lowStockMedicines) + count($criticalStockMedicines) == 0) {
                $this->info('No medicines require stock alerts at this time.');
                return Command::SUCCESS;
            }
            
            $result = $this->emailService->sendStockAlert(
                $lowStockMedicines, 
                $criticalStockMedicines, 
                $alertType, 
                $testMode
            );
            
            if ($result['success']) {
                $this->info($result['message']);
                
                $this->table(['Stock Level', 'Count'], [
                    ['Low Stock', count($lowStockMedicines)],
                    ['Critical Stock', count($criticalStockMedicines)],
                    ['Total Alerts', count($lowStockMedicines) + count($criticalStockMedicines)],
                ]);
                
                if (!empty($criticalStockMedicines)) {
                    $this->warn('Critical stock items require immediate attention!');
                    $this->table(['Medicine', 'Current Stock', 'Status'], array_map(function($item) {
                        return [
                            $item['medicine_name'],
                            $item['current_stock'],
                            $item['status'] ?? 'CRITICAL'
                        ];
                    }, array_slice($criticalStockMedicines, 0, 5))); // Show first 5
                }
                
                Log::info('Stock alerts command completed', $result);
                
                return Command::SUCCESS;
            } else {
                $this->error($result['message']);
                Log::error('Stock alerts command failed', $result);
                
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('Error sending stock alerts: ' . $e->getMessage());
            Log::error('Stock alerts command exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Calculate estimated days until reorder based on usage patterns
     */
    private function calculateDaysUntilReorder($medicine): string
    {
        // Simple estimation - you can make this more sophisticated based on usage history
        $reorderLevel = $medicine->reorder_level ?? 20;
        $currentStock = $medicine->current_stock;
        
        if ($currentStock <= $reorderLevel) {
            return 'Immediate';
        }
        
        // Estimate based on average daily usage (you'd need to track this)
        $estimatedDailyUsage = 1; // Default to 1 unit per day
        $daysRemaining = max(0, ($currentStock - $reorderLevel) / $estimatedDailyUsage);
        
        return round($daysRemaining) . ' days';
    }
}
