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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id',36)->default(DB::raw('uuid()'));
            $table->string('full_name', 80);
            $table->string('email',40);
            $table->string('mobile_phone',15);
            $table->string('place_of_birth');
            $table->date('birthdate');
            $table->enum('gender', array('Male', 'Female'));
            $table->string('religion');
            $table->string('nik',20);
            $table->string('citizen_id_address');
            $table->string('residential_address');
            $table->date('join_date');
            $table->string('barcode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
