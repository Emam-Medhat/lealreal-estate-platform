@extends('layouts.app')

@section('title', 'Agent Calendar')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Calendar</h1>
                    <p class="text-gray-600">Manage your appointments and schedule</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="addAppointment()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Appointment
                    </button>
                    <a href="{{ route('agent.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button onclick="previousMonth()" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $currentMonth->format('F Y') }}</h2>
                    <button onclick="nextMonth()" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="todayView()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Today
                    </button>
                    <div class="flex bg-gray-200 rounded-lg">
                        <button onclick="setView('month')" class="view-btn px-3 py-2 rounded-l-lg bg-blue-600 text-white">Month</button>
                        <button onclick="setView('week')" class="view-btn px-3 py-2 text-gray-700 hover:bg-gray-300">Week</button>
                        <button onclick="setView('day')" class="view-btn px-3 py-2 rounded-r-lg text-gray-700 hover:bg-gray-300">Day</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Week Days Header -->
            <div class="grid grid-cols-7 bg-gray-50">
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Sun</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Mon</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Tue</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Wed</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Thu</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700 border-r">Fri</div>
                <div class="p-3 text-center text-sm font-medium text-gray-700">Sat</div>
            </div>
            
            <!-- Calendar Days -->
            <div class="grid grid-cols-7">
                @for ($i = 0; $i < $startOffset; $i++)
                    <div class="h-32 border-r border-b bg-gray-50"></div>
                @endfor
                
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    <div class="h-32 border-r border-b hover:bg-gray-50 cursor-pointer relative" onclick="selectDate({{ $currentMonth->year }}, {{ $currentMonth->month }}, {{ $day }})">
                        <div class="p-2">
                            <div class="text-sm font-medium text-gray-900 {{ $day === $today ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                {{ $day }}
                            </div>
                            
                            <!-- Appointments for this day -->
                            <div class="mt-1 space-y-1">
                                @foreach ($appointments->where('date', $currentMonth->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT))->take(3) as $appointment)
                                    <div class="text-xs p-1 rounded
                                        @if($appointment->type === 'showing')
                                            bg-blue-100 text-blue-800
                                        @elseif($appointment->type === 'meeting')
                                            bg-green-100 text-green-800
                                        @elseif($appointment->type === 'call')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ $appointment->time }} - {{ Str::limit($appointment->title, 15) }}
                                    </div>
                                @endforeach
                                
                                @if($appointments->where('date', $currentMonth->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT))->count() > 3)
                                    <div class="text-xs text-gray-500">+{{ $appointments->where('date', $currentMonth->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT))->count() - 3 }} more</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endfor
                
                @for ($i = 0; $i < (7 - (($startOffset + $daysInMonth) % 7)) % 7; $i++)
                    <div class="h-32 border-r border-b bg-gray-50"></div>
                @endfor
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Appointments</h2>
            
            <div class="space-y-3">
                @forelse ($upcomingAppointments as $appointment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="viewAppointment({{ $appointment->id }})">
                        <div class="flex items-center">
                            <div class="bg-{{ $appointment->type === 'showing' ? 'blue' : ($appointment->type === 'meeting' ? 'green' : ($appointment->type === 'call' ? 'yellow' : 'gray')) }}-100 rounded-full p-2 mr-3">
                                <i class="fas fa-{{ $appointment->type === 'showing' ? 'home' : ($appointment->type === 'meeting' ? 'users' : ($appointment->type === 'call' ? 'phone' : 'calendar')) }} text-{{ $appointment->type === 'showing' ? 'blue' : ($appointment->type === 'meeting' ? 'green' : ($appointment->type === 'call' ? 'yellow' : 'gray')) }}-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $appointment->title }}</h4>
                                <div class="flex items-center space-x-3 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $appointment->date->format('M j, Y') }}</span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $appointment->time }}</span>
                                    @if($appointment->client)
                                        <span><i class="fas fa-user mr-1"></i>{{ $appointment->client->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($appointment->status === 'confirmed')
                                    bg-green-100 text-green-800
                                @elseif($appointment->status === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($appointment->status) }}
                            </span>
                            <button onclick="event.stopPropagation(); editAppointment({{ $appointment->id }})" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No upcoming appointments</p>
                @endforelse
            </div>
        </div>

        <!-- Appointment Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['this_month'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Show Rate</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['show_rate'] }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Appointment Modal -->
<div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Appointment</h3>
        
        <form action="{{ route('agent.appointments.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Appointment Title</label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                        <input type="time" name="time" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="showing">Property Showing</option>
                            <option value="meeting">Client Meeting</option>
                            <option value="call">Phone Call</option>
                            <option value="follow_up">Follow Up</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                        <select name="client_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                @if($selectedProperty)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-medium text-gray-800">{{ $selectedProperty->title }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedProperty->address }}</p>
                        </div>
                    </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" placeholder="Meeting location"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <select name="duration"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAppointmentModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentYear = {{ $currentMonth->year }};
let currentMonth = {{ $currentMonth->month }};
let currentView = 'month';

function previousMonth() {
    currentMonth--;
    if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }
    updateCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    }
    updateCalendar();
}

function todayView() {
    const today = new Date();
    currentMonth = today.getMonth() + 1;
    currentYear = today.getFullYear();
    updateCalendar();
}

function setView(view) {
    currentView = view;
    
    // Update button styles
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('text-gray-700', 'hover:bg-gray-300');
    });
    
    event.target.classList.remove('text-gray-700', 'hover:bg-gray-300');
    event.target.classList.add('bg-blue-600', 'text-white');
    
    updateCalendar();
}

function selectDate(year, month, day) {
    const date = new Date(year, month - 1, day);
    const formattedDate = date.toISOString().split('T')[0];
    
    // Add appointment with pre-selected date
    document.getElementById('appointmentModal').classList.remove('hidden');
    document.querySelector('input[name="date"]').value = formattedDate;
}

function updateCalendar() {
    window.location.href = '/agent/calendar?month=' + currentMonth + '&year=' + currentYear + '&view=' + currentView;
}

function addAppointment() {
    document.getElementById('appointmentModal').classList.remove('hidden');
}

function closeAppointmentModal() {
    document.getElementById('appointmentModal').classList.add('hidden');
}

function viewAppointment(appointmentId) {
    window.location.href = '/agent/appointments/' + appointmentId;
}

function editAppointment(appointmentId) {
    window.location.href = '/agent/appointments/' + appointmentId + '/edit';
}
</script>
@endsection
