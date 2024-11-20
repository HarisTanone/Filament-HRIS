<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model ini
    protected $table = 'work_from_home';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'status',
        'reason',
    ];

    // Menentukan relasi dengan model `Employee` (karyawan yang bekerja dari rumah)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Boot method untuk model WorkFromHome
     */
    protected static function boot()
    {
        parent::boot();

        // Bisa ditambahkan kode untuk manipulasi data saat pembuatan jika diperlukan
    }
}
