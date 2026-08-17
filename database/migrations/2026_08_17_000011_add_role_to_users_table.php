<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('owner')->after('email'); // owner/partner
            $table->boolean('can_view_amounts')->default(true)->after('role');
            $table->string('partner_side')->nullable()->after('can_view_amounts'); // cpp/cpw
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_view_amounts', 'partner_side']);
        });
    }
};