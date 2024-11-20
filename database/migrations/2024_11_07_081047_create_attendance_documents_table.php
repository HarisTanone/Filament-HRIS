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
        Schema::create('attendance_documents', function (Blueprint $table) {
            $table->id();  // ID otomatis
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');  // Relasi ke tabel attendances
            $table->enum('document_type', ['Surat Sakit', 'Surat Cuti', 'Dokumen Lain']);  // Jenis dokumen
            $table->string('file_path');  // Path ke file dokumen
            $table->timestamps();  // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_documents');
    }
};
