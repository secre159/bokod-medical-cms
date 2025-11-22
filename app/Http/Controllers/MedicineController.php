<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Services\ImgBBService;

class MedicineController extends Controller
{
    /**
     * Display a listing of medicines with filtering and search
     */
    public function index(Request $request)
    {
        $query = Medicine::query();
        
        // Filter by status - show all by default
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by stock status
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('medicine_name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }
        
        $medicines = $query->orderBy('medicine_name')
                          ->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => Medicine::count(), // Show all medicines, not just active
            'active' => Medicine::active()->count(),
            'low_stock' => Medicine::active()->lowStock()->count(),
            'expired' => Medicine::expired()->count(),
            'expiring_soon' => Medicine::active()->expiringSoon()->count(),
        ];
        
        // Get categories for filter dropdown
        $categories = Medicine::getCategoriesList();
        
        return view('medicines.index', compact('medicines', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new medicine
     */
    public function create()
    {
        $categories = Medicine::getCategoriesList();
        $dosageForms = Medicine::getDosageFormsList();
        $therapeuticClasses = Medicine::getTherapeuticClassesList();
        $pregnancyCategories = Medicine::getPregnancyCategoriesList();
        
        return view('medicines.create', compact('categories', 'dosageForms', 'therapeuticClasses', 'pregnancyCategories'));
    }

    /**
     * Store a newly created medicine
     */
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'medicine_name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'therapeutic_class' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'indication' => 'nullable|string',
            'dosage_form' => 'required|string|max:255',
            'strength' => 'required|string|max:255',
            'dosage_instructions' => 'nullable|string',
            'age_restrictions' => 'nullable|string|max:255',
            'unit_measure' => 'nullable|string|max:255', // Changed from 'unit' to 'unit_measure' and made nullable
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'balance_per_card' => 'nullable|integer|min:0',
            'on_hand_per_count' => 'nullable|integer|min:0',
            'shortage_overage' => 'nullable|integer',
            'inventory_remarks' => 'nullable|string|max:500',
            'supplier' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'manufacturing_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'storage_conditions' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'drug_interactions' => 'nullable|string',
            'pregnancy_category' => 'nullable|string|max:10',
            'warnings' => 'nullable|string',
            'requires_prescription' => 'boolean',
            'notes' => 'nullable|string',
            'medicine_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ];
        
        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        try {
            // Handle image upload if provided
            if ($request->hasFile('medicine_image') && $request->file('medicine_image')->isValid()) {
                $imgBBService = new ImgBBService();
                $imageName = 'medicine_' . time() . '_' . uniqid();
                $uploadResult = $imgBBService->uploadImage($request->file('medicine_image'), $imageName);
                
                if ($uploadResult['success']) {
                    $validated['medicine_image'] = $uploadResult['url'];
                } else {
                    throw new \Exception('Failed to upload image: ' . $uploadResult['error']);
                }
            }
            
            // Set status based on expiry date
            if (isset($validated['expiry_date'])) {
                $expiryDate = Carbon::parse($validated['expiry_date']);
                if ($expiryDate->isPast()) {
                    $validated['status'] = Medicine::STATUS_EXPIRED;
                } else {
                    $validated['status'] = Medicine::STATUS_ACTIVE;
                }
            } else {
                $validated['status'] = Medicine::STATUS_ACTIVE;
            }
            
            $medicine = Medicine::create($validated);
            
            $successMessage = "Medicine '{$validated['medicine_name']}' added successfully!";
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'medicine' => $medicine
                ]);
            }
            
            return redirect()->route('medicines.index')
                           ->with('success', $successMessage);
                           
        } catch (\Exception $e) {
            $errorMessage = 'Failed to add medicine: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->withErrors(['error' => $errorMessage])
                        ->withInput();
        }
    }

    /**
     * Display the specified medicine
     */
    public function show(Medicine $medicine)
    {
        $medicine->load('prescriptions.patient');
        
        return view('medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the medicine
     */
    public function edit(Medicine $medicine)
    {
        $categories = Medicine::getCategories();
        $dosageForms = Medicine::getDosageForms();
        $therapeuticClasses = Medicine::getTherapeuticClasses();
        $pregnancyCategories = Medicine::getPregnancyCategories();
        
        return view('medicines.edit', compact('medicine', 'categories', 'dosageForms', 'therapeuticClasses', 'pregnancyCategories'));
    }

    /**
     * Update the specified medicine
     */
    public function update(Request $request, Medicine $medicine)
    {
        $rules = [
            'medicine_name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'therapeutic_class' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'indication' => 'nullable|string',
            'dosage_form' => 'required|string|max:255',
            'strength' => 'required|string|max:255',
            'dosage_instructions' => 'nullable|string',
            'age_restrictions' => 'nullable|string|max:255',
            'unit_measure' => 'nullable|string|max:255', // Changed from 'unit' to 'unit_measure' and made nullable
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'balance_per_card' => 'nullable|integer|min:0',
            'on_hand_per_count' => 'nullable|integer|min:0',
            'shortage_overage' => 'nullable|integer',
            'inventory_remarks' => 'nullable|string|max:500',
            'supplier' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:255',
            'manufacturing_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:manufacturing_date',
            'storage_conditions' => 'nullable|string|max:255',
            'side_effects' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'drug_interactions' => 'nullable|string',
            'pregnancy_category' => 'nullable|string|max:10',
            'warnings' => 'nullable|string',
            'requires_prescription' => 'boolean',
            'status' => ['required', Rule::in([Medicine::STATUS_ACTIVE, Medicine::STATUS_INACTIVE, Medicine::STATUS_EXPIRED, Medicine::STATUS_DISCONTINUED])],
            'notes' => 'nullable|string',
            'medicine_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
        ];
        
        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        try {
            // Handle image upload if provided
            if ($request->hasFile('medicine_image') && $request->file('medicine_image')->isValid()) {
                $imgBBService = new ImgBBService();
                $imageName = 'medicine_' . $medicine->id . '_' . time() . '_' . uniqid();
                $uploadResult = $imgBBService->uploadImage($request->file('medicine_image'), $imageName);
                
                if ($uploadResult['success']) {
                    $validated['medicine_image'] = $uploadResult['url'];
                } else {
                    throw new \Exception('Failed to upload image: ' . $uploadResult['error']);
                }
            }
            
            // Auto-update status if expired
            if (isset($validated['expiry_date'])) {
                $expiryDate = Carbon::parse($validated['expiry_date']);
                if ($expiryDate->isPast() && $validated['status'] === Medicine::STATUS_ACTIVE) {
                    $validated['status'] = Medicine::STATUS_EXPIRED;
                }
            }
            
            $medicine->update($validated);
            
            $successMessage = "Medicine '{$validated['medicine_name']}' updated successfully!";
            
            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'medicine' => $medicine->fresh()
                ]);
            }
            
            return redirect()->route('medicines.index')
                           ->with('success', $successMessage);
                           
        } catch (\Exception $e) {
            $errorMessage = 'Failed to update medicine: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->withErrors(['error' => $errorMessage])
                        ->withInput();
        }
    }

    /**
     * Remove the specified medicine (soft delete by changing status)
     */
    public function destroy(Medicine $medicine)
    {
        try {
            // Check if medicine has active prescriptions
            $activePrescriptions = $medicine->prescriptions()->where('status', 'active')->count();
            
            if ($activePrescriptions > 0) {
                $errorMessage = 'Cannot delete medicine with active prescriptions. Please complete or cancel active prescriptions first.';
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                
                return back()->withErrors(['error' => $errorMessage]);
            }
            
            $medicine->update(['status' => Medicine::STATUS_DISCONTINUED]);
            
            $successMessage = "Medicine '{$medicine->medicine_name}' discontinued successfully!";
            
            // Handle AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }
            
            return redirect()->route('medicines.index')
                           ->with('success', $successMessage);
                           
        } catch (\Exception $e) {
            $errorMessage = 'Failed to discontinue medicine: ' . $e->getMessage();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return back()->withErrors(['error' => $errorMessage]);
        }
    }
    
    /**
     * Stock management page
     */
    public function stock()
    {
        $medicines = Medicine::active()
                           ->orderBy('stock_quantity')
                           ->paginate(20);
        
        $lowStockCount = Medicine::active()->lowStock()->count();
        $outOfStockCount = Medicine::active()->where('stock_quantity', '<=', 0)->count();
        
        return view('medicines.stock', compact('medicines', 'lowStockCount', 'outOfStockCount'));
    }
    
    /**
     * Update stock quantity with comprehensive operations
     */
    public function updateStock(Request $request, Medicine $medicine)
    {
        $validationRules = [
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'reason' => 'nullable|string|max:500',
        ];
        
        // Require either action or operation
        if ($request->has('action')) {
            $validationRules['action'] = 'required|in:add,subtract';
        } elseif ($request->has('operation')) {
            $validationRules['operation'] = 'required|in:add,subtract';
        } else {
            $validationRules['action'] = 'required|in:add,subtract';
        }
        
        try {
            $request->validate($validationRules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $oldQuantity = $medicine->stock_quantity;
            $newQuantity = $oldQuantity;
            $operation = $request->operation ?? $request->action;
            
            // Debug information
            $debugInfo = [
                'medicine_id' => $medicine->id,
                'operation' => $operation,
                'quantity' => $request->quantity,
                'old_quantity' => $oldQuantity,
                'request_all' => $request->all()
            ];
            
            switch ($operation) {
                case 'add':
                    $newQuantity = $oldQuantity + $request->quantity;
                    break;
                case 'subtract':
                    if ($request->quantity > $oldQuantity) {
                        throw new \Exception('Cannot subtract more stock than available. Current stock: ' . $oldQuantity);
                    }
                    $newQuantity = $oldQuantity - $request->quantity;
                    break;
            }
            
            $medicine->update(['stock_quantity' => $newQuantity]);
            
            // Log stock movement
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => $operation,
                'quantity_changed' => $request->quantity,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'reason' => $request->reason ?? $request->notes ?? 'Manual stock update',
                'notes' => $request->notes,
                'reference_type' => 'manual',
                'reference_id' => null
            ]);
            
            DB::commit();
            
            $actionText = $operation === 'add' ? 'Added' : 'Removed';
            $message = "{$actionText} {$request->quantity} units. Stock updated from {$oldQuantity} to {$newQuantity}.";
            
            // Always return JSON response for AJAX requests
            return response()->json([
                'success' => true,
                'message' => $message,
                'new_quantity' => $newQuantity,
                'stock_status' => $medicine->refresh()->stock_status,
                'stock_status_color' => $medicine->refresh()->stock_status_color,
                'debug' => $debugInfo ?? null
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $errorMessage = 'Failed to update stock: ' . $e->getMessage();
            
            // Always return JSON response for consistency
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 400);
        }
    }
    
    /**
     * Search medicines for prescriptions (AJAX)
     */
    public function searchForPrescription(Request $request)
    {
        $search = $request->get('q');
        
        $medicines = Medicine::active()
                           ->where('stock_quantity', '>', 0)
                           ->where(function($query) use ($search) {
                               $query->where('medicine_name', 'like', "%{$search}%")
                                     ->orWhere('generic_name', 'like', "%{$search}%")
                                     ->orWhere('brand_name', 'like', "%{$search}%");
                           })
                           ->limit(10)
                           ->get(['id', 'medicine_name', 'generic_name', 'brand_name', 'strength', 'dosage_form', 'stock_quantity']);
        
        return response()->json($medicines);
    }
    
    /**
     * Get low stock medicines for dashboard alerts
     */
    public function getLowStockAlert()
    {
        try {
            $lowStockMedicines = Medicine::active()
                ->where(function($query) {
                    $query->whereRaw('stock_quantity <= minimum_stock')
                          ->orWhere('stock_quantity', '<=', 10); // Default minimum if not set
                })
                ->orderBy('stock_quantity', 'asc')
                ->limit(10)
                ->get(['id', 'medicine_name', 'stock_quantity', 'minimum_stock']);
            
            return response()->json([
                'success' => true,
                'medicines' => $lowStockMedicines,
                'count' => $lowStockMedicines->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch low stock alerts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get medicine statistics for reports
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_medicines' => Medicine::count(),
                'active_medicines' => Medicine::where('status', 'active')->count(),
                'low_stock_count' => Medicine::active()
                    ->where(function($query) {
                        $query->whereRaw('stock_quantity <= minimum_stock')
                              ->orWhere('stock_quantity', '<=', 10);
                    })->count(),
                'out_of_stock_count' => Medicine::active()->where('stock_quantity', '<=', 0)->count(),
                'expired_count' => Medicine::where('expiry_date', '<', now())->count(),
                'expiring_soon_count' => Medicine::active()
                    ->whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
                'average_stock_level' => Medicine::active()
                    ->selectRaw('AVG(stock_quantity) as avg_stock')
                    ->value('avg_stock') ?: 0,
                'categories' => Medicine::active()
                    ->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(Request $request)
    {
        try {
            \Log::info('Bulk stock update request received', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            // Validate the request
            $validated = $request->validate([
                'updates' => 'required|array',
                'updates.*.medicine_id' => 'required|exists:medicines,id',
                'updates.*.action' => 'required|in:add,remove,set',
                'updates.*.quantity' => 'required|integer|min:0',
                'reason' => 'nullable|string|max:500'
            ]);
            
            \Log::info('Bulk stock update validation passed', [
                'updates_count' => count($validated['updates'])
            ]);
            
        } catch (ValidationException $e) {
            \Log::error('Bulk stock update validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            $updatedCount = 0;
            $errors = [];
            
            foreach ($validated['updates'] as $update) {
                try {
                    $medicine = Medicine::findOrFail($update['medicine_id']);
                    $oldQuantity = $medicine->stock_quantity;
                    $newQuantity = $oldQuantity;
                    
                    switch ($update['action']) {
                        case 'add':
                            $newQuantity = $oldQuantity + $update['quantity'];
                            break;
                        case 'remove':
                            if ($update['quantity'] > $oldQuantity) {
                                throw new \Exception("Cannot remove {$update['quantity']} from {$medicine->medicine_name} (only {$oldQuantity} available)");
                            }
                            $newQuantity = $oldQuantity - $update['quantity'];
                            break;
                        case 'set':
                            $newQuantity = $update['quantity'];
                            break;
                    }
                    
                    $medicine->update(['stock_quantity' => $newQuantity]);
                    
                    // Log stock movement
                    StockMovement::create([
                        'medicine_id' => $medicine->id,
                        'user_id' => auth()->id(),
                        'type' => $update['action'] === 'add' ? 'bulk_add' : ($update['action'] === 'remove' ? 'bulk_subtract' : 'adjust'),
                        'quantity_changed' => $update['quantity'],
                        'quantity_before' => $oldQuantity,
                        'quantity_after' => $newQuantity,
                        'reason' => $request->reason ?? 'Bulk stock update',
                        'notes' => null,
                        'reference_type' => 'bulk',
                        'reference_id' => null
                    ]);
                    
                    $updatedCount++;
                    
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            
            if (empty($errors)) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Successfully updated stock for {$updatedCount} medicine(s)."
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'errors' => $errors,
                    'message' => 'Some updates failed. No changes were made.'
                ], 400);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get stock status text
     */
    private function getStockStatus(Medicine $medicine)
    {
        if ($medicine->stock_quantity <= 0) {
            return 'Out of Stock';
        } elseif ($medicine->stock_quantity <= $medicine->minimum_stock) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }
    
    /**
     * Get stock status color class
     */
    private function getStockStatusColor(Medicine $medicine)
    {
        if ($medicine->stock_quantity <= 0) {
            return 'danger';
        } elseif ($medicine->stock_quantity <= $medicine->minimum_stock) {
            return 'warning';
        } else {
            return 'success';
        }
    }
    
    /**
     * Export stock report
     */
    public function exportStockReport(Request $request)
    {
        try {
            $format = $request->input('format', 'csv');
            $medicines = Medicine::active()
                ->orderBy('stock_quantity', 'asc')
                ->get();
            
            $filename = 'stock_report_' . now()->format('Y_m_d_H_i_s') . '.' . $format;
            
            if ($format === 'pdf') {
                // PDF export implementation would go here
                return $this->exportStockPDF($medicines, $filename);
            } else {
                // CSV export
                return $this->exportStockCSV($medicines, $filename);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export stock data as CSV
     */
    private function exportStockCSV($medicines, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($medicines) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Medicine Name',
                'Brand Name', 
                'Category',
                'Therapeutic Class',
                'Current Stock',
                'Minimum Stock',
                'Unit',
                'Status',
                'Expiry Date',
                'Supplier'
            ]);
            
            // Add data rows
            foreach ($medicines as $medicine) {
                fputcsv($file, [
                    $medicine->medicine_name,
                    $medicine->brand_name ?? '',
                    $medicine->category,
                    $medicine->therapeutic_class ?? '',
                    $medicine->stock_quantity,
                    $medicine->minimum_stock ?? 10,
                    $medicine->unit ?? 'pcs',
                    $this->getStockStatus($medicine),
                    $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '',
                    $medicine->supplier ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get stock history for a medicine
     */
    public function getStockHistory(Medicine $medicine)
    {
        try {
            $movements = StockMovement::where('medicine_id', $medicine->id)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->take(50) // Limit to last 50 movements
                ->get();
            
            $history = $movements->map(function($movement) {
                return [
                    'id' => $movement->id,
                    'date' => $movement->created_at->format('M d, Y H:i'),
                    'type' => $movement->type,
                    'formatted_type' => $movement->formatted_type,
                    'icon_class' => $movement->icon_class,
                    'quantity_changed' => $movement->quantity_changed,
                    'quantity_before' => $movement->quantity_before,
                    'quantity_after' => $movement->quantity_after,
                    'reason' => $movement->reason,
                    'notes' => $movement->notes,
                    'user' => $movement->user ? $movement->user->name : 'System',
                    'reference_type' => $movement->reference_type,
                    'created_at_human' => $movement->created_at->diffForHumans(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'medicine' => [
                    'id' => $medicine->id,
                    'name' => $medicine->medicine_name,
                    'current_stock' => $medicine->stock_quantity,
                    'unit' => $medicine->unit ?? 'pcs',
                    'minimum_stock' => $medicine->minimum_stock ?? 10
                ],
                'history' => $history,
                'total_movements' => $movements->count(),
                'has_more' => $movements->count() === 50
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update physical count for inventory management
     */
    public function updatePhysicalCount(Request $request, Medicine $medicine)
    {
        $request->validate([
            'on_hand_per_count' => 'required|integer|min:0',
            'inventory_remarks' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            $oldCount = $medicine->on_hand_per_count ?? 0;
            $newCount = $request->on_hand_per_count;
            $balancePerCard = $medicine->balance_per_card ?? $medicine->stock_quantity;
            
            // Calculate shortage/overage
            $shortageOverage = $newCount - $balancePerCard;
            
            $medicine->update([
                'on_hand_per_count' => $newCount,
                'shortage_overage' => $shortageOverage,
                'inventory_remarks' => $request->inventory_remarks,
            ]);
            
            // Log the physical count update
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => 'physical_count',
                'quantity_changed' => 0, // Physical count doesn't change actual stock
                'quantity_before' => $oldCount,
                'quantity_after' => $newCount,
                'reason' => 'Physical inventory count',
                'notes' => $request->inventory_remarks,
                'reference_type' => 'inventory',
                'reference_id' => null
            ]);
            
            DB::commit();
            
            $shortageOverageText = '';
            if ($shortageOverage > 0) {
                $shortageOverageText = "+{$shortageOverage} (Overage)";
            } elseif ($shortageOverage < 0) {
                $shortageOverageText = "{$shortageOverage} (Shortage)";
            } else {
                $shortageOverageText = "0 (Balanced)";
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Physical count updated successfully.',
                'data' => [
                    'on_hand_per_count' => $newCount,
                    'shortage_overage' => $shortageOverage,
                    'shortage_overage_text' => $shortageOverageText,
                    'balance_per_card' => $balancePerCard,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update physical count: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Adjust stock based on physical count discrepancy
     */
    public function adjustStockFromCount(Request $request, Medicine $medicine)
    {
        $request->validate([
            'adjustment_reason' => 'required|string|max:500',
            'confirm_adjustment' => 'required|boolean|accepted',
        ]);
        
        try {
            DB::beginTransaction();
            
            $physicalCount = $medicine->on_hand_per_count;
            $currentStock = $medicine->stock_quantity;
            $shortageOverage = $medicine->shortage_overage ?? 0;
            
            if ($shortageOverage == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No adjustment needed. Stock is balanced.'
                ], 400);
            }
            
            // Update the actual stock quantity to match physical count
            $newStockQuantity = $physicalCount;
            $quantityChanged = abs($shortageOverage);
            $adjustmentType = $shortageOverage > 0 ? 'adjustment_add' : 'adjustment_subtract';
            
            $medicine->update([
                'stock_quantity' => $newStockQuantity,
                'balance_per_card' => $newStockQuantity, // Update balance to match
                'shortage_overage' => 0, // Reset to balanced
            ]);
            
            // Log the stock adjustment
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => $adjustmentType,
                'quantity_changed' => $quantityChanged,
                'quantity_before' => $currentStock,
                'quantity_after' => $newStockQuantity,
                'reason' => $request->adjustment_reason,
                'notes' => "Stock adjusted based on physical count. Previous shortage/overage: {$shortageOverage}",
                'reference_type' => 'adjustment',
                'reference_id' => null
            ]);
            
            DB::commit();
            
            $adjustmentText = $shortageOverage > 0 ? 'increased' : 'decreased';
            
            return response()->json([
                'success' => true,
                'message' => "Stock {$adjustmentText} by {$quantityChanged} units to match physical count.",
                'data' => [
                    'old_stock' => $currentStock,
                    'new_stock' => $newStockQuantity,
                    'adjustment' => $shortageOverage,
                    'stock_status' => $medicine->refresh()->stock_status,
                    'stock_status_color' => $medicine->refresh()->stock_status_color,
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate inventory report
     */
    public function inventoryReport(Request $request)
    {
        try {
            $query = Medicine::active();
            
            // Filter by category if specified
            if ($request->has('category') && $request->category !== '') {
                $query->where('category', $request->category);
            }
            
            // Filter by discrepancy type
            if ($request->has('discrepancy_filter')) {
                switch ($request->discrepancy_filter) {
                    case 'shortage':
                        $query->where('shortage_overage', '<', 0);
                        break;
                    case 'overage':
                        $query->where('shortage_overage', '>', 0);
                        break;
                    case 'balanced':
                        $query->where('shortage_overage', '=', 0);
                        break;
                    case 'uncounted':
                        $query->whereNull('on_hand_per_count');
                        break;
                }
            }
            
            $medicines = $query->orderBy('medicine_name')->paginate(50);
            
            $stats = [
                'total_items' => Medicine::active()->count(),
                'counted_items' => Medicine::active()->whereNotNull('on_hand_per_count')->count(),
                'items_with_shortage' => Medicine::active()->where('shortage_overage', '<', 0)->count(),
                'items_with_overage' => Medicine::active()->where('shortage_overage', '>', 0)->count(),
                'balanced_items' => Medicine::active()->where('shortage_overage', '=', 0)->count(),
            ];
            
            return view('medicines.inventory-report', compact('medicines', 'stats'));
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate inventory report: ' . $e->getMessage()]);
        }
    }
}
