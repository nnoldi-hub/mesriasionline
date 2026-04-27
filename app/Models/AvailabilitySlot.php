<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AvailabilitySlot extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'appointment_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    /**
     * Get the craftsman who owns this slot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointment for this slot.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Check if slot is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if slot is in the past.
     */
    public function isPast(): bool
    {
        $slotDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
        return $slotDateTime->isPast();
    }

    /**
     * Check if slot can be booked.
     */
    public function canBeBooked(): bool
    {
        return $this->isAvailable() && !$this->isPast();
    }

    /**
     * Book this slot.
     */
    public function book(Appointment $appointment): bool
    {
        if (!$this->canBeBooked()) {
            return false;
        }

        $this->update([
            'status' => 'booked',
            'appointment_id' => $appointment->id,
        ]);

        return true;
    }

    /**
     * Block this slot.
     */
    public function block(?string $notes = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $this->update([
            'status' => 'blocked',
            'notes' => $notes,
        ]);

        return true;
    }

    /**
     * Release this slot.
     */
    public function release(): bool
    {
        $this->update([
            'status' => 'available',
            'appointment_id' => null,
            'notes' => null,
        ]);

        return true;
    }

    /**
     * Get formatted time display.
     */
    public function getTimeDisplayAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
    }

    /**
     * Scope for available slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for future slots.
     */
    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    /**
     * Scope for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope for a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Generate slots for a craftsman based on their weekly schedule.
     */
    public static function generateSlotsForCraftsman(User $craftsman, Carbon $startDate, Carbon $endDate): int
    {
        $weeklySchedule = $craftsman->weekly_schedule;
        $slotDuration = $craftsman->slot_duration_minutes ?? 60;
        $buffer = $craftsman->buffer_between_slots ?? 15;
        $vacationPeriods = $craftsman->vacation_periods ?? [];

        if (!$weeklySchedule) {
            return 0;
        }

        $slotsCreated = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayOfWeek = strtolower($currentDate->englishDayOfWeek);
            
            // Check if craftsman works on this day
            if (!isset($weeklySchedule[$dayOfWeek]) || !$weeklySchedule[$dayOfWeek]['active']) {
                $currentDate->addDay();
                continue;
            }

            // Check if date is in vacation period
            $isVacation = false;
            foreach ($vacationPeriods as $period) {
                $vacStart = Carbon::parse($period['start']);
                $vacEnd = Carbon::parse($period['end']);
                if ($currentDate->between($vacStart, $vacEnd)) {
                    $isVacation = true;
                    break;
                }
            }
            if ($isVacation) {
                $currentDate->addDay();
                continue;
            }

            $daySchedule = $weeklySchedule[$dayOfWeek];
            $workStart = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $daySchedule['start']);
            $workEnd = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $daySchedule['end']);

            // Generate slots for this day
            $slotStart = $workStart->copy();
            while ($slotStart->addMinutes($slotDuration) <= $workEnd) {
                $slotEnd = $slotStart->copy();
                
                // Check if slot already exists
                $exists = self::where('user_id', $craftsman->id)
                    ->where('date', $currentDate->toDateString())
                    ->where('start_time', $slotStart->copy()->subMinutes($slotDuration)->format('H:i:s'))
                    ->exists();

                if (!$exists) {
                    self::create([
                        'user_id' => $craftsman->id,
                        'date' => $currentDate->toDateString(),
                        'start_time' => $slotStart->copy()->subMinutes($slotDuration)->format('H:i:s'),
                        'end_time' => $slotEnd->format('H:i:s'),
                        'status' => 'available',
                    ]);
                    $slotsCreated++;
                }

                // Add buffer time
                $slotStart->addMinutes($buffer);
            }

            $currentDate->addDay();
        }

        return $slotsCreated;
    }
}
