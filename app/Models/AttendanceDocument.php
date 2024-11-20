<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDocument extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan oleh model ini
    protected $table = 'attendance_documents';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'attendance_id',
        'document_type',
        'file_path',
    ];

    // Relasi dengan tabel `attendances`
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // Relasi untuk jenis dokumen
    public function documentType()
    {
        return $this->belongsTo(AttendanceType::class, 'document_type', 'type_name');
    }

    /**
     * Boot method untuk model AttendanceDocument
     */
    protected static function boot()
    {
        parent::boot();

        // Bisa ditambahkan kode untuk manipulasi data saat pembuatan jika diperlukan
    }
}
