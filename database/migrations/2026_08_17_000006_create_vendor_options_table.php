<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('name');                    // Nama paket/penawaran
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);  // Harga penawaran (Rp)
            $table->boolean('selected')->default(false);      // Dipilih untuk acara
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_options');
    }
};