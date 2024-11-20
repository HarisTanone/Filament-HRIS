<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    // Tentukan nama tabel, jika berbeda dengan penamaan default
    protected $table = 'offices';

    // Tentukan kolom yang dapat diisi secara massal
    protected $fillable = [
        'office_name',
        'latitude',
        'longitude',
        'radius',
        'created_at',
        'updated_at',
        'description'
    ];

    // Tentukan kolom yang tidak bisa diisi massal
    protected $guarded = ['id'];

    // Tentukan format waktu
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan tabel Attendances (misalnya, jika ada banyak absensi yang terkait dengan kantor ini)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
