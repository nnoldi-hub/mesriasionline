@extends('layouts.craftsman')

@section('title', 'Disponibilitate & Program')
@section('page-title', 'Disponibilitate & Program')

@section('content')
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Weekly Schedule -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Program de lucru săptămânal</h2>
        
        <form action="{{ route('craftsman.availability.schedule') }}" method="POST">
            @csrf
            @method('PUT')
            
            @php
                $days = [
                    'monday' => 'Luni',
                    'tuesday' => 'Marți',
                    'wednesday' => 'Miercuri',
                    'thursday' => 'Joi',
                    'friday' => 'Vineri',
                    'saturday' => 'Sâmbătă',
                    'sunday' => 'Duminică',
                ];
                $schedule = auth()->user()->weekly_schedule ?? [];
            @endphp
            
            <div class="space-y-3 mb-6">
                @foreach($days as $key => $label)
                    @php
                        $daySchedule = $schedule[$key] ?? ['active' => false, 'start' => '09:00', 'end' => '18:00'];
                    @endphp
                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                        <label class="flex items-center w-32">
                            <input type="hidden" name="weekly_schedule[{{ $key }}][active]" value="0">
                            <input type="checkbox" name="weekly_schedule[{{ $key }}][active]" value="1" {{ $daySchedule['active'] ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                            <span class="font-medium">{{ $label }}</span>
                        </label>
                        <input type="time" name="weekly_schedule[{{ $key }}][start]" value="{{ $daySchedule['start'] ?? '09:00' }}" class="border border-gray-300 rounded px-3 py-1">
                        <span class="text-gray-500">-</span>
                        <input type="time" name="weekly_schedule[{{ $key }}][end]" value="{{ $daySchedule['end'] ?? '18:00' }}" class="border border-gray-300 rounded px-3 py-1">
                    </div>
                @endforeach
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durată slot (minute)</label>
                    <input type="number" name="slot_duration_minutes" value="{{ auth()->user()->slot_duration_minutes ?? 60 }}" min="15" max="480" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pauză între sloturi (minute)</label>
                    <input type="number" name="buffer_between_slots" value="{{ auth()->user()->buffer_between_slots ?? 15 }}" min="0" max="120" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
            
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                Salvează programul
            </button>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="space-y-6">
        <!-- Generate Slots -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Generează sloturi</h3>
            <p class="text-sm text-gray-500 mb-4">Generează automat sloturi de disponibilitate bazate pe programul tău.</p>
            <form action="{{ route('craftsman.availability.generate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Zile în avans</label>
                    <select name="days_ahead" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="7">7 zile</option>
                        <option value="14">14 zile</option>
                        <option value="30" selected>30 zile</option>
                        <option value="60">60 zile</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                    Generează sloturi
                </button>
            </form>
        </div>

        <!-- Add Vacation -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Adaugă perioadă liberă</h3>
            <form action="{{ route('craftsman.availability.add-vacation') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">De la</label>
                    <input type="date" name="start_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Până la</label>
                    <input type="date" name="end_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-700 mb-1">Motiv (opțional)</label>
                    <input type="text" name="reason" placeholder="Ex: Concediu, Sărbători..." class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded-lg hover:bg-yellow-600 transition">
                    Adaugă vacanță
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Vacation Periods -->
@php $vacations = auth()->user()->vacation_periods ?? []; @endphp
@if(count($vacations) > 0)
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Perioade libere programate</h2>
    <div class="space-y-3">
        @foreach($vacations as $index => $vacation)
            <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($vacation['start'])->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($vacation['end'])->format('d.m.Y') }}</span>
                    @if($vacation['reason'] ?? null)
                        <span class="text-gray-500 ml-2">({{ $vacation['reason'] }})</span>
                    @endif
                </div>
                <form action="{{ route('craftsman.availability.remove-vacation') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="index" value="{{ $index }}">
                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Sigur vrei să ștergi această perioadă?')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Booking Settings -->
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Setări programări online</h2>
    
    <form action="{{ route('craftsman.availability.booking-settings') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="flex items-center mb-4">
                    <input type="hidden" name="accepts_online_booking" value="0">
                    <input type="checkbox" name="accepts_online_booking" value="1" {{ $bookingSettings->accepts_online_booking ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                    <span>Accept programări online</span>
                </label>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Programare cu maxim X zile în avans</label>
                    <input type="number" name="advance_booking_days" value="{{ $bookingSettings->advance_booking_days }}" min="1" max="365" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preaviz minim (ore)</label>
                    <input type="number" name="min_notice_hours" value="{{ $bookingSettings->min_notice_hours }}" min="0" max="168" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max. programări pe zi (opțional)</label>
                    <input type="number" name="max_bookings_per_day" value="{{ $bookingSettings->max_bookings_per_day }}" min="1" max="50" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Nelimitat">
                </div>
            </div>
            
            <div>
                <label class="flex items-center mb-4">
                    <input type="hidden" name="requires_confirmation" value="0">
                    <input type="checkbox" name="requires_confirmation" value="1" {{ $bookingSettings->requires_confirmation ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                    <span>Necesită confirmare manuală</span>
                </label>
                
                <label class="flex items-center mb-4">
                    <input type="hidden" name="send_reminders" value="0">
                    <input type="checkbox" name="send_reminders" value="1" {{ $bookingSettings->send_reminders ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                    <span>Trimite remindere</span>
                </label>

                <label class="flex items-center mb-4">
                    <input type="hidden" name="send_email_reminders" value="0">
                    <input type="checkbox" name="send_email_reminders" value="1" {{ $bookingSettings->send_email_reminders ?? true ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                    <span>Remindere pe Email</span>
                </label>

                @if(config('services.sms.enabled'))
                <label class="flex items-center mb-4">
                    <input type="hidden" name="send_sms_reminders" value="0">
                    <input type="checkbox" name="send_sms_reminders" value="1" {{ $bookingSettings->send_sms_reminders ?? false ? 'checked' : '' }} class="mr-2 rounded text-primary-600">
                    <span>Remindere pe SMS</span>
                </label>
                @endif
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reminder cu X ore înainte</label>
                    <input type="number" name="reminder_hours_before" value="{{ $bookingSettings->reminder_hours_before }}" min="1" max="168" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anulare gratuită cu X ore înainte</label>
                    <input type="number" name="cancellation_hours" value="{{ $bookingSettings->cancellation_hours }}" min="0" max="168" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Taxă anulare târzie (%)</label>
                    <input type="number" name="cancellation_fee_percent" value="{{ $bookingSettings->cancellation_fee_percent }}" min="0" max="100" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Instrucțiuni programare (opțional)</label>
            <textarea name="booking_instructions" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Instrucțiuni afișate clienților când fac o programare...">{{ $bookingSettings->booking_instructions }}</textarea>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Politică anulare</label>
            <textarea name="cancellation_policy" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ $bookingSettings->cancellation_policy }}</textarea>
        </div>
        
        <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
            Salvează setările
        </button>
    </form>
</div>

<!-- Calendar View -->
<div class="mt-6 bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Calendar disponibilitate</h2>
        <div class="flex items-center space-x-2">
            <a href="?month={{ $month == 1 ? 12 : $month - 1 }}&year={{ $month == 1 ? $year - 1 : $year }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <span class="font-medium">{{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}</span>
            <a href="?month={{ $month == 12 ? 1 : $month + 1 }}&year={{ $month == 12 ? $year + 1 : $year }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-7 gap-1">
        @foreach(['L', 'Ma', 'Mi', 'J', 'V', 'S', 'D'] as $dayName)
            <div class="text-center text-sm font-medium text-gray-500 py-2">{{ $dayName }}</div>
        @endforeach
        
        @php
            $firstDay = $startDate->copy()->startOfMonth();
            $startPadding = $firstDay->dayOfWeekIso - 1;
            $daysInMonth = $startDate->daysInMonth;
        @endphp
        
        @for($i = 0; $i < $startPadding; $i++)
            <div></div>
        @endfor
        
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $currentDate = \Carbon\Carbon::create($year, $month, $day);
                $dateKey = $currentDate->format('Y-m-d');
                $daySlots = $slots->get($dateKey, collect());
                $availableCount = $daySlots->where('status', 'available')->count();
                $bookedCount = $daySlots->where('status', 'booked')->count();
                $isPast = $currentDate->isPast();
            @endphp
            <div class="border rounded-lg p-2 min-h-[80px] {{ $isPast ? 'bg-gray-100' : 'bg-white' }}">
                <div class="text-sm font-medium {{ $currentDate->isToday() ? 'text-primary-600' : 'text-gray-900' }}">{{ $day }}</div>
                @if(!$isPast && $daySlots->count() > 0)
                    <div class="mt-1 space-y-1">
                        @if($availableCount > 0)
                            <div class="text-xs bg-green-100 text-green-700 px-1 rounded">{{ $availableCount }} liber</div>
                        @endif
                        @if($bookedCount > 0)
                            <div class="text-xs bg-blue-100 text-blue-700 px-1 rounded">{{ $bookedCount }} rezervat</div>
                        @endif
                    </div>
                @endif
            </div>
        @endfor
    </div>
    
    <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
        <span class="flex items-center"><span class="w-3 h-3 bg-green-100 rounded mr-1"></span> Disponibil</span>
        <span class="flex items-center"><span class="w-3 h-3 bg-blue-100 rounded mr-1"></span> Rezervat</span>
        <span class="flex items-center"><span class="w-3 h-3 bg-gray-100 rounded mr-1"></span> Blocat/Trecut</span>
    </div>
</div>
@endsection
