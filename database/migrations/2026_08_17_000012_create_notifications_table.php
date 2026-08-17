<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            // tabel sudah terlanjur dibuat dengan skema salah → perbaiki
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasColumn('notifications', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }
                if (! Schema::hasColumn('notifications', 'notifiable_id')) {
                    $table->morphs('notifiable');
                }
                if (Schema::hasColumn('notifications', 'type')) {
                    $table->string('type')->change();
                }
                if (Schema::hasColumn('notifications', 'data')) {
                    $table->text('data')->change();
                }
                if (Schema::hasColumn('notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable()->change();
                }
            });
            // tambah index bila belum ada
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
            });

            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('notifiable');
            $table->string('type');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
