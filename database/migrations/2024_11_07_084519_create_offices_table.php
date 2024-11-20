<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_name'); // Nama kantor
            $table->decimal('latitude', 10, 6); // Latitude (koordinat geografis)
            $table->decimal('longitude', 10, 6); // Longitude (koordinat geografis)
            $table->integer('radius'); // Radius kantor
            $table->string('description')->nullable(); // Ganti menjadi string
            $table->timestamps(); // Created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}
