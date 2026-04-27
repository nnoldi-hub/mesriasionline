<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\AvailabilitySlot;
use App\Models\BookingSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Display availability calendar.
     */
    public function index(Request $request)
    {
        $craftsman = auth()->user();
        
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $slots = AvailabilitySlot::where('user_id', $craftsman->id)
            ->betweenDates($startDate->toDateString(), $endDate->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn($slot) => $slot->date->format('Y-m-d'));
        
        $bookingSettings = BookingSetting::getOrCreate($craftsman);
        
        return view('craftsman.availability.index', compact(
            'slots',
            'bookingSettings',
            'month',
            'year',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Update weekly schedule.
     */
    public function updateSchedule(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'weekly_schedule' => 'required|array',
            'weekly_schedule.*.active' => 'boolean',
            'weekly_schedule.*.start' => 'required_if:weekly_schedule.*.active,true|nullable|date_format:H:i',
            'weekly_schedule.*.end' => 'required_if:weekly_schedule.*.active,true|nullable|date_format:H:i|after:weekly_schedule.*.start',
            'slot_duration_minutes' => 'required|integer|min:15|max:480',
            'buffer_between_slots' => 'required|integer|min:0|max:120',
        ]);
        
        $craftsman->update([
            'weekly_schedule' => $validated['weekly_schedule'],
            'slot_duration_minutes' => $validated['slot_duration_minutes'],
            'buffer_between_slots' => $validated['buffer_between_slots'],
        ]);
        
        return back()->with('success', 'Programul de lucru a fost actualizat.');
    }

    /**
     * Generate slots for upcoming days.
     */
    public function generateSlots(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'days_ahead' => 'required|integer|min:1|max:90',
        ]);
        
        $startDate = now()->addDay();
        $endDate = now()->addDays($validated['days_ahead']);
        
        $slotsCreated = AvailabilitySlot::generateSlotsForCraftsman($craftsman, $startDate, $endDate);
        
        return back()->with('success', "Au fost generate {$slotsCreated} sloturi de disponibilitate.");
    }

    /**
     * Block a specific slot.
     */
    public function blockSlot(Request $request, AvailabilitySlot $slot)
    {
        $craftsman = auth()->user();
        
        if ($slot->user_id !== $craftsman->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);
        
        if ($slot->block($validated['notes'] ?? null)) {
            return back()->with('success', 'Slotul a fost blocat.');
        }
        
        return back()->with('error', 'Slotul nu poate fi blocat.');
    }

    /**
     * Release a blocked slot.
     */
    public function releaseSlot(AvailabilitySlot $slot)
    {
        $craftsman = auth()->user();
        
        if ($slot->user_id !== $craftsman->id) {
            abort(403);
        }
        
        if ($slot->release()) {
            return back()->with('success', 'Slotul a fost eliberat.');
        }
        
        return back()->with('error', 'Slotul nu poate fi eliberat.');
    }

    /**
     * Add vacation period.
     */
    public function addVacation(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);
        
        $vacations = $craftsman->vacation_periods ?? [];
        $vacations[] = [
            'start' => $validated['start_date'],
            'end' => $validated['end_date'],
            'reason' => $validated['reason'] ?? null,
        ];
        
        $craftsman->update(['vacation_periods' => $vacations]);
        
        // Block all slots in this period
        AvailabilitySlot::where('user_id', $craftsman->id)
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->where('status', 'available')
            ->update(['status' => 'blocked', 'notes' => 'Vacanță: ' . ($validated['reason'] ?? 'Concediu')]);
        
        return back()->with('success', 'Perioada de vacanță a fost adăugată.');
    }

    /**
     * Remove vacation period.
     */
    public function removeVacation(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'index' => 'required|integer|min:0',
        ]);
        
        $vacations = $craftsman->vacation_periods ?? [];
        
        if (isset($vacations[$validated['index']])) {
            $removed = $vacations[$validated['index']];
            unset($vacations[$validated['index']]);
            $craftsman->update(['vacation_periods' => array_values($vacations)]);
            
            // Optionally release blocked slots
            AvailabilitySlot::where('user_id', $craftsman->id)
                ->whereBetween('date', [$removed['start'], $removed['end']])
                ->where('status', 'blocked')
                ->update(['status' => 'available', 'notes' => null]);
            
            return back()->with('success', 'Perioada de vacanță a fost ștearsă.');
        }
        
        return back()->with('error', 'Perioada de vacanță nu a fost găsită.');
    }

    /**
     * Update booking settings.
     */
    public function updateBookingSettings(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'accepts_online_booking' => 'boolean',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'min_notice_hours' => 'required|integer|min:0|max:168',
            'max_bookings_per_day' => 'nullable|integer|min:1|max:50',
            'requires_confirmation' => 'boolean',
            'auto_confirm' => 'boolean',
            'booking_instructions' => 'nullable|string|max:2000',
            'cancellation_policy' => 'nullable|string|max:2000',
            'cancellation_hours' => 'required|integer|min:0|max:168',
            'cancellation_fee_percent' => 'required|numeric|min:0|max:100',
            'send_reminders' => 'boolean',
            'reminder_hours_before' => 'required|integer|min:1|max:168',
            'send_sms_reminders' => 'boolean',
            'send_email_reminders' => 'boolean',
            'sms_reminder_hours_before' => 'nullable|integer|min:1|max:168',
        ]);
        
        BookingSetting::updateOrCreate(
            ['user_id' => $craftsman->id],
            $validated
        );
        
        return back()->with('success', 'Setările de programare au fost actualizate.');
    }

    /**
     * Get available slots for a specific date (AJAX).
     */
    public function getSlotsForDate(Request $request)
    {
        $craftsman = auth()->user();
        
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        $slots = AvailabilitySlot::where('user_id', $craftsman->id)
            ->forDate($validated['date'])
            ->orderBy('start_time')
            ->get();
        
        return response()->json([
            'slots' => $slots->map(fn($slot) => [
                'id' => $slot->id,
                'start_time' => Carbon::parse($slot->start_time)->format('H:i'),
                'end_time' => Carbon::parse($slot->end_time)->format('H:i'),
                'status' => $slot->status,
                'notes' => $slot->notes,
            ]),
        ]);
    }
}
