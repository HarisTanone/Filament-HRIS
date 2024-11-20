<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model ini
    protected $table = 'overtimes';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
        'status',
        'reason',
        'approved_by',
        'approved_at',
    ];

    // Menentukan relasi dengan model `Employee` (karyawan yang lembur)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Menentukan relasi dengan model `Employee` untuk approval (approved_by)
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    // Menentukan relasi dengan model `AttendanceType` untuk status lembur
    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class, 'status', 'type_name');
    }
}
