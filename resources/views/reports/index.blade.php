@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Reports Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Total Patients</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['total_patients']) }}</p>
                </div>
                <div class="bg-blue-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/60 text-sm mt-2">{{ $stats['active_patients'] }} active</p>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Total Appointments</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['total_appointments']) }}</p>
                </div>
                <div class="bg-green-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/60 text-sm mt-2">{{ $stats['appointments_today'] }} today</p>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Total Medicines</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['total_medicines']) }}</p>
                </div>
                <div class="bg-purple-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/60 text-sm mt-2">{{ $stats['low_stock_medicines'] }} low stock</p>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">This Month</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($stats['appointments_this_month']) }}</p>
                </div>
                <div class="bg-yellow-500/20 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/60 text-sm mt-2">{{ $stats['appointments_this_week'] }} this week</p>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Patient Reports -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Patient Reports</h2>
                <div class="bg-blue-500/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/70 text-sm mb-4">Demographics, status, and patient statistics</p>
            <div class="flex space-x-3">
                <a href="{{ route('reports.patients') }}" class="flex-1 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-center">
                    View Report
                </a>
                <button onclick="exportReport('patients', 'pdf')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    PDF
                </button>
                <button onclick="exportReport('patients', 'excel')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Excel
                </button>
            </div>
        </div>

        <!-- Appointment Reports -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Appointment Reports</h2>
                <div class="bg-green-500/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/70 text-sm mb-4">Appointment trends, status distribution, and scheduling analysis</p>
            <div class="flex space-x-3">
                <a href="{{ route('reports.appointments') }}" class="flex-1 bg-green-600/20 hover:bg-green-600/30 text-green-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-center">
                    View Report
                </a>
                <button onclick="exportReport('appointments', 'pdf')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    PDF
                </button>
                <button onclick="exportReport('appointments', 'excel')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Excel
                </button>
            </div>
        </div>

        <!-- Medicine Reports -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Medicine Inventory</h2>
                <div class="bg-purple-500/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/70 text-sm mb-4">Stock levels, expiry dates, and inventory value</p>
            <div class="flex space-x-3">
                <a href="{{ route('reports.medicines') }}" class="flex-1 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-center">
                    View Report
                </a>
                <button onclick="exportReport('medicines', 'pdf')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    PDF
                </button>
                <button onclick="exportReport('medicines', 'excel')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Excel
                </button>
            </div>
        </div>

        <!-- User Activity Reports -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">User Activity</h2>
                <div class="bg-yellow-500/20 p-2 rounded-full">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-white/70 text-sm mb-4">Staff performance and role distribution</p>
            <div class="flex space-x-3">
                <a href="{{ route('reports.users') }}" class="flex-1 bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 text-center">
                    View Report
                </a>
                <button onclick="exportReport('users', 'pdf')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    PDF
                </button>
                <button onclick="exportReport('users', 'excel')" class="bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                    Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport(type, format) {
    fetch(`/reports/export/${type}/${format}`)
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