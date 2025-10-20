@extends('layouts.app')

@section('title', 'User Activity Reports')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">User Activity Reports</h1>
            <p class="text-white/70 mt-2">Staff performance, role distribution, and activity analysis</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-lg transition-all duration-200 backdrop-blur-md border border-white/20">
            ← Back to Reports
        </a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Role Distribution -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Role Distribution</h3>
            <div class="space-y-3">
                @foreach($roleStats as $stat)
                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-sm">{{ $stat->role }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-white/20 rounded-full h-2">
                            @php
                                $total = $roleStats->sum('count');
                                $percentage = $total > 0 ? ($stat->count / $total) * 100 : 0;
                                $colorClass = match($stat->role) {
                                    'Admin' => 'bg-purple-500',
                                    'Doctor' => 'bg-blue-500',
                                    'Nurse' => 'bg-green-500',
                                    'Pharmacist' => 'bg-yellow-500',
                                    'Receptionist' => 'bg-pink-500',
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

        <!-- Activity Summary -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Activity Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Users</span>
                    <span class="text-white font-medium">{{ number_format($users->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Appointments</span>
                    <span class="text-white font-medium">{{ number_format($users->sum('appointments_count')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Prescriptions</span>
                    <span class="text-white font-medium">{{ number_format($users->sum('prescriptions_count')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Avg per User</span>
                    <span class="text-white font-medium">{{ $users->count() > 0 ? number_format(($users->sum('appointments_count') + $users->sum('prescriptions_count')) / $users->count(), 1) : '0' }}</span>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Top Performers</h3>
            <div class="space-y-3">
                @php
                    $topPerformers = $users->sortByDesc(function($user) {
                        return $user->appointments_count + $user->prescriptions_count;
                    })->take(5);
                @endphp
                @foreach($topPerformers as $user)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <span class="text-blue-400 font-medium text-xs">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ Str::limit($user->name, 15) }}</p>
                            <p class="text-white/60 text-xs">{{ $user->role }}</p>
                        </div>
                    </div>
                    <span class="text-white font-medium text-sm">{{ $user->appointments_count + $user->prescriptions_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/20">
            <h3 class="text-lg font-semibold text-white">Staff Performance Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">User</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Role</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Status</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Appointments</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Prescriptions</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Total Activities</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($users as $user)
                    <tr class="hover:bg-white/5 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                    <span class="text-blue-400 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $user->name }}</p>
                                    <p class="text-white/60 text-sm">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ 
                                $user->role === 'Admin' ? 'bg-purple-500/20 text-purple-300' : 
                                ($user->role === 'Doctor' ? 'bg-blue-500/20 text-blue-300' : 
                                ($user->role === 'Nurse' ? 'bg-green-500/20 text-green-300' : 
                                ($user->role === 'Pharmacist' ? 'bg-yellow-500/20 text-yellow-300' : 'bg-pink-500/20 text-pink-300')))
                            }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->email_verified_at)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-300">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-300">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-center">
                                <p class="text-white font-medium">{{ number_format($user->appointments_count) }}</p>
                                @if($user->appointments_count > 0)
                                    <div class="w-full bg-white/20 rounded-full h-1 mt-1">
                                        @php
                                            $maxAppointments = $users->max('appointments_count');
                                            $percentage = $maxAppointments > 0 ? ($user->appointments_count / $maxAppointments) * 100 : 0;
                                        @endphp
                                        <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-center">
                                <p class="text-white font-medium">{{ number_format($user->prescriptions_count) }}</p>
                                @if($user->prescriptions_count > 0)
                                    <div class="w-full bg-white/20 rounded-full h-1 mt-1">
                                        @php
                                            $maxPrescriptions = $users->max('prescriptions_count');
                                            $percentage = $maxPrescriptions > 0 ? ($user->prescriptions_count / $maxPrescriptions) * 100 : 0;
                                        @endphp
                                        <div class="bg-green-500 h-1 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $totalActivities = $user->appointments_count + $user->prescriptions_count;
                            @endphp
                            <div class="text-center">
                                <p class="text-white font-bold">{{ number_format($totalActivities) }}</p>
                                @if($totalActivities > 0)
                                    <div class="w-full bg-white/20 rounded-full h-1 mt-1">
                                        @php
                                            $maxTotal = $users->map(function($u) { return $u->appointments_count + $u->prescriptions_count; })->max();
                                            $percentage = $maxTotal > 0 ? ($totalActivities / $maxTotal) * 100 : 0;
                                        @endphp
                                        <div class="bg-purple-500 h-1 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-white">{{ $user->created_at->format('M d, Y') }}</p>
                                <p class="text-white/60">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-white/60">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <p class="text-xl">No users found</p>
                                <p class="text-sm">Check your user data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productivity Analysis -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Productivity Analysis</h3>
            <div class="space-y-4">
                @php
                    $doctorAppointments = $users->where('role', 'Doctor')->sum('appointments_count');
                    $totalAppointments = $users->sum('appointments_count');
                    $doctorPercentage = $totalAppointments > 0 ? ($doctorAppointments / $totalAppointments) * 100 : 0;
                    
                    $pharmacistPrescriptions = $users->where('role', 'Pharmacist')->sum('prescriptions_count');
                    $totalPrescriptions = $users->sum('prescriptions_count');
                    $pharmacistPercentage = $totalPrescriptions > 0 ? ($pharmacistPrescriptions / $totalPrescriptions) * 100 : 0;
                @endphp
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white/80 text-sm">Doctor Appointments</span>
                        <span class="text-white font-medium">{{ number_format($doctorPercentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $doctorPercentage }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-white/80 text-sm">Pharmacist Prescriptions</span>
                        <span class="text-white font-medium">{{ number_format($pharmacistPercentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pharmacistPercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Composition -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Team Composition</h3>
            <div class="space-y-3">
                @foreach($roleStats as $stat)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @php
                            $roleIcon = match($stat->role) {
                                'Admin' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                'Doctor' => 'M19 14l-5 5-3-3m9-6v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h2l2-2h6l2 2h2a2 2 0 012 2z',
                                'Nurse' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                                'Pharmacist' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
                                default => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
                            };
                            $roleColor = match($stat->role) {
                                'Admin' => 'text-purple-400',
                                'Doctor' => 'text-blue-400',
                                'Nurse' => 'text-green-400',
                                'Pharmacist' => 'text-yellow-400',
                                default => 'text-gray-400'
                            };
                        @endphp
                        <svg class="w-5 h-5 {{ $roleColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $roleIcon }}"></path>
                        </svg>
                        <span class="text-white/80 text-sm">{{ $stat->role }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-white font-medium">{{ $stat->count }}</span>
                        <span class="text-white/60 text-xs ml-2">
                            ({{ $users->count() > 0 ? number_format(($stat->count / $users->count()) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="mt-8 flex justify-end space-x-3">
        <button onclick="exportReport('users', 'pdf')" class="bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
            Export PDF
        </button>
        <button onclick="exportReport('users', 'excel')" class="bg-green-600/20 hover:bg-green-600/30 text-green-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
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