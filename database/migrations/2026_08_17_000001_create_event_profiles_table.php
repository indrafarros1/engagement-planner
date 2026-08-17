<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('couple_a_name');                 // Nama calon mempelai pria (CPP)
            $table->string('couple_b_name');                 // Nama calon mempelai wanita (CPW)
            $table->date('event_date');                      // Tanggal acara lamaran
            $table->string('start_time')->nullable();        // Waktu mulai (H:i)
            $table->string('end_time')->nullable();          // Waktu selesai (H:i)
            $table->string('venue_name')->nullable();        // Nama lokasi
            $table->text('venue_address')->nullable();       // Alamat lokasi
            $table->unsignedInteger('estimated_guests')->nullable(); // Estimasi tamu
            $table->text('notes')->nullable();               // Catatan
            $table->string('status')->default('planning');   // EventStatus
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_profiles');
    }
};
