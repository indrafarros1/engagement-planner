# TASKS — Engagement Planner (Fase 1–3)

> Sumber: brief AI PM + instruksi Bos 17 Agu 2026. **SEMUA fase selesai.**
> Lokasi: `/opt/data/projects/engagement-planner/` (app) — push GitHub `indrafarros1/engagement-planner` ✅

## STATUS: Fase 1–3 + Redesign + Ops — SELESAI ✅ (17 Agu 2026)

## Fase 1 (MVP) — SELESAI
- [x] T1.1 Laravel 13 + auth + Livewire 4 + Filament v5, branding engagement
- [x] T1.2 Profil acara lamaran
- [x] T1.3 Dashboard countdown + ringkasan + kegiatan + DP (klik→sumber)
- [x] T1.4 Kegiatan: CRUD + filter + sortir + arsip
- [x] T1.5 Item anggaran: satuan×jumlah, kontrak, penanggung
- [x] T1.6 DP & pembayaran: status otomatis + aturan 5.5
- [x] T1.7 Export CSV
- [x] T1.8 Desain cream/coral/navy + mobile 360px
- [x] T1.9 QA + akun demo (10 test hijau)

## Fase 2 — SELESAI
- [x] T2.1 Vendor + Pilihan/Penawaran + halaman Perbandingan (TERMURAH, Pilih, filter)
- [x] T2.2 Seserahan CRUD + statistik
- [x] T2.3 Lampiran bukti multi-file di pembayaran
- [x] T2.4 Impor Buku.xlsx (template + parsing + laporan)
- [x] T2.5 Export Excel .xlsx (anggaran & pembayaran)
- [x] T2.6 Reminder `reminders:generate` 07:00 WIB + dedup

## Fase 3 — SELESAI
- [x] T3.1 Akun Partner + role + izin + hak akses nominal (policy & mask)
- [x] T3.2 Activity log (observer + resource read-only)
- [x] T3.3 Notifikasi in-app (bell + database notifications)
- [x] T3.4 Tamu/keluarga CRUD + widget dashboard

## Redesign Kimi K3 — SELESAI
Bersih, lapang, tipografi Inter tegas, tanpa emoji, ikon heroicons, kartu/tabel rapi.

## Aturan Bisnis (5.5) — 10 test hijau
| # | Aturan | Status |
|---|---|---|
| 1 | total dibayar = pembayaran valid | ✅ |
| 2 | sisa = kontrak − dibayar | ✅ |
| 3 | terlambat = jatuh tempo < hari ini && sisa > 0 | ✅ |
| 4 | nominal > 0 | ✅ |
| 5 | bayar valid ≤ kontrak (koreksi via Koreksi/Refund) | ✅ |
| 6 | kegiatan selesai ≠ pembayaran otomatis | ✅ |
| 7 | nominal integer Rupiah | ✅ |

## Verifikasi Keluar
1. ✅ `php artisan test --filter=BusinessRulesTest` → 10 passed
2. ✅ Semua halaman 200 + screenshot desktop & mobile (screenshots/FINAL-*.png)
3. ✅ Impor Buku.xlsx (4 item) & Export Excel diuji via UI
4. ✅ Reminder & notifikasi dibuat (dedup bekerja)
5. ✅ Activity log terekam saat aksi UI
6. ✅ Partner: menu & nominal di-mask, rute anggaran 403
7. ✅ Push GitHub, live di Cloudflare tunnel
