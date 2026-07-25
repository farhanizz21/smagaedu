# Ringkasan Perubahan - Role Superadmin untuk SmartEdu

## File Baru yang Dibuat

| No | File | Fungsi |
|---|------|--------|
| 1 | `smartedu_with_superadmin.sql` | SQL lengkap dengan tabel users terpusat + superadmin |
| 2 | `smartedu_updated.sql` | SQL schema saja (tanpa data) |
| 3 | `migrate_users.sql` | Script migrasi data dari tabel lama |
| 4 | `application/models/Auth_model.php` | Model auth dengan role-based system |
| 5 | `application/helpers/role_helper.php` | Helper functions untuk role checking |
| 6 | `application/core/MY_Controller.php` | Base controller dengan middleware |
| 7 | `application/controllers/Admin.php` | Controller khusus superadmin |
| 8 | `application/views/admin/dashboard.php` | Dashboard superadmin |
| 9 | `application/views/admin/settings.php` | Halaman settings |
| 10 | `application/views/admin/roles.php` | Kelola role |
| 11 | `application/views/admin/permissions.php` | Kelola permission |
| 12 | `README_SUPERADMIN.md` | Dokumentasi lengkap |
| 13 | `smartedu_backup.sql` | Backup database lama |

## File yang Diubah

| No | File | Perubahan |
|---|------|----------|
| 1 | `application/config/autoload.php` | Menambahkan helper role dan form |
| 2 | `application/views/partials/navbar.php` | Update role checking untuk menampilkan menu superadmin |

## Cara Instalasi Cepat

### Opsi 1: Fresh Install
```bash
# Hapus database lama, import yang baru
mysql -u root -p smartedu < smartedu_with_superadmin.sql
```

### Opsi 2: Migrasi dari Database Lama
```bash
# Backup dulu!
cp smartedu.sql smartedu_backup.sql

# Import schema baru (tanpa hapus data lama)
mysql -u root -p smartedu < smartedu_updated.sql

# Migrasi data user lama
mysql -u root -p smartedu < migrate_users.sql
```

## Akun Superadmin Default
- **Username:** `superadmin`
- **Password:** `superadmin123`
- **Role:** superadmin (memiliki semua permission)

## Helper Functions yang Tersedia

```php
is_superadmin()           // True jika user adalah superadmin
has_role('admin')         // True jika user memiliki role tertentu
has_role(['admin','guru']) // True jika user memiliki salah satu role
has_permission('manage_users') // True jika user punya permission
user_role()              // Mendapatkan role user (string)
user_role_id()           // Mendapatkan role ID (integer)
current_user()          // Mendapatkan data user login
```

## Permission Default

| Permission | Superadmin | Admin | Guru | Siswa |
|------------|------------|-------|------|-------|
| manage_users | ✅ | ✅ | ❌ | ❌ |
| manage_mapel | ✅ | ✅ | ❌ | ❌ |
| manage_materi | ✅ | ✅ | ✅ | ❌ |
| manage_ujian | ✅ | ✅ | ✅ | ❌ |
| manage_proyek | ✅ | ✅ | ✅ | ❌ |
| view_reports | ✅ | ✅ | ✅ | ✅ |
| manage_settings | ✅ | ❌ | ❌ | ❌ |

## Contoh Penggunaan di Controller

```php
// Controller khusus superadmin
class Admin extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->require_superadmin(); // Hanya superadmin
    }
}

// Controller dengan permission check
class Guru extends MY_Controller {
    public function manage_materi() {
        $this->require_permission('manage_materi'); // Bisa diakses superadmin, admin, guru
        // ...
    }
}
```

## Contoh Penggunaan di View

```php
<!-- navbar.php -->
<?php if(is_superadmin()): ?>
    <a href="<?= base_url('admin/settings') ?>">Settings</a>
<?php endif; ?>

<?php if(in_array(user_role(), ['superadmin', 'admin'])): ?>
    <!-- Master Data menu -->
<?php endif; ?>