# TASKS — Engagement Planner Fase 1 (MVP)

> Sumber: brief AI PM 17 Agu 2026. Target 2–3 minggu, demo minggu ke-2.
> Lokasi kerja: `/opt/data/projects/engagement-planner/` (app) — **dipush ke GitHub `indrafarros1/engagement-planner` (master) ✅**

## Milestone

- **M1:** T1.1–T1.3 (setup + auth + Filament, profil acara, dashboard) — **SELESAI 17 Agu 2026**
- **M2:** T1.4–T1.6 (kegiatan, anggaran, pembayaran) — **SELESAI 17 Agu 2026**
- **M3:** T1.7–T1.9 (export, desain, QA + akun demo) — **SELESAI 17 Agu 2026**

## Checklist

### M1 — Setup & Fondasi

- [x] T1.1 Laravel 13 + auth + Livewire + Filament v5, branding tema engagement (navy/coral/cream)
- [x] T1.2 Profil acara lamaran (CRUD, 1 event utama)
- [x] T1.3 Dashboard: countdown + ringkasan anggaran + kegiatan <7hr/terlambat + DP terdekat, semuanya klik→sumber

### M2 — Data Inti

- [x] T1.4 Kegiatan: CRUD + filter (status/kategori/PIC/tanggal/terlambat) + sortir (deadline/prioritas) + arsip
- [x] T1.5 Item anggaran: CRUD — satuan×jumlah=estimasi, nilai kontrak, penanggung CPP/CPW/Bersama/Lainnya
- [x] T1.6 DP & pembayaran: CRUD — jenis (DP/Cicilan/Pelunasan/Refund/Koreksi), status otomatis, metode, bukti (upload)

### M3 — Export, Desain, QA

- [x] T1.7 Export CSV anggaran & pembayaran (tombol hijau di header list)
- [x] T1.8 Desain: palet cream/coral/navy, mobile-first 360px, label status teks
- [x] T1.9 QA + aturan 5.5 + akun demo (demo@lamaran.test / DemoLamaran2026! — 10 test hijau)

## Aturan Bisnis (5.5) — titik cek QA

| # | Aturan | Status |
|---|---|---|
| 1 | total dibayar = jumlah pembayaran valid | ✅ test hijau |
| 2 | sisa = kontrak − dibayar | ✅ test hijau |
| 3 | terlambat = jatuh tempo < hari ini && sisa > 0 | ✅ test hijau |
| 4 | nominal > 0 | ✅ test hijau |
| 5 | bayar valid ≤ kontrak (koreksi via Koreksi/Refund) | ✅ test hijau |
| 6 | kegiatan selesai ≠ pembayaran otomatis | ✅ test hijau |
| 7 | nominal integer Rupiah | ✅ test hijau |

## Verifikasi Keluar (Definition of Done Fase 1) — SEMUA TERPENUHI

1. ✅ `php artisan test --filter=BusinessRulesTest` → 10 passed (18 assertions)
2. ✅ Screenshot semua halaman (desktop + mobile 360px) di `screenshots/`
3. ✅ Semua aturan 5.5 teruji otomatis
4. ✅ Akun demo login OK (desktop + mobile + via tunnel)
5. ✅ Push GitHub `indrafarros1/engagement-planner` (master, tanpa .env/secret bocor)
6. ✅ Live di Cloudflare tunnel: `https://joins-amongst-candles-operator.trycloudflare.com` (URL sementara — ganti tiap restart)