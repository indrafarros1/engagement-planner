# PRD — Engagement Planner (Preparasi Acara Lamaran)

> **Status:** Disetujui (ACC) oleh Bos — eksekusi Fase 1 (MVP) dimulai.
> **Versi:** 1.0 · Tanggal: 17 Agu 2026 · Source: brief AI PM (thread 1538942185628770388) — file asli tidak ditemukan di disk, direkonstruksi dari brief agar menjadi dokumen kerja resmi.

---

## 1. Ringkasan Produk

Aplikasi web **pribadi** untuk persiapan acara **LAMARAN** (bukan pernikahan). Dipakai oleh pasangan dan keluarga dekat untuk mengelola:

- Profil acara (siapa, kapan, di mana)
- Kegiatan/checklist persiapan
- Anggaran + pembayaran/DP
- Countdown menuju hari H

## 2. Target Pengguna

- Calon mempelai pria (CPP) & wanita (CPW)
- Keluarga dekat kedua belah pihak (akses bersama)
- Single private installation — bukan aplikasi publik

## 3. Stack Teknis (wajib dari brief)

| Layer | Pilihan |
|---|---|
| Framework | Laravel 13 |
| Komponen UI interaktif | Livewire 3 |
| Panel admin/CRUD | Filament 3 |
| View | Blade + Tailwind CSS |
| DB MVP | **SQLite** (lokal) |
| Arsitektur DB | Eloquent — siap migrasi ke MySQL/PostgreSQL |
| Timezone | Asia/Jakarta (WIB, UTC+7) |

## 4. Fase

- **Fase 1 (MVP):** 9 task di bawah — target 2–3 minggu, demo minggu ke-2.
- Fase berikutnya: (ditetapkan kemudian — tidak dalam scope Fase 1)

---

## 5. Fase 1 — Task List

### T1.1 Setup Laravel + auth + Filament
- Laravel 13 + autentikasi (login/register sederhana, multi-user)
- Filament panel terpasang, branding tema "engagement"
- SQLite untuk MVP, koneksi DB via Eloquent yang siap dialihkan

### T1.2 Profil Acara Lamaran
Field:
- Nama pasangan (CPP & CPW)
- Tanggal acara (hari H)
- Waktu mulai & selesai
- Lokasi (nama tempat) + alamat
- Estimasi jumlah tamu
- Catatan (opsional)
- Status persiapan

Single event (1 profil acara utama) untuk MVP.

### T1.3 Dashboard
- **Countdown** ke hari H (timezone Asia/Jakarta)
- Ringkasan anggaran: total anggaran / total dibayar / sisa / belum bayar
- Kegiatan dengan deadline **< 7 hari** & kegiatan **terlambat**
- **DP terdekat** (pembayaran dengan jatuh tempo terdekat & belum lunas)
- **Semua ringkasan klik → data sumber** (link ke halaman/laporan terkait)

### T1.4 Kegiatan / Checklist
- CRUD lengkap
- Filter: status, kategori, PIC, rentang tanggal, terlambat
- Sortir: deadline, prioritas
- **Arsip (bukan hapus)** — data tidak hilang

### T1.5 Item Anggaran
- CRUD
- Harga satuan × jumlah = **estimasi** (dihitung)
- **Nilai kontrak** (nominal riil yang disepakati)
- Penanggung: **CPP / CPW / Bersama / Lainnya**

### T1.6 DP & Pembayaran
- CRUD
- Jenis: **DP / Cicilan / Pelunasan / Refund / Koreksi**
- **Status otomatis:** Belum Bayar / Terlambat / Sebagian Dibayar / Lunas / Dibatalkan
- Metode pembayaran
- **Bukti** (upload lampiran)

### T1.7 Export CSV
- Export anggaran (per item + ringkasan)
- Export pembayaran

### T1.8 Desain UI
- Palet (PRD 13.2): **cream `#FFF8EF` / coral `#FF7A6B` / navy `#25233A`**
- Mobile-first, minimum **360px** lebar layar
- Label status **teks** (tidak hanya warna)
- Aksi utama mudah dijangkau (thumb-friendly)

### T1.9 QA + Akun Demo
- Verifikasi semua alur + aturan bisnis 5.5
- Akun demo siap untuk Bos mencoba

---

## 5.5 Aturan Bisnis (WAJIB)

1. **total dibayar** = jumlah nilai pembayaran **valid**
2. **sisa** = nilai kontrak − total dibayar
3. **terlambat** (pembayaran/kegiatan) = jatuh tempo **< hari ini** DAN sisa > 0 (belum lunas/belum selesai)
4. **nominal > 0** (tidak boleh 0 atau negatif untuk pembayaran valid)
5. Pembayaran valid **tidak boleh melebihi nilai kontrak** → koreksi lewat status **Koreksi/Refund**, tanpa menghapus histori
6. **Kegiatan selesai TIDAK otomatis membuat pembayaran**
7. Semua nominal **integer Rupiah** (tidak desimal)

### Status Otomatis Pembayaran (detail)
| Kondisi | Status |
|---|---|
| Dibatalkan manual | Dibatalkan |
| Belum ada bayar, jatuh tempo > hari ini | Belum Bayar |
| Belum ada bayar, jatuh tempo < hari ini | Terlambat |
| Sebagian dibayar (belum ≥ nilai) | Sebagian Dibayar |
| Terbayar ≥ nilai (atau dianggap lunas) | Lunas |

## 13. Desain (referensi 13.2)

### 13.2 Palet Warna
| Token | Hex | Fungsi |
|---|---|---|
| Cream | `#FFF8EF` | Latar utama |
| Coral | `#FF7A6B` | Aksi primer / aksen |
| Navy | `#25233A` | Teks utama / elemen gelap |

- Mobile-first 360px → pastikan tanpa overflow horizontal
- Status selalu ada teks label, warna hanya penguat
- Aksi utama (tombol tambah/Ubah/Simpan) besar & mudah dijangkau jempol

## 15. Non-Scope Fase 1
- Undangan digital / RSVP
- Multibahasa
- Notifikasi realtime push
- Integrasi pembayaran gateway (manual input saja)
