<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';

    protected $fillable = [
        'employee_id',
        'office_id',
        'clock_in',
        'clock_out',
        'latitude',
        'longitude',
        'location_verified',
        'photo',
        'status',
        'latitude_out',
        'longitude_out',
        'photo_out',
        'schedule_id',
        'attendance_notes',
        'location_verified_clockOut'
    ];

    // Relasi dengan Schedule
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Menentukan relasi dengan model `Employee`
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Menentukan relasi dengan model `Office`
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    // Menentukan relasi dengan model `AttendanceType` (Status kehadiran)
    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class, 'status', 'type_name');
    }

    // Accessor untuk waktu masuk efektif
    public function getEffectiveClockInAttribute()
    {
        return $this->schedule
            ? $this->schedule->start_time
            : config('app.default_clock_in', env('DEFAULT_CLOCK_IN'));
    }

    // Method untuk mengecek keterlambatan
    public function isLate()
    {
        $scheduleStart = Carbon::parse($this->effective_clock_in)->format('H:i:s');
        $clockIn = Carbon::parse($this->clock_in)->format('H:i:s');
        return $clockIn && $clockIn > $scheduleStart;
    }

    // Method untuk menghitung selisih waktu keterlambatan
    public function getLateMinutesAttribute()
    {
        $scheduleStart = Carbon::parse($this->effective_clock_in);
        $clockIn = Carbon::parse($this->clock_in);

        $diffInMinutes = $scheduleStart->diffInMinutes($clockIn);

        $hours = intdiv($diffInMinutes, 60);
        $minutes = $diffInMinutes % 60;
        if ($hours == 0) {
            return "{$minutes} menit";
        }
        return "{$hours} jam {$minutes} menit";
    }

    // Method untuk menghasilkan keterangan otomatis
    public function generateAttendanceNotes(): string
    {
        $notes = [];

        if ($this->isLate()) {
            $notes[] = "Terlambat " . $this->late_minutes;
        }

        return !empty($notes) ? implode(', ', $notes) : '';
    }
}
