<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeOff extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model ini
    protected $table = 'time_offs';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'attendance_type_id', // Perbarui menjadi attendance_type_id
        'reason',
        'status',
        'document',
        'approved_by',
        'approved_at',
    ];

    // Menentukan relasi dengan model `Employee`
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Menentukan relasi dengan model `Employee` untuk approval (approved_by)
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    // Menentukan relasi dengan model `AttendanceType` untuk status cuti
    // Relasi ke model AttendanceType
    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class, 'attendance_type_id');
    }
}
