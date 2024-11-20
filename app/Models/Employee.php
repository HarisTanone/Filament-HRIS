<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'employee_id',
        'full_name',
        'email',
        'mobile_phone',
        'place_of_birth',
        'birthdate',
        'gender',
        'religion',
        'nik',
        'citizen_id_address',
        'residential_address',
        'join_date',
        'barcode',
        'user_id',
        'manager_id',
        'office_id',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan manajer (Employee yang bertindak sebagai manager)
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id'); // Manajer adalah seorang employee
    }

    // Relasi dengan karyawan yang dikelola (subordinates)
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id'); // Karyawan yang dikelola oleh manajer
    }

    // Menghasilkan barcode secara otomatis saat membuat karyawan baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->barcode = str_pad(rand(00000, 99999), 5, '0', STR_PAD_LEFT); // Menghasilkan barcode acak
        });
    }

    public function TimeOffApprover()
    {
        return $this->hasMany(TimeOff::class, 'approved_by');
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
    public function employee()
    {
        return $this->belongsTo(Schedule::class);
    }
}
