# 💍 Lamaran Planner — Engagement Planner

Web pribadi persiapan acara **lamaran** (bukan pernikahan) — dipakai pasangan & keluarga dekat untuk mengelola profil acara, kegiatan/checklist, anggaran, dan DP/pembayaran.

**Stack:** Laravel 13 · Livewire 4 · Filament 5 · Blade + Tailwind CSS · MySQL (arsitektur siap migrasi PostgreSQL/SQLite)

## Fitur Fase 1 (MVP)

| # | Fitur | Status |
|---|---|---|
| T1.1 | Setup Laravel + auth + Filament, branding tema engagement | ✅ |
| T1.2 | Profil acara lamaran (pasangan, tanggal, waktu, lokasi, tamu, catatan, status) | ✅ |
| T1.3 | Dashboard: countdown Asia/Jakarta + total anggaran/dibayar/sisa/belum bayar + kegiatan <7 hari & terlambat + DP terdekat (semua klik → sumber) | ✅ |
| T1.4 | Kegiatan: CRUD + filter (status/kategori/PIC/rentang tanggal/terlambat) + sortir (deadline/prioritas) + arsip (bukan hapus) | ✅ |
| T1.5 | Item anggaran: CRUD — satuan×jumlah=estimasi, nilai kontrak, penanggung CPP/CPW/Bersama/Lainnya | ✅ |
| T1.6 | DP & pembayaran: CRUD — jenis (DP/Cicilan/Pelunasan/Refund/Koreksi), status otomatis, metode, bukti | ✅ |
| T1.7 | Export CSV anggaran & pembayaran | ✅ |
| T1.8 | Desain: palet cream #FFF8EF / coral #FF7A6B / navy #25233A, mobile-first 360px, label status berteks | ✅ |
| T1.9 | QA: 10 unit test aturan bisnis 5.5 hijau + akun demo | ✅ |

## Aturan Bisnis (PRD 5.5) — teruji otomatis

1. Total dibayar = jumlah pembayaran valid
2. Sisa = kontrak − dibayar
3. Terlambat = jatuh tempo < hari ini DAN sisa > 0
4. Nominal > 0
5. Pembayaran valid tidak boleh melebihi kontrak → koreksi via **Koreksi/Refund** (tanpa hapus histori)
6. Kegiatan selesai ≠ pembayaran otomatis
7. Semua nominal integer Rupiah

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
# Siapkan DB MySQL: engagement_planner
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Akses: `http://localhost:8000/admin`

**Akun demo:**
- Email: `demo@lamaran.test`
- Password: `DemoLamaran2026!`

## Test

```bash
php artisan test --filter=BusinessRulesTest   # 10 test aturan 5.5
```

## Desain (PRD 13.2)

- Cream `#FFF8EF` latar · Coral `#FF7A6B` aksi primer · Navy `#25233A` sidebar/teks
- Mobile-first (minimum 360px), tanpa overflow horizontal
- Status selalu berlabel teks — warna hanya penguat

## Struktur

```
app/Enums/                         # EventStatus, PaymentStatus, PaymentType, dll
app/Models/                        # EventProfile, Activity, BudgetItem, Payment
app/Filament/Resources/            # Resource CRUD + widget statistik
app/Filament/Widgets/              # Overview countdown, kegiatan, DP terdekat
resources/views/filament/widgets/  # View Blade dashboard
database/seeders/DatabaseSeeder.php # Akun demo + data contoh
tests/Feature/BusinessRulesTest.php # Verifikasi aturan 5.5
```

## PRD & TASKS

- `PRD-ENGAGEMENT.md` — dokumen produk (direkonstruksi dari brief PM karena file asli tidak ada di disk)
- `TASKS.md` — checklist pengerjaan