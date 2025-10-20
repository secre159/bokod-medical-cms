@extends('layouts.app')

@section('title', 'Medicine Inventory Reports')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Medicine Inventory Reports</h1>
            <p class="text-white/70 mt-2">Stock levels, expiry dates, and inventory value analysis</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 backdrop-blur-md border border-white/20">
            ← Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6 mb-8">
        <h2 class="text-xl font-semibold text-white mb-4">Filters</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="Discontinued" {{ request('status') == 'Discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Dosage Form</label>
                <select name="dosage_form" class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Forms</option>
                    <option value="Tablet" {{ request('dosage_form') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                    <option value="Capsule" {{ request('dosage_form') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                    <option value="Syrup" {{ request('dosage_form') == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                    <option value="Injection" {{ request('dosage_form') == 'Injection' ? 'selected' : '' }}>Injection</option>
                    <option value="Cream" {{ request('dosage_form') == 'Cream' ? 'selected' : '' }}>Cream</option>
                    <option value="Drops" {{ request('dosage_form') == 'Drops' ? 'selected' : '' }}>Drops</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="low_stock_only" value="1" {{ request('low_stock_only') ? 'checked' : '' }}
                           class="rounded bg-white/10 border-white/20 text-blue-600 focus:ring-blue-500">
                    <span class="text-white/80 text-sm">Low Stock Only</span>
                </label>
            </div>
            
            <div class="flex items-center space-x-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }}
                           class="rounded bg-white/10 border-white/20 text-blue-600 focus:ring-blue-500">
                    <span class="text-white/80 text-sm">Expiring Soon (3 months)</span>
                </label>
            </div>
            
            <div class="lg:col-span-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    Apply Filters
                </button>
                <a href="{{ route('reports.medicines') }}" class="bg-gray-600/20 hover:bg-gray-600/30 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 ml-3">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Total Value</p>
                    <p class="text-2xl font-bold text-white">${{ number_format($totalValue, 2) }}</p>
                </div>
                <div class="bg-green-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Low Stock Value</p>
                    <p class="text-2xl font-bold text-red-400">${{ number_format($lowStockValue, 2) }}</p>
                </div>
                <div class="bg-red-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.348 13.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Total Items</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($medicines->count()) }}</p>
                </div>
                <div class="bg-blue-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Expiring Soon</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ number_format($expiringMedicines->count()) }}</p>
                </div>
                <div class="bg-yellow-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Medicines Alert -->
    @if($expiringMedicines->count() > 0)
    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-6 mb-8">
        <div class="flex items-start space-x-3">
            <div class="bg-yellow-500/20 p-2 rounded-full">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.348 13.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yellow-300 mb-2">Medicines Expiring Soon</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($expiringMedicines->take(6) as $medicine)
                    <div class="bg-white/5 rounded-lg p-3">
                        <p class="text-white font-medium">{{ $medicine->name }}</p>
                        <p class="text-white/70 text-sm">Expires: {{ \Carbon\Carbon::parse($medicine->expiry_date)->format('M d, Y') }}</p>
                        <p class="text-yellow-400 text-sm">{{ \Carbon\Carbon::parse($medicine->expiry_date)->diffForHumans() }}</p>
                    </div>
                    @endforeach
                </div>
                @if($expiringMedicines->count() > 6)
                <p class="text-yellow-400 text-sm mt-3">and {{ $expiringMedicines->count() - 6 }} more...</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Medicine List -->
    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/20">
            <h3 class="text-lg font-semibold text-white">Medicine Inventory</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Medicine</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Stock</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Unit Price</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Total Value</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Expiry Date</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Status</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Prescriptions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($medicines as $medicine)
                    <tr class="hover:bg-white/5 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-white font-medium">{{ $medicine->name }}</p>
                                <p class="text-white/60 text-sm">{{ $medicine->generic_name }}</p>
                                <p class="text-white/40 text-xs">{{ $medicine->strength }} • {{ $medicine->dosage_form }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-white font-medium">{{ number_format($medicine->stock_quantity) }}</p>
                                <p class="text-white/60">{{ $medicine->unit }}</p>
                                @if($medicine->stock_quantity <= $medicine->minimum_stock_level)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-300 mt-1">
                                    Low Stock
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white">${{ number_format($medicine->unit_price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">${{ number_format($medicine->stock_quantity * $medicine->unit_price, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-white">{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('M d, Y') }}</p>
                                @php
                                    $expiryDays = \Carbon\Carbon::parse($medicine->expiry_date)->diffInDays(now(), false);
                                @endphp
                                @if($expiryDays > -90)
                                <p class="text-yellow-400 text-xs">{{ \Carbon\Carbon::parse($medicine->expiry_date)->diffForHumans() }}</p>
                                @else
                                <p class="text-white/60 text-xs">{{ \Carbon\Carbon::parse($medicine->expiry_date)->diffForHumans() }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ 
                                $medicine->status === 'Active' ? 'bg-green-500/20 text-green-300' : 
                                ($medicine->status === 'Inactive' ? 'bg-gray-500/20 text-gray-300' : 'bg-red-500/20 text-red-300')
                            }}">
                                {{ $medicine->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">{{ number_format($medicine->prescriptions_count) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-white/60">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                <p class="text-xl">No medicines found</p>
                                <p class="text-sm">Try adjusting your filters or check your inventory</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="mt-8 flex justify-end space-x-3">
        <button onclick="exportReport('medicines', 'pdf')" class="bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
            Export PDF
        </button>
        <button onclick="exportReport('medicines', 'excel')" class="bg-green-600/20 hover:bg-green-600/30 text-green-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
            Export Excel
        </button>
    </div>
</div>

<script>
function exportReport(type, format) {
    const params = new URLSearchParams(window.location.search);
    const queryString = params.toString();
    const url = `/reports/export/${type}/${format}${queryString ? '?' + queryString : ''}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Export error:', error);
            alert('Export failed. Please try again.');
        });
}
</script>
@endsection