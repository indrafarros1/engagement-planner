<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('other');   // BudgetCategory
            $table->unsignedBigInteger('unit_price')->default(0); // Harga satuan (Rupiah int)
            $table->unsignedInteger('quantity')->default(1);      // Jumlah
            $table->unsignedBigInteger('contract_value')->nullable(); // Nilai kontrak (Rupiah int)
            $table->string('payer')->default('bersama');     // Payer (CPP/CPW/Bersama/Lainnya)
            $table->text('notes')->nullable();
            $table->boolean('archived')->default(false);     // Arsip (bukan hapus)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
