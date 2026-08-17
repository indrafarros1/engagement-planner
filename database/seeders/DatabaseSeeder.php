<?php

namespace Database\Seeders;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Enums\BudgetCategory;
use App\Enums\EventStatus;
use App\Enums\Payer;
use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\Priority;
use App\Models\Activity;
use App\Models\BudgetItem;
use App\Models\EventProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Akun demo =====
        $user = User::firstOrCreate(
            ['email' => 'demo@lamaran.test'],
            [
                'name' => 'Pasangan Demo',
                'password' => Hash::make('DemoLamaran2026!'),
            ]
        );
        $user2 = User::firstOrCreate(
            ['email' => 'keluarga@lamaran.test'],
            [
                'name' => 'Keluarga Demo',
                'password' => Hash::make('DemoLamaran2026!'),
            ]
        );

        // ===== Profil acara =====
        EventProfile::firstOrCreate(
            ['couple_a_name' => 'Raka', 'couple_b_name' => 'Nadia'],
            [
                'event_date' => now()->addDays(32)->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '13:00',
                'venue_name' => 'Rumah Keluarga Nadia',
                'venue_address' => 'Jl. Melati No. 12, Bandung',
                'estimated_guests' => 60,
                'notes' => 'Acara lamaran adat Sunda — siapkan siger & seserahan.',
                'status' => EventStatus::Planning->value,
            ]
        );

        // ===== Kegiatan =====
        $activities = [
            [
                'name' => 'Konfirmasi undangan keluarga besar CPP',
                'category' => ActivityCategory::FamilyMeeting->value,
                'pic' => Payer::CPP->value,
                'due_date' => now()->subDays(2)->format('Y-m-d'), // TERLAMBAT
                'priority' => Priority::High->value,
                'status' => ActivityStatus::InProgress->value,
            ],
            [
                'name' => 'Fitting baju adat CPP',
                'category' => ActivityCategory::Attire->value,
                'pic' => Payer::CPP->value,
                'due_date' => now()->addDays(3)->format('Y-m-d'), // < 7 hari
                'priority' => Priority::High->value,
                'status' => ActivityStatus::NotStarted->value,
            ],
            [
                'name' => 'Pesan katering seserahan',
                'category' => ActivityCategory::Catering->value,
                'pic' => Payer::Bersama->value,
                'due_date' => now()->addDays(5)->format('Y-m-d'), // < 7 hari
                'priority' => Priority::Medium->value,
                'status' => ActivityStatus::InProgress->value,
            ],
            [
                'name' => 'Cek seserahan & souvenir',
                'category' => ActivityCategory::Preparation->value,
                'pic' => Payer::CPW->value,
                'due_date' => now()->addDays(12)->format('Y-m-d'),
                'priority' => Priority::Medium->value,
                'status' => ActivityStatus::NotStarted->value,
            ],
            [
                'name' => 'Latihan adat & MC keluarga',
                'category' => ActivityCategory::Religious->value,
                'pic' => Payer::Bersama->value,
                'due_date' => now()->addDays(20)->format('Y-m-d'),
                'priority' => Priority::Low->value,
                'status' => ActivityStatus::NotStarted->value,
            ],
            [
                'name' => 'Booking dokumentasi',
                'category' => ActivityCategory::Documentation->value,
                'pic' => Payer::Bersama->value,
                'due_date' => now()->addDays(6)->format('Y-m-d'), // < 7 hari
                'priority' => Priority::High->value,
                'status' => ActivityStatus::NotStarted->value,
            ],
            [
                'name' => 'Distribusi undangan fisik',
                'category' => ActivityCategory::Invitation->value,
                'pic' => Payer::CPP->value,
                'due_date' => now()->subDay()->format('Y-m-d'), // TERLAMBAT
                'priority' => Priority::Medium->value,
                'status' => ActivityStatus::Done->value, // selesai walau lewat
            ],
            [
                'name' => 'Siapkan daftar tamu & meja',
                'category' => ActivityCategory::Preparation->value,
                'pic' => Payer::CPW->value,
                'due_date' => now()->addDays(15)->format('Y-m-d'),
                'priority' => Priority::Medium->value,
                'status' => ActivityStatus::NotStarted->value,
                'archived' => true, // contoh arsip
            ],
        ];
        foreach ($activities as $a) {
            Activity::firstOrCreate(['name' => $a['name']], $a);
        }

        // ===== Item anggaran + pembayaran =====
        $items = [
            [
                'name' => 'Cincin Lamaran',
                'category' => BudgetCategory::Ring->value,
                'unit_price' => 8_500_000, 'quantity' => 1,
                'contract_value' => 8_500_000,
                'payer' => Payer::CPP->value,
                'payments' => [
                    ['type' => PaymentType::DP->value, 'amount' => 3_000_000, 'due_date' => now()->subDays(10)->format('Y-m-d'), 'paid_date' => now()->subDays(10)->format('Y-m-d'), 'method' => PaymentMethod::Transfer->value], // Lunas DP
                    ['type' => PaymentType::Installment->value, 'amount' => 4_000_000, 'due_date' => now()->addDays(5)->format('Y-m-d'), 'paid_date' => now()->addDays(2)->format('Y-m-d'), 'paid_amount' => 2_000_000, 'method' => PaymentMethod::Transfer->value], // Sebagian
                    ['type' => PaymentType::Full->value, 'amount' => 1_500_000, 'due_date' => now()->addDays(30)->format('Y-m-d')], // Belum bayar
                ],
            ],
            [
                'name' => 'Katering Seserahan',
                'category' => BudgetCategory::Catering->value,
                'unit_price' => 75_000, 'quantity' => 60,
                'contract_value' => 4_500_000,
                'payer' => Payer::CPW->value,
                'payments' => [
                    // DP sesuai kontrak satuan×jumlah = 4.5jt
                    ['type' => PaymentType::DP->value, 'amount' => 2_000_000, 'due_date' => now()->subDays(3)->format('Y-m-d'), 'paid_date' => now()->subDays(3)->format('Y-m-d'), 'method' => PaymentMethod::Transfer->value], // lunas DP
                    ['type' => PaymentType::Installment->value, 'amount' => 2_500_000, 'due_date' => now()->addDays(4)->format('Y-m-d')], // belum bayar, terdekat
                ],
            ],
            [
                'name' => 'Dekorasi Pelaminan',
                'category' => BudgetCategory::Decoration->value,
                'unit_price' => 6_000_000, 'quantity' => 1,
                'contract_value' => 6_000_000,
                'payer' => Payer::Bersama->value,
                'payments' => [
                    ['type' => PaymentType::DP->value, 'amount' => 1_500_000, 'due_date' => now()->subDays(6)->format('Y-m-d'), 'paid_date' => now()->subDays(6)->format('Y-m-d'), 'method' => PaymentMethod::Cash->value],
                    // Terlambat: cicilan jatuh tempo kemarin, belum dibayar
                    ['type' => PaymentType::Installment->value, 'amount' => 2_000_000, 'due_date' => now()->subDay()->format('Y-m-d')],
                ],
            ],
            [
                'name' => 'Dokumentasi Foto & Video',
                'category' => BudgetCategory::Documentation->value,
                'unit_price' => 3_500_000, 'quantity' => 1,
                'contract_value' => 3_500_000,
                'payer' => Payer::Bersama->value,
                'payments' => [
                    ['type' => PaymentType::DP->value, 'amount' => 3_500_000, 'due_date' => now()->addDays(6)->format('Y-m-d'), 'paid_date' => now()->subDay()->format('Y-m-d'), 'method' => PaymentMethod::Transfer->value], // lunas DP (full even though due future)
                ],
            ],
            [
                'name' => 'Souvenir Tamu',
                'category' => BudgetCategory::Other->value,
                'unit_price' => 20_000, 'quantity' => 60,
                'contract_value' => null, // belum kontrak → pakai estimasi 1.2jt
                'payer' => Payer::CPP->value,
                'payments' => [],
            ],
            [
                'name' => 'Siger & Aksesori Adat',
                'category' => BudgetCategory::Attire->value,
                'unit_price' => 2_000_000, 'quantity' => 1,
                'contract_value' => 2_000_000,
                'payer' => Payer::CPW->value,
                'payments' => [
                    ['type' => PaymentType::Full->value, 'amount' => 2_000_000, 'due_date' => now()->subDays(8)->format('Y-m-d'), 'paid_date' => now()->subDays(8)->format('Y-m-d'), 'method' => PaymentMethod::Transfer->value], // Lunas
                ],
                'archived' => true, // contoh arsip
            ],
        ];

        foreach ($items as $item) {
            $payments = $item['payments'] ?? [];
            unset($item['payments']);

            $bi = BudgetItem::firstOrCreate(['name' => $item['name']], $item);
            foreach ($payments as $p) {
                Payment::firstOrCreate(
                    [
                        'budget_item_id' => $bi->id,
                        'type' => $p['type'],
                        'amount' => $p['amount'],
                    ],
                    $p + ['budget_item_id' => $bi->id]
                );
            }
        }
    }
}

