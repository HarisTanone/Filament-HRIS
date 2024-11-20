<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Tentukan nama tabel, jika berbeda dengan penamaan default
    protected $table = 'schedules';

    // Tentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'employee_id',
        'shift_name',
        'start_time',
        'end_time',
        'week_day',
        'created_at',
        'updated_at',
        'is_active',
        'notes'
    ];

    // Tentukan kolom yang tidak bisa diisi massal
    protected $guarded = ['id'];

    // Tentukan format waktu
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan model Employee (karena setiap jadwal terkait dengan seorang karyawan)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
