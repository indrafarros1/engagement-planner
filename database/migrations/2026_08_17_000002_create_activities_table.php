<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('preparation'); // ActivityCategory
            $table->string('pic')->default('bersama');          // Payer (CPP/CPW/Bersama/Lainnya)
            $table->date('due_date')->nullable();               // Deadline
            $table->string('priority')->default('medium');      // Priority
            $table->string('status')->default('not_started');   // ActivityStatus
            $table->boolean('archived')->default(false);        // Arsip (bukan hapus)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