// ===== Akun Partner (Fase 3) =====
$partner = User::firstOrCreate(
    ['email' => 'partner@lamaran.test'],
    [
        'name' => 'Partner (Nadia)',
        'password' => \Illuminate\Support\Facades\Hash::make('PartnerLamaran2026!'),
        'role' => \App\Enums\UserRole::Partner->value,
        'partner_side' => \App\Enums\Payer::CPW->value,
        'can_view_amounts' => false, // contoh: partner tanpa akses nominal
    ]
);

// ===== Vendor + Penawaran (Fase 2) =====
$vendors = [
    [
        'name' => 'Griya Dekor Mayang',
        'category' => \App\Enums\BudgetCategory::Decoration->value,
        'contact_name' => 'Bu Mayang', 'contact_phone' => '0812-3456-7890',
        'address' => 'Jl. Asia Afrika No. 88, Bandung',
        'options' => [
            ['name' => 'Paket Pelaminan Dasar', 'price' => 4_500_000, 'description' => 'Pelaminan + backdrop + 2 janur', 'selected' => false],
            ['name' => 'Paket Pelaminan Premium', 'price' => 6_000_000, 'description' => 'Pelaminan, backdrop, janur, karpet, dekor meja', 'selected' => true],
            ['name' => 'Paket Lengkap Royal', 'price' => 8_500_000, 'description' => 'Semua premium + dekor tamu + lighting', 'selected' => false],
        ],
    ],
    [
        'name' => 'Katering Selerasa',
        'category' => \App\Enums\BudgetCategory::Catering->value,
        'contact_name' => 'Pak Rasa', 'contact_phone' => '0821-1111-2222',
        'address' => 'Jl. Setiabudi No. 5, Bandung',
        'options' => [
            ['name' => 'Paket Nasi Box 75K', 'price' => 4_500_000, 'description' => 'Nasi box @Rp75.000 x 60', 'selected' => false],
            ['name' => 'Paket Prasmanan 60K', 'price' => 3_600_000, 'description' => 'Prasmanan @Rp60.000 x 60', 'selected' => true],
        ],
    ],
    [
        'name' => 'Studio Foto Melati',
        'category' => \App\Enums\BudgetCategory::Documentation->value,
        'contact_name' => 'Mas Ari', 'contact_phone' => '0877-4444-5555',
        'address' => 'Jl. Dago No. 12, Bandung',
        'options' => [
            ['name' => 'Paket Prewedding + Hari H', 'price' => 3_500_000, 'description' => 'Prewedding, dokumentasi acara, 2 videografer', 'selected' => true],
            ['name' => 'Paket Golden', 'price' => 5_000_000, 'description' => 'Plus softcopy semua + drone', 'selected' => false],
        ],
    ],
];
foreach ($vendors as $v) {
    $options = $v['options'];
    unset($v['options']);
    $vendor = \App\Models\Vendor::firstOrCreate(['name' => $v['name']], $v);
    foreach ($options as $o) {
        \App\Models\VendorOption::firstOrCreate(
            ['vendor_id' => $vendor->id, 'name' => $o['name']],
            $o + ['vendor_id' => $vendor->id]
        );
    }
}

