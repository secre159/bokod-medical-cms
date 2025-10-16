@extends('layouts.app')

@section('title', 'Patient Reports')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white">Patient Reports</h1>
            <p class="text-white/70 mt-2">Demographics, status, and patient statistics</p>
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
                </select>
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Gender</label>
                <select name="gender" class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Genders</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ request('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Age From</label>
                <input type="number" name="age_from" value="{{ request('age_from') }}" min="0" max="120" 
                       class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-white/50" 
                       placeholder="0">
            </div>
            
            <div>
                <label class="block text-white/80 text-sm font-medium mb-2">Age To</label>
                <input type="number" name="age_to" value="{{ request('age_to') }}" min="0" max="120"
                       class="w-full bg-white/10 border border-white/20 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-white/50" 
                       placeholder="120">
            </div>
            
            <div class="lg:col-span-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                    Apply Filters
                </button>
                <a href="{{ route('reports.patients') }}" class="bg-gray-600/20 hover:bg-gray-600/30 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 ml-3">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Age Groups Chart -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Age Distribution</h3>
            <div class="space-y-3">
                @foreach($ageGroups as $group)
                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-sm">{{ $group->age_group }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-white/20 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $patients->count() > 0 ? ($group->count / $patients->count()) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-white font-medium text-sm">{{ $group->count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Gender Distribution</h3>
            <div class="space-y-3">
                @foreach($genderStats as $stat)
                <div class="flex items-center justify-between">
                    <span class="text-white/80 text-sm">{{ $stat->gender ?: 'Not Specified' }}</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-white/20 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $patients->count() > 0 ? ($stat->count / $patients->count()) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-white font-medium text-sm">{{ $stat->count }}</span>
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
                    <span class="text-white/80 text-sm">Total Patients</span>
                    <span class="text-white font-medium">{{ number_format($patients->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Active Patients</span>
                    <span class="text-white font-medium">{{ number_format($patients->where('status', 'Active')->count()) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Appointments</span>
                    <span class="text-white font-medium">{{ number_format($patients->sum('appointments_count')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/80 text-sm">Total Prescriptions</span>
                    <span class="text-white font-medium">{{ number_format($patients->sum('prescriptions_count')) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient List -->
    <div class="bg-white/10 backdrop-blur-md rounded-xl border border-white/20 overflow-hidden">
        <div class="px-6 py-4 border-b border-white/20">
            <h3 class="text-lg font-semibold text-white">Patient Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/5">
                    <tr>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Patient</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Age</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Gender</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Status</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Contact</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Appointments</th>
                        <th class="text-left text-white/80 font-medium px-6 py-3 text-sm">Prescriptions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-white/5 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($patient->avatar)
                                    <img src="{{ asset('storage/' . $patient->avatar) }}" alt="{{ $patient->first_name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <span class="text-blue-400 font-medium text-sm">{{ substr($patient->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-white font-medium">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                    <p class="text-white/60 text-xs">{{ $patient->patient_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white">{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age : 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white/80">{{ $patient->gender ?: 'Not Specified' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $patient->status === 'Active' ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ $patient->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                @if($patient->phone)
                                    <p class="text-white/80">{{ $patient->phone }}</p>
                                @endif
                                @if($patient->email)
                                    <p class="text-white/60">{{ $patient->email }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">{{ number_format($patient->appointments_count) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-white font-medium">{{ number_format($patient->prescriptions_count) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-white/60">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-xl">No patients found</p>
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
        <button onclick="exportReport('patients', 'pdf')" class="bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
            Export PDF
        </button>
        <button onclick="exportReport('patients', 'excel')" class="bg-green-600/20 hover:bg-green-600/30 text-green-300 hover:text-white px-4 py-2 rounded-lg font-medium transition-all duration-200">
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