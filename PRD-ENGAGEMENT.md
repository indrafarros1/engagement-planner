# PRD — Engagement Planner (Preparasi Acara Lamaran)

> **Status:** SELESAI semua fase (1–3) + redesign Kimi K3 — 17 Agu 2026.
> **Versi:** 3.0 · Source: brief AI PM + instruksi Bos (thread 1538942185628770388) — file asli tidak ditemukan, direkonstruksi dari brief agar menjadi dokumen kerja resmi.

---

## 1. Ringkasan Produk

Aplikasi web **pribadi** untuk persiapan acara **LAMARAN** (bukan pernikahan). Dipakai oleh pasangan, keluarga dekat, dan tim penyedia (vendor) untuk mengelola:

- Profil acara (siapa, kapan, di mana)
- Kegiatan/checklist persiapan
- Vendor & perbandingan penawaran
- Seserahan adat
- Anggaran + pembayaran/DP + lampiran bukti
- Tamu/keluarga
- Reminder & notifikasi
- Activity log & hak akses per pengguna

## 2. Target Pengguna

- **Owner** — akses penuh (semua fitur + manajemen akun)
- **Partner** — pasangan (CPP/CPW); nominal anggaran disembunyikan kecuali diizinkan
- Keluarga dekat & vendor — akses terbatas sesuai peran

## 3. Stack Teknis (wajib)

| Layer | Pilihan |
|---|---|
| Framework | Laravel 13 |
| Komponen UI interaktif | Livewire 4 |
| Panel admin/CRUD | Filament 5 |
| View | Blade + Tailwind CSS |
| DB | **MySQL** (MariaDB, server tim) — arsitektur siap migrasi PostgreSQL/SQLite |
| Spreadsheet | PhpSpreadsheet (impor/export .xlsx) |
| Timezone | Asia/Jakarta (WIB, UTC+7) |

## 4. Fase

- **Fase 1 (MVP):** fondasi + profil + dashboard + kegiatan + anggaran + pembayaran + export CSV + desain + QA — **SELESAI**
- **Fase 2:** vendor + pilihan/perbandingan, seserahan, lampiran bukti, impor Buku.xlsx, export Excel, reminder — **SELESAI**
- **Fase 3:** akun Partner + izin + hak akses nominal, activity log, notifikasi, tamu/keluarga — **SELESAI**

---

## 5. Fase 1 — Task List

### T1.1 Setup Laravel + auth + Filament ✅
Laravel 13 + auth (login/register), Filament v5 panel branding tema engagement, MySQL.

### T1.2 Profil Acara Lamaran ✅
Field: nama pasangan · tanggal · waktu mulai/selesai · lokasi+alamat · estimasi tamu · catatan · status persiapan.

### T1.3 Dashboard ✅
Countdown Asia/Jakarta · total anggaran/dibayar/sisa/belum bayar · kegiatan <7 hari & terlambat · DP terdekat — **semua ringkasan klik → data sumber**.

### T1.4 Kegiatan/Checklist ✅
CRUD · filter (status/kategori/PIC/rentang tanggal/terlambat) · sortir (deadline/prioritas) · **arsip (bukan hapus)**.

### T1.5 Item Anggaran ✅
CRUD · harga satuan × jumlah = estimasi · nilai kontrak · penanggung CPP/CPW/Bersama/Lainnya · semua integer Rupiah.

### T1.6 DP & Pembayaran ✅
CRUD · jenis (DP/Cicilan/Pelunasan/Refund/Koreksi) · status otomatis · metode · bukti upload · validasi aturan 5.5.

### T1.7 Export CSV ✅
Export anggaran & pembayaran (CSV).

### T1.8 Desain UI ✅
Palet cream/coral/navy, mobile-first 360px, label status berteks.

### T1.9 QA + Akun Demo ✅
10 unit test aturan 5.5 hijau · akun demo · verifikasi browser desktop+mobile.

## 5b. Fase 2 — Task List

### T2.1 Vendor + Pilihan + Perbandingan ✅
- **Vendor**: CRUD (nama, kategori, kontak, alamat, catatan, arsip)
- **Pilihan/Penawaran** per vendor: nama paket, deskripsi, harga, status terpilih (1 paket per vendor)
- **Perbandingan Vendor**: halaman khusus — filter kategori/vendor, tabel penawaran terurut harga, badge **TERMURAH** per vendor, tombol "Pilih", status "Terpilih"

