<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_from_home', function (Blueprint $table) {
            $table->id();  // ID otomatis
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');  // Relasi ke karyawan
            $table->date('start_date');  // Tanggal mulai WFH
            $table->date('end_date');    // Tanggal selesai WFH
            $table->enum('status', ['Aktif', 'Selesai', 'Ditolak']);  // Status WFH
            $table->text('reason')->nullable();  // Alasan bekerja dari rumah
            $table->timestamps();  // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_from_home');
    }
};
