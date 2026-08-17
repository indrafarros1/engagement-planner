<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // Nama kepala keluarga/kelompok
            $table->string('group')->default('cpw'); // GuestGroup: CPP/CPW
            $table->unsignedInteger('total_people')->default(1); // jumlah orang
            $table->string('status')->default('invited'); // invited/confirmed/declined/unknown
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};