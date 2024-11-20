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
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->timestamp('start_time')->nullable();  // Mengizinkan start_time bernilai NULL
            $table->timestamp('end_time')->nullable();    // Mengizinkan end_time bernilai NULL
            $table->decimal('total_hours', 5, 2);          // Total jam lembur
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak']);
            $table->text('reason')->nullable();            // Alasan lembur
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtimes');
    }
};
