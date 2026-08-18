<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Semua tabel data milik owner (user_id = siapa yang memiliki event ini).
        $tables = [
            'event_profiles', 'activities', 'budget_items', 'payments',
            'vendors', 'vendor_options', 'seserahan_items', 'guests',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (! Schema::hasColumn($table, 'user_id')) {
                    $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                    $t->index(['user_id']);
                }
            });
        }

        // Lampiran pembayaran: user_id langsung (biar mudah disaring + konsisten).
        if (! Schema::hasColumn('payment_attachments', 'user_id')) {
            Schema::table('payment_attachments', function (Blueprint $t) {
                $t->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
                $t->index(['user_id']);
            });
        }

        // Partner terhubung ke Owner (owner_id di users).
        if (! Schema::hasColumn('users', 'owner_id')) {
            Schema::table('users', function (Blueprint $t) {
                $t->foreignId('owner_id')->nullable()->after('id')
                    ->constrained('users')->nullOnDelete();
                $t->index(['owner_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'event_profiles', 'activities', 'budget_items', 'payments',
            'vendors', 'vendor_options', 'seserahan_items', 'guests', 'payment_attachments',
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (Schema::hasColumn($table, 'user_id')) {
                    $t->dropConstrainedForeignId('user_id');
                }
            });
        }
        if (Schema::hasColumn('users', 'owner_id')) {
            Schema::table('users', function (Blueprint $t) {
                $t->dropConstrainedForeignId('owner_id');
            });
        }
    }
};
