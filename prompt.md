# 📬 Plan Aplikasi Persuratan dengan Disposisi

> Dokumen ini adalah **project plan + prompt context** untuk membangun Aplikasi Persuratan Digital (E-Surat) dengan fitur Disposisi. Gunakan file ini sebagai context di Gemini, Antigravity, Cursor, atau AI tools lainnya.

---

## 🎯 Tujuan Aplikasi

Membangun sistem persuratan digital yang menggantikan proses manual, mencakup:

- Manajemen surat masuk & keluar
- Disposisi surat oleh pimpinan
- Penomoran otomatis
- Pelacakan status surat real-time
- Arsip digital yang mudah dicari

---

## 🧩 Modul Utama

### 1. Surat Masuk

- Input surat masuk (scan/upload PDF + metadata)
- Metadata: nomor surat, tanggal, pengirim, perihal, klasifikasi
- Status: `Diterima → Dibaca → Didisposisi → Selesai`
- Notifikasi ke penerima / unit terkait

### 2. Surat Keluar

- Buat surat dari template
- Kolaborasi & revisi draft (multi-user)
- Penomoran otomatis sesuai format instansi
- Approval berjenjang sebelum dikirim
- Tanda tangan elektronik (TTE)
- Arsip otomatis setelah dikirim

### 3. Disposisi Surat

- Pimpinan meneruskan surat ke unit/staf dengan instruksi
- Form disposisi: tujuan, instruksi, batas waktu, catatan
- Status disposisi: `Belum Diproses → Sedang Diproses → Selesai`
- Rantai disposisi (disposisi bisa diteruskan ke bawah)
- Notifikasi real-time (email + push notification)

### 4. Draft & Template Surat

- Buat & simpan template surat resmi
- Kolaborasi real-time pada draft (seperti Google Docs)
- Riwayat revisi
- Auto-fill variabel (nama, tanggal, nomor, jabatan)

### 5. Arsip & Pencarian

- Semua surat tersimpan otomatis
- Pencarian berdasarkan: nomor, perihal, pengirim, tanggal, kata kunci
- Filter per unit kerja, tahun, klasifikasi
- Export ke PDF

### 6. Dashboard & Laporan

- Statistik surat masuk/keluar per periode
- Status disposisi yang pending
- Kinerja respons tiap unit
- Grafik tren persuratan

---

## 👥 Peran Pengguna (Roles)

| Role                | Akses                                                        |
| ------------------- | ------------------------------------------------------------ |
| **Admin**           | Kelola user, struktur organisasi, template, penomoran        |
| **Pimpinan**        | Baca surat masuk, buat disposisi, approve surat keluar       |
| **Staf/Sekretaris** | Input surat masuk, buat draft surat keluar, proses disposisi |
| **Unit Kerja**      | Terima disposisi, update status, buat surat keluar unit      |

---

## 🗃️ Struktur Database (Entitas Utama)

```
users
  - id, name, email, role, jabatan, unit_kerja_id, avatar

unit_kerja
  - id, nama, parent_id (untuk hierarki)

surat_masuk
  - id, nomor_agenda, nomor_surat, tanggal_surat, tanggal_terima
  - pengirim, perihal, klasifikasi, file_url
  - status [diterima|dibaca|didisposisi|selesai]
  - created_by, unit_tujuan_id

surat_keluar
  - id, nomor_surat (auto), tanggal, perihal, tujuan
  - isi_surat, file_url, status [draft|review|approved|sent]
  - created_by, unit_kerja_id, approved_by

disposisi
  - id, surat_masuk_id
  - dari_user_id, ke_user_id (atau ke_unit_id)
  - instruksi, catatan, batas_waktu
  - status [belum_diproses|sedang_diproses|selesai]
  - created_at, updated_at

disposisi_balasan
  - id, disposisi_id, user_id, isi_balasan, created_at

template_surat
  - id, nama, isi_template, unit_kerja_id, created_by

notifikasi
  - id, user_id, tipe, referensi_id, pesan, dibaca, created_at
```

---

## 🖥️ Tech Stack

### Core — Laravel 11 + Filament v5

```
Backend    : Laravel 11 (PHP 8.2+)
Admin UI   : Filament v5.x (latest stable: v5.2.x)
Frontend   : Livewire v4 + Alpine.js + Tailwind CSS v4
             (sudah bundled dalam Filament v5, tidak perlu install terpisah)
Database   : MySQL 8 / PostgreSQL
Auth       : Spatie Permission (RBAC) + FilamentUser contract
File       : spatie/laravel-media-library (terintegrasi dengan Filament)
Queue      : Laravel Queue + Redis (notifikasi async, email)
Realtime   : Laravel Reverb (WebSocket native) → Filament Notifications
PDF        : barryvdh/laravel-dompdf
Excel/Exp  : maatwebsite/laravel-excel
```

### Instalasi Filament v5

```bash
# 1. Buat project Laravel baru
laravel new esurat
cd esurat

# 2. Install Filament v5
composer require filament/filament:"^5.0"
php artisan filament:install --panels

# 3. Buat user admin pertama
php artisan make:filament-user

# 4. Akses panel di browser
# http://localhost:8000/admin
```

### Paket Composer Utama

```
filament/filament:"^5.0"          → Admin panel, forms, tables, widgets
spatie/laravel-permission          → Role & Permission (RBAC)
spatie/laravel-activitylog         → Audit log otomatis
spatie/laravel-media-library       → Upload & manajemen file surat (terintegrasi Filament)
barryvdh/laravel-dompdf            → Generate PDF surat keluar
maatwebsite/laravel-excel          → Export laporan ke Excel
laravel/reverb                     → WebSocket realtime (built-in L11)
```

### 🤖 Filament Blueprint (Bonus untuk AI Coding)

Filament Blueprint adalah tool yang membantu AI coding agents menghasilkan implementation plan yang lebih baik untuk proyek Filament v4 dan v5. Sangat berguna saat pakai Gemini/Antigravity/Cursor untuk generate kode Filament.

```
→ Docs: https://filamentphp.com/docs/5.x/blueprint/introduction
→ Gunakan Blueprint untuk minta AI generate:
   - Resource CRUD (SuratMasuk, Disposisi, dll)
   - Form fields dengan validasi
   - Table columns dengan filter & search
   - Action buttons (Disposisi, Approve, Tolak)
   - Stats widgets untuk dashboard
```

### Filament Resources yang Akan Dibuat

```bash
php artisan make:filament-resource SuratMasuk --generate
php artisan make:filament-resource SuratKeluar --generate
php artisan make:filament-resource Disposisi --generate
php artisan make:filament-resource UnitKerja --generate
php artisan make:filament-resource User --generate
php artisan make:filament-widget StatsOverview         # Dashboard statistik
php artisan make:filament-widget DisposisiPendingChart  # Chart disposisi pending
```

### Tambahan (Opsional)

```
Mobile App : Flutter + Laravel Sanctum API
TTE        : Integrasi BSrE / Peruri / Privy (via HTTP Client Laravel)
SSO        : LDAP (dirathore/ldap-auth) / OAuth2 / SAML
```

---

## 📱 Fitur Mobile App

- Login & notifikasi push
- Lihat surat masuk
- Buat & kirim disposisi
- Approve surat keluar
- Pantau status surat

---

## 🔐 Keamanan

- Role-based Access Control (RBAC)
- Audit log semua aktivitas user
- Enkripsi file surat (at rest & in transit)
- Session management & 2FA opsional
- Backup otomatis harian

---

## 🔗 Integrasi (Opsional)

| Sistem                      | Keterangan                             |
| --------------------------- | -------------------------------------- |
| **TTE (BSrE/Peruri/Privy)** | Tanda tangan elektronik tersertifikasi |
| **SSO/LDAP**                | Login terpusat dengan akun instansi    |
| **SIMPEG/HR**               | Sinkronisasi data pegawai & jabatan    |
| **Email (SMTP)**            | Notifikasi & kirim surat via email     |
| **WhatsApp API**            | Notifikasi via WA                      |

---

## 🗺️ Alur Utama: Surat Masuk & Disposisi

```
[Surat Fisik/Digital Masuk]
        ↓
[Sekretaris Input ke Sistem]
  - Upload scan/PDF
  - Isi metadata
        ↓
[Sistem beri nomor agenda otomatis]
        ↓
[Notifikasi ke Pimpinan]
        ↓
[Pimpinan buka aplikasi → baca surat]
        ↓
[Pimpinan buat Disposisi]
  - Pilih tujuan (user/unit)
  - Tulis instruksi
  - Set batas waktu
        ↓
[Notifikasi ke Unit/Staf yang dituju]
        ↓
[Staf proses → update status → balas disposisi]
        ↓
[Pimpinan pantau progres]
        ↓
[Status: SELESAI]
```

---

## 🗺️ Alur Utama: Surat Keluar

```
[Staf buat Draft Surat dari Template]
        ↓
[Kolaborasi & Revisi (jika perlu)]
        ↓
[Penomoran Otomatis]
        ↓
[Submit untuk Approval Pimpinan]
        ↓
[Pimpinan Review → Approve / Tolak]
        ↓
[Tanda Tangan Elektronik (TTE)]
        ↓
[Surat Dikirim (print/email/sistem eksternal)]
        ↓
[Arsip Otomatis]
```

---

## 📋 Sprint Plan (Agile - 2 Minggu per Sprint)

### Sprint 1 — Foundation

- [ ] `laravel new esurat` → install Filament v5: `composer require filament/filament:"^5.0"`
- [ ] `php artisan filament:install --panels` → setup AdminPanelProvider
- [ ] Install `spatie/laravel-permission` → RoleSeeder (admin, pimpinan, sekretaris, staf)
- [ ] Migration: users, unit_kerja → UnitKerjaResource di Filament
- [ ] Konfigurasi FilamentUser contract di User model + hak akses per role

### Sprint 2 — Surat Masuk

- [ ] Migration & Model SuratMasuk
- [ ] `php artisan make:filament-resource SuratMasuk --generate`
- [ ] Install `spatie/laravel-media-library` → konfigurasi SpatieMediaLibraryFileUpload di form
- [ ] Tambah kolom status, filter, search di ListSuratMasuks
- [ ] Penomoran agenda otomatis (format: `SM/001/II/2026`) di model observer

### Sprint 3 — Disposisi

- [ ] Migration & Model Disposisi + DisposisiBalasan
- [ ] `php artisan make:filament-resource Disposisi --generate`
- [ ] RelationManager: DisposisisRelationManager di SuratMasukResource
- [ ] Custom Action "Buat Disposisi" di tabel surat masuk (Action modal)
- [ ] Status tracking dengan SelectColumn + badge warna
- [ ] Notifikasi database menggunakan Filament Notifications

### Sprint 4 — Surat Keluar

- [ ] Migration & Model SuratKeluar + TemplateSurat
- [ ] `php artisan make:filament-resource SuratKeluar --generate`
- [ ] Penomoran surat keluar otomatis per unit kerja
- [ ] Alur approval: Action "Submit Review" → "Approve" → "Tolak" dengan modal konfirmasi
- [ ] Generate PDF dengan `barryvdh/laravel-dompdf` via Action button

### Sprint 5 — Dashboard & Notifikasi

- [ ] `php artisan make:filament-widget StatsOverview` → total surat, disposisi pending
- [ ] `php artisan make:filament-widget SuratChart` → grafik tren persuratan
- [ ] Laravel Reverb untuk realtime notifications (Filament sudah support broadcast)
- [ ] Export laporan Excel dengan `maatwebsite/laravel-excel` via Action
- [ ] Audit log dengan `spatie/laravel-activitylog`

### Sprint 6 — Polish & Integrasi

- [ ] Pencarian global surat (Filament Global Search)
- [ ] API endpoint dengan Laravel Sanctum untuk mobile Flutter
- [ ] Integrasi TTE BSrE/Privy (HTTP Client Laravel)
- [ ] Custom theme Filament (warna, logo instansi) di AdminPanelProvider
- [ ] Testing dengan Pest PHP + deploy setup (Nginx, Supervisor queue worker)

