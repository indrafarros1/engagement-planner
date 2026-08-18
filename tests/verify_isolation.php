<?php
require '/opt/data/projects/engagement-planner/app/vendor/autoload.php';
$app = require '/opt/data/projects/engagement-planner/app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Activity;
use App\Models\BudgetItem;
use App\Models\EventProfile;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\SeserahanItem;
use App\Models\User;
use App\Models\Vendor;

$owner = User::where('email', 'owner@engagement.test')->first();
$partner = User::where('email', 'partner@lamaran.test')->first();
echo "owner id={$owner->id} | partner id={$partner->id} owner_id={$partner->owner_id}\n";

// 1) Tanpa login (CLI) → tanpa scope → semua data
echo "\n--- TANPA LOGIN (harus semua data) ---\n";
echo "activities=" . Activity::count() . " budget=" . BudgetItem::count() . " vendors=" . Vendor::count() . "\n";

// 2) Login sebagai OWNER
auth()->login($owner);
echo "\n--- LOGIN OWNER (id={$owner->id}) ---\n";
echo "activities=" . Activity::count() . " budget=" . BudgetItem::count() . " payments=" . Payment::count() . " vendors=" . Vendor::count() . " seserahan=" . SeserahanItem::count() . " guests=" . Guest::count() . " events=" . EventProfile::count() . "\n";
echo "Semua user_id=? " . (Activity::first()?->user_id ?? '-') . "\n";

// 3) Login sebagai PARTNER → membaca data owner (owner_id)
auth()->login($partner);
echo "\n--- LOGIN PARTNER (owner_id={$partner->owner_id}) ---\n";
echo "activities=" . Activity::count() . " budget=" . BudgetItem::count() . " vendors=" . Vendor::count() . " guests=" . Guest::count() . "\n";

// 4) Buat data sebagai partner → harus masuk ke ruang owner
$a = Activity::create(['name' => 'Test Isolasi Partner', 'category' => 'preparation', 'pic' => 'bersama', 'priority' => 'medium', 'status' => 'not_started']);
echo "\nPartner buat activity → user_id=" . $a->user_id . " (harus {$partner->owner_id})\n";
$a->forceDelete();

// 5) Akun BARU (register) → tidak ada data
$newbie = User::create(['name' => 'Akun Baru', 'email' => 'baru@test.app', 'password' => bcrypt('x'), 'role' => 'owner']);
auth()->login($newbie);
echo "\n--- LOGIN AKUN BARU (id={$newbie->id}) → HARUS KOSONG ---\n";
echo "activities=" . Activity::count() . " budget=" . BudgetItem::count() . " vendors=" . Vendor::count() . " events=" . EventProfile::count() . " guests=" . Guest::count() . "\n";
$newbie->forceDelete();

auth()->logout();
echo "\nDONE\n";
