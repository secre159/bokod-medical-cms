@extends('layouts.app')

@section('title', 'Appointment Reports')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Appointment Reports</h1>
            <p class="text-white/70 mt-2">Appointment trends, status distribution, and scheduling analysis</p>
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
                <label class="block text-white/80 text-sm font-medium mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="No-show" {{ request('status') == 'No-show' ? 'selected' : '' }}>No-show</option>
                </select>
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Type</label>
                <select name="type" class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="Consultation" {{ request('type') == 'Consultation' ? 'selected' : '' }}>Consultation</option>
                    <option value="Follow-up" {{ request('type') == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
                    <option value="Emergency" {{ request('type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="Check-up" {{ request('type') == 'Check-up' ? 'selected' : '' }}>Check-up</option>
                </select>
            </div>
            
            <div class="lg:col-span-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    Apply Filters
                </button>
                <a href="{{ route('reports.appointments') }}" class="bg-gray-600/20 hover:bg-gray-600/30 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 ml-3">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Status Distribution -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Status Distribution</h3>
            <div class="space-y-3">
                @foreach($statusStats as $stat)
                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-sm">{{ $stat->status }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-white/20 rounded-full h-2">
                            @php
                                $total = $statusStats->sum('count');
                                $percentage = $total > 0 ? ($stat->count / $total) * 100 : 0;
                                $colorClass = match($stat->status) {
                                    'Scheduled' => 'bg-blue-500',
                                    'Completed' => 'bg-green-500',
                                    'Cancelled' => 'bg-red-500',
                                    'No-show' => 'bg-yellow-500',
                                    default => 'bg-gray-500'
                                };
                            @endphp
                            <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-white font-medium text-sm">{{ $stat->count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Monthly Trends (Last 12 Months)</h3>
            <div class="space-y-3">
                @foreach($monthlyStats->take(6) as $stat)
                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-sm">{{ \Carbon\Carbon::createFromDate($stat->year, $stat->month)->format('M Y') }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="text-white/60 text-xs">
                            <span class="text-green-400">{{ $stat->completed }}</span> / 
                            <span class="text-red-400">{{ $stat->cancelled }}</span>
                        </div>
                        <span class="text-white font-medium text-sm">{{ $stat->total_appointments }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Appointments</span>
                    <span class="text-white font-medium">{{ number_format($appointments->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Completed</span>
                    <span class="text-green-400 font-medium">{{ number_format($appointments->where('status', 'Completed')->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Scheduled</span>
                    <span class="text-blue-400 font-medium">{{ number_format($appointments->where('status', 'Scheduled')->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Cancelled</span>
                    <span class="text-red-400 font-medium">{{ number_format($appointments->where('status', 'Cancelled')->count()) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointment List -->
    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/20">
            <h3 class="text-lg font-semibold text-white">Appointment Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Patient</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Date & Time</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Type</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Doctor</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Status</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($appointments as $appointment)
                    <tr class="hover:bg-white/5 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($appointment->patient->avatar)
                                    <img src="{{ asset('storage/' . $appointment->patient->avatar) }}" alt="{{ $appointment->patient->first_name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="text-blue-400 font-medium text-sm">{{ substr($appointment->patient->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-white font-medium">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</p>
                                    <p class="text-white/60 text-xs">{{ $appointment->patient->patient_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-white">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                                <p class="text-white/60">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ 
                                $appointment->type === 'Emergency' ? 'bg-red-500/20 text-red-300' : 
                                ($appointment->type === 'Follow-up' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-blue-500/20 text-blue-300') 
                            }}">
                                {{ $appointment->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-white">{{ $appointment->user->name }}</p>
                                <p class="text-white/60 text-xs">{{ $appointment->user->role }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ 
                                $appointment->status === 'Completed' ? 'bg-green-500/20 text-green-300' : 
                                ($appointment->status === 'Scheduled' ? 'bg-blue-500/20 text-blue-300' : 
                                ($appointment->status === 'Cancelled' ? 'bg-red-500/20 text-red-300' : 'bg-yellow-500/20 text-yellow-300'))
                            }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-white/80 text-sm">{{ Str::limit($appointment->notes, 30) }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-white/60">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xl">No appointments found</p>
                                <p class="text-sm">Try adjusting your filters or check your data</p>
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
        <button onclick="exportReport('appointments', 'pdf')" class="bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
            Export PDF
        </button>
        <button onclick="exportReport('appointments', 'excel')" class="bg-green-600/20 hover:bg-green-600/30 text-green-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
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