---

## 💡 Prompt untuk Gemini / AI Tools

Gunakan prompt berikut saat bekerja dengan file ini:

```
Saya sedang membangun Aplikasi Persuratan Digital dengan fitur Disposisi
menggunakan Laravel 11 + Filament v5 + Livewire v4 + Tailwind CSS v4.
Berikut adalah project plan lengkapnya dalam file gemini.md.

Tolong bantu saya [TASK SPESIFIK, contoh:
- "buatkan migration database untuk semua tabel di atas"
- "buatkan SuratMasukResource lengkap dengan form, table, filter dan search"
- "buatkan DisposisiResource dengan RelationManager di SuratMasukResource"
- "buatkan Action 'Buat Disposisi' dengan modal form di tabel surat masuk"
- "buatkan StatsOverview widget untuk dashboard"
- "buatkan RoleSeeder menggunakan spatie/laravel-permission"
- "buatkan API endpoint untuk mobile Flutter dengan Sanctum"
- "konfigurasi AdminPanelProvider dengan custom tema dan navigasi"
]

Tech stack: Laravel 11, Filament v5, Livewire v4, Alpine.js,
Tailwind CSS v4, MySQL, Spatie Permission, Laravel Reverb, DomPDF
```

### Tips Pakai Filament Blueprint di Antigravity

Filament Blueprint membantu AI coding agents menghasilkan implementation plan yang lebih detail dan akurat untuk proyek Filament. Cara pakainya:

1. Install Blueprint di project: `composer require filament/blueprint --dev`
2. Aktifkan "planning mode" di AI agent (Antigravity/Cursor/Gemini)
3. Minta AI: _"Buat Filament Blueprint untuk Resource SuratMasuk dengan fitur disposisi"_
4. Blueprint akan generate spesifikasi detail: form fields, table columns, actions, relations

---

## 📁 Struktur Folder Proyek (Laravel 11 + Filament v5)

```
app/
  Filament/
    Resources/
      SuratMasukResource.php
      SuratMasukResource/
        Pages/
          ListSuratMasuks.php
          CreateSuratMasuk.php
          ViewSuratMasuk.php
        RelationManagers/
          DisposisisRelationManager.php
      SuratKeluarResource.php
      SuratKeluarResource/Pages/...
      DisposisiResource.php
      DisposisiResource/Pages/...
      UnitKerjaResource.php
      UserResource.php
    Pages/
      Dashboard.php
    Widgets/
      StatsOverview.php          ← total surat masuk/keluar, pending disposisi
      DisposisiPendingTable.php  ← tabel disposisi yang belum diproses
      SuratChart.php             ← grafik tren persuratan

  Http/
    Controllers/
      Api/                       ← untuk mobile Flutter
        SuratMasukController.php
        DisposisiController.php
    Middleware/
      CheckRole.php

  Models/
    User.php
    UnitKerja.php
    SuratMasuk.php
    SuratKeluar.php
    Disposisi.php
    DisposisiBalasan.php
    TemplateSurat.php

  Notifications/
    SuratMasukBaru.php
    DisposisiBaru.php
    SuratKeluarApproved.php

  Providers/
    Filament/
      AdminPanelProvider.php     ← konfigurasi utama panel Filament

database/
  migrations/
    ..._create_unit_kerjas_table.php
    ..._create_surat_masuks_table.php
    ..._create_surat_keluars_table.php
    ..._create_disposisis_table.php
    ..._create_disposisi_balasans_table.php
    ..._create_template_surats_table.php
  seeders/
    RoleSeeder.php
    UserSeeder.php
    UnitKerjaSeeder.php

resources/
  views/
    filament/           ← custom Blade untuk override tampilan Filament
  css/app.css           ← Tailwind CSS v4
  js/app.js

routes/
  web.php
  api.php               ← untuk mobile Flutter (Sanctum)
```

---

_Dibuat untuk: PT Integra / Proyek E-Surat Internal_  
_Versi: 1.0 | Tanggal: Februari 2026_
