<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_item_id')->constrained()->cascadeOnDelete();
            $table->string('type');                    // PaymentType: DP/Cicilan/Pelunasan/Refund/Koreksi
            $table->unsignedBigInteger('amount');      // Nominal (Rupiah int, > 0)
            $table->date('due_date')->nullable();      // Jatuh tempo
            $table->date('paid_date')->nullable();     // Tanggal bayar (null = belum bayar)
            $table->unsignedBigInteger('paid_amount')->nullable(); // Sebagian dibayar
            $table->string('method')->nullable();      // PaymentMethod
            $table->string('proof_path')->nullable();  // Bukti (file path)
            $table->text('notes')->nullable();
            $table->boolean('cancelled')->default(false); // Dibatalkan
            $table->timestamps();

            $table->index(['budget_item_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