// ===== Seserahan (Fase 2) =====
$seserahan = [
    ['name' => 'Kain Batik & Kebaya', 'quantity' => 1, 'unit_price' => 1_200_000, 'status' => 'done', 'pic' => 'cpw'],
    ['name' => 'Kue Tradisional (Kue Serabi dkk)', 'quantity' => 10, 'unit_price' => 50_000, 'status' => 'preparing', 'pic' => 'bersama'],
    ['name' => 'Gelang & Kalung Emas', 'quantity' => 1, 'unit_price' => 2_500_000, 'status' => 'planned', 'pic' => 'cpp'],
    ['name' => 'Alat Rias (Seserahan)', 'quantity' => 1, 'unit_price' => 750_000, 'status' => 'planned', 'pic' => 'cpw'],
    ['name' => 'Peralatan Makan & Kelamin', 'quantity' => 1, 'unit_price' => 900_000, 'status' => 'planned', 'pic' => 'bersama'],
];
foreach ($seserahan as $s) {
    \App\Models\SeserahanItem::firstOrCreate(['name' => $s['name']], $s);
}

// ===== Tamu/Keluarga (Fase 3) =====
$guests = [
    ['name' => 'Keluarga Bapak Raka', 'group' => 'cpp', 'total_people' => 12, 'status' => 'confirmed'],
    ['name' => 'Keluarga Ibu Raka', 'group' => 'cpp', 'total_people' => 8, 'status' => 'confirmed'],
    ['name' => 'Keluarga Bapak Nadia', 'group' => 'cpw', 'total_people' => 15, 'status' => 'confirmed'],
    ['name' => 'Keluarga Ibu Nadia', 'group' => 'cpw', 'total_people' => 10, 'status' => 'unknown'],
    ['name' => 'Kerabat dekat CPP', 'group' => 'cpp', 'total_people' => 6, 'status' => 'unknown'],
    ['name' => 'Kerabat dekat CPW', 'group' => 'cpw', 'total_people' => 5, 'status' => 'invited'],
];
foreach ($guests as $g) {
    \App\Models\Guest::firstOrCreate(['name' => $g['name']], $g);
}
