<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceType extends Model
{
    use HasFactory;

    // Tentukan nama tabel, jika berbeda dengan penamaan default
    protected $table = 'attendance_types';

    // Tentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'type_name',
        'created_at',
        'updated_at',
    ];

    // Tentukan kolom yang tidak bisa diisi massal
    protected $guarded = ['id'];

    // Tentukan format waktu
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi jika dibutuhkan, misalnya relasi dengan tabel `attendances`
    public function timeOffs()
    {
        return $this->hasMany(TimeOff::class, 'attendance_type_id');
    }
}
