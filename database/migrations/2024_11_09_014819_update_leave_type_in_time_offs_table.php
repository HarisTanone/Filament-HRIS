<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_offs', function (Blueprint $table) {
            // Cek apakah kolom 'leave_type' ada sebelum mencoba menghapusnya
            if (Schema::hasColumn('time_offs', 'leave_type')) {
                $table->dropColumn('leave_type');
            }

            // Cek apakah kolom 'attendance_type_id' sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('time_offs', 'attendance_type_id')) {
                $table->foreignId('attendance_type_id')->constrained('attendance_types')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_offs', function (Blueprint $table) {
            // Kembalikan kolom attendance_type_id dengan menghapus foreign key dan kolomnya
            if (Schema::hasColumn('time_offs', 'attendance_type_id')) {
                $table->dropForeign(['attendance_type_id']); // Hapus foreign key constraint
                $table->dropColumn('attendance_type_id'); // Hapus kolom attendance_type_id
            }

            // Jika kolom 'leave_type' sebelumnya ada, tambahkan kembali kolom tersebut
            if (!Schema::hasColumn('time_offs', 'leave_type')) {
                $table->string('leave_type')->nullable(); // Sesuaikan tipe data jika perlu
            }
        });
    }
};
