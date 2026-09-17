# Setup Role Superadmin untuk SmartEdu

## File yang Telah Dibuat

| File | Fungsi |
|------|--------|
| `smartedu_with_superadmin.sql` | SQL lengkap dengan tabel users terpusat, roles, permissions, dan user superadmin pertama |
| `smartedu_updated.sql` | SQL schema saja (tanpa data) |
| `migrate_users.sql` | Script migrasi data user lama ke sistem baru |
| `application/models/Auth_model.php` | Model autentikasi yang diupdate untuk role terpusat |
| `application/helpers/role_helper.php` | Helper functions untuk mengecek role dan permission |
| `application/core/MY_Controller.php` | Base controller dengan middleware role checking |
| `application/config/autoload.php` | Diupdate untuk autoload helper role dan form |
| `application/views/partials/navbar.php` | Diupdate untuk menampilkan menu berdasarkan role |

## Langkah-langkah Instalasi

### 1. Backup Database (PENTING!)
```bash
cp smartedu.sql smartedu_backup.sql
```

### 2. Jalankan SQL Migration
Jika ingin migrasi dari sistem lama:
```bash
# Import schema baru (role system)
mysql -u username -p database_name < smartedu_updated.sql

# Migrasi data user lama
mysql -u username -p database_name < migrate_users.sql
```

Atau jika ingin fresh install:
```bash
# Import schema lengkap dengan superadmin
mysql -u username -p database_name < smartedu_with_superadmin.sql
```

### 3. Login dengan Superadmin
- Username: `superadmin`
- Password: `superadmin123`
- **Segera ganti password setelah login pertama!**

### 4. Verifikasi di Dashboard
Buka aplikasi di browser, login dengan akun di atas, dan Anda akan melihat menu **Settings** yang hanya bisa diakses oleh superadmin.

## Penggunaan di Kode

### Di Controller:
```php
// Cek role
if (has_role('superadmin')) {
    // hanya superadmin
}

if (is_admin_or_superadmin()) {
    // admin atau superadmin
}

// Cek permission - superadmin otomatis punya semua permission
if (has_permission('manage_users')) {
    // bisa mengelola user
}
```

### Di View:
```php
<?php if (is_superadmin()): ?>
    <a href="<?= base_url('admin/settings') ?>">Settings</a>
<?php endif; ?>

<?php if (is_admin_or_superadmin()): ?>
    <!-- menu khusus admin -->
<?php endif; ?>
```

### Di Controller dengan MY_Controller:
```php
class Admin extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->require_admin_or_superadmin(); // Admin atau superadmin yang boleh akses
    }
    
    public function manage_users() {
        $this->require_permission('manage_users');
        // superadmin otomatis punya akses semua permission
        // logic
    }
}
```

## Permission Default

| Role | Permission |
|------|------------|
| superadmin | Semua permission (otomatis) |
| admin | manage_users, manage_mapel, manage_materi, manage_ujian, manage_proyek |
| guru | manage_materi, manage_ujian, manage_proyek |
| siswa | view_reports |

## Helper Functions Baru

| Function | Deskripsi |
|----------|-----------|
| `is_superadmin()` | Mengecek apakah user adalah superadmin |
| `is_admin_or_superadmin()` | Mengecek apakah user adalah admin atau superadmin |
| `require_role_access($roles)` | Mengecek akses role (superadmin otomatis punya akses semua) |
| `has_permission($permission)` | Mengecek permission (superadmin otomatis punya semua permission) |

## Struktur Database Baru

```
roles
  ├── id, nama, deskripsi

users (tabela terpusat)
  ├── id, uuid, role_id, nama, username, password, email, status

user_profiles (data tambahan per user)
  ├── user_id, nip, nis, tgl_lahir, jenis_kelamin

permissions
  ├── id, nama, deskripsi

role_permissions
  ├── role_id, permission_id (many-to-many)
```

## Catatan Penting

1. Sistem baru menggunakan `role_nama` (string) alih-alih angka di session
2. Role yang tersedia: `superadmin`, `admin`, `guru`, `siswa`
3. VIEW `admin_view`, `guru_view`, `siswa_view` tersedia untuk kompatibilitas kode lama
4. Untuk migrasi penuh, hapus tabel lama `admin`, `guru`, `siswa` setelah migrasi selesai
5. **Superadmin otomatis memiliki akses penuh ke semua fitur** - tidak perlu cek permission secara khusus
6. Semua controller kini meng-extend `MY_Controller` untuk konsistensi role checking