### T2.2 Seserahan ✅
CRUD item seserahan: nama, jumlah, perkiraan harga, status (Direncanakan/Sedang Disiapkan/Siap), penanggung, catatan, widget statistik.

### T2.3 Lampiran Bukti ✅
Relation manager di pembayaran: unggah banyak file (gambar/PDF), unduh per lampiran, keterangan.

### T2.4 Impor Buku.xlsx ✅
Action "Impor Buku.xlsx": upload file Excel → parsing kolom (Item/Kategori/Harga Satuan/Jumlah/Penanggung/Kontrak/Catatan) → buat item anggaran. Template disediakan ("Download Template"), pelaporan jumlah berhasil/gagal.

### T2.5 Export Excel ✅
Export .xlsx (PhpSpreadsheet) untuk anggaran & pembayaran (di samping CSV). Kolom uang terformat angka.

### T2.6 Reminder ✅
Command `reminders:generate` (dijadwalkan tiap 07:00 WIB) — menghasilkan notifikasi untuk pembayaran jatuh tempo hari ini/terlambat dan kegiatan deadline hari ini/terlambat. Deduplikasi berlaku (tidak membuat ganda yang belum dibaca).

## 5c. Fase 3 — Task List

### T3.1 Akun Partner + Izin + Hak Akses Nominal ✅
- Kolom users: `role` (owner/partner), `partner_side` (cpp/cpw), `can_view_amounts`
- Resource manajemen akun (hanya owner): CRUD, password opsional saat edit
- **Hak akses nominal**: partner tanpa izin → menu/ routes anggaran & pembayaran tersembunyi (403), dashboard menampilkan pesan "nominal disembunyikan", harga vendor & seserahan tampil "•••"
- Partner berizin (can_view_amounts=true) → akses penuh nominal

### T3.2 Activity Log ✅
Observer terdaftar eksplisit (Laravel 13 tidak auto-discover) → mencatat created/updated/deleted untuk Kegiatan, Anggaran, Pembayaran, Vendor, Seserahan, Tamu. Resource read-only: waktu, pengguna, aksi, deskripsi, entitas, IP + filter aksi.

### T3.3 Notifikasi ✅
Laravel database notifications + bell di topbar (polling 30 detik). Notifikasi reminder (Fase 2) dikirim ke semua pengguna, dengan link ke record.

### T3.4 Tamu/Keluarga ✅
CRUD tamu: nama keluarga, sisi (CPP/CPW), jumlah orang, status undangan (Diundang/Konfirmasi Hadir/Tidak Hadir/Belum Konfirmasi), telepon, catatan. Widget dashboard: total kelompok/orang, konfirmasi, belum, tidak hadir.

---

## 5.5 Aturan Bisnis (WAJIB) — teruji 10 test

1. **total dibayar** = jumlah nilai pembayaran **valid**
2. **sisa** = nilai kontrak − total dibayar
3. **terlambat** (pembayaran/kegiatan) = jatuh tempo **< hari ini** DAN sisa > 0
4. **nominal > 0** (validasi + aturan impor)
5. Pembayaran valid **tidak boleh melebihi kontrak** → koreksi lewat **Koreksi/Refund**, tanpa hapus histori
6. **Kegiatan selesai TIDAK otomatis membuat pembayaran**
7. Semua nominal **integer Rupiah**

## 13. Desain (13.2 — gaya Kimi K3)

### 13.2 Palet Warna
| Token | Hex | Fungsi |
|---|---|---|
| Cream | `#FFF8EF` | Latar utama |
| Coral | `#FF7A6B` | Aksi primer / aksen |
| Navy | `#25233A` | Teks utama / sidebar |

**Penyajian gaya Kimi K3:** bersih & lapang, tipografi tegas (Inter 400–800), spacing konsisten, kartu/tabel rapi, tanpa dekorasi/emoji berlebihan. Ikon garis (heroicons). Mobile-first 360px tanpa overflow. Status selalu label teks — warna penguat. Angka keuangan pakai `tabular-nums`.

## 15. Non-Scope
- Undangan digital / RSVP online
- Integrasi pembayaran gateway (manual input)
- Multibahasa / notifikasi push realtime (cukup in-app)
