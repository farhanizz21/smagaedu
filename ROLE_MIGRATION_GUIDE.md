# Panduan Migrasi ke Role-Based Access Control (RBAC)

## Ringkasan Perubahan

Untuk mempermudah pengaturan role dan hak akses, telah dibuat sistem role terpusat dengan tabel `users`, `roles`, `user_profiles`, dan `permissions`.

### Role yang Tersedia:
1. **superadmin** - Akses penuh ke semua fitur sistem
2. **admin** - Admin sekolah - mengelola data utama
3. **guru** - Guru - mengelola materi dan ujian
4. **siswa** - Siswa - mengakses materi dan mengumpulkan tugas

## Langkah Migrasi

### 1. Jalankan SQL Migration

Eksekusi file `smartedu_updated.sql` di database Anda untuk membuat tabel baru:

```bash
mysql -u username -p database_name < smartedu_updated.sql
```

Atau import via phpMyAdmin.

### 2. Migrasi Data User Lama ke Tabel Baru

Jalankan query berikut untuk memindahkan data user lama:

```sql
-- Migrasi admin lama ke users
INSERT INTO users (uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT uuid, 2, nama, username, password, created_by, modified_at, deleted_at 
FROM admin 
WHERE deleted_at IS NULL;

-- Migrasi guru lama ke users
INSERT INTO users (uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT uuid, 3, nama, username, password, created_by, modified_at, deleted_at 
FROM guru 
WHERE deleted_at IS NULL;

-- Migrasi siswa lama ke users
INSERT INTO users (uuid, role_id, nama, username, password, created_by, modified_at, deleted_at)
SELECT uuid, 4, nama, username, password, created_by, modified_at, deleted_at 
FROM siswa 
WHERE deleted_at IS NULL;
```

Setelah data migrasi selesai, baru hapus tabel lama jika diperlukan.

## Penggunaan di CodeIgniter

### 1. Helper Functions (autoload otomatis)

Helper `role_helper.php` telah ditambahkan ke autoload. Fungsi yang tersedia:

```php
// Cek apakah user adalah superadmin
is_superadmin();

// Cek apakah user memiliki role tertentu
has_role('superadmin');        // Single role
has_role(['admin', 'superadmin']);  // Multiple roles

// Cek apakah user memiliki permission tertentu
has_permission('manage_users');
has_permission('manage_materi');

// Dapatkan data user yang sedang login
current_user();

// Dapatkan role user
user_role();    // Returns: 'superadmin', 'admin', 'guru', 'siswa'
user_role_id(); // Returns: role ID (1, 2, 3, 4)
```

### 2. Penggunaan di Controller

```php
class Dashboard extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Cek login
        if (!current_user()) {
            redirect('login');
        }
    }
    
    public function index() {
        // Cek role
        if (has_role(['superadmin', 'admin'])) {
            // Hanya superadmin dan admin yang bisa akses
            $this->load->view('dashboard/admin');
        } elseif (has_role('guru')) {
            // Guru bisa akses fitur terbatas
            $this->load->view('dashboard/guru');
        } else {
            // Siswa
            $this->load->view('dashboard/siswa');
        }
    }
    
    public function manage_users() {
        // Cek permission
        if (!has_permission('manage_users')) {
            show_error('Anda tidak memiliki akses ke halaman ini');
        }
        
        // Logic untuk mengelola users
    }
}
```

### 3. Penggunaan di View

```php
<!-- Di view file -->
<?php if (is_superadmin()): ?>
    <a href="<?= base_url('admin/settings') ?>" class="btn btn-danger">Pengaturan Sistem</a>
<?php endif; ?>

<?php if (has_role(['admin', 'superadmin'])): ?>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-primary">Kelola User</a>
<?php endif; ?>

<?php if (has_permission('manage_materi')): ?>
    <a href="<?= base_url('materi/create') ?>" class="btn btn-success">Tambah Materi</a>
<?php endif; ?>
```

## Permission Default

| Permission | Deskripsi | Superadmin | Admin | Guru | Siswa |
|------------|-----------|------------|-------|------|-------|
| manage_users | Kelola semua pengguna | ✅ | ✅ | ❌ | ❌ |
| manage_mapel | Kelola mata pelajaran | ✅ | ✅ | ❌ | ❌ |
| manage_materi | Kelola materi pembelajaran | ✅ | ✅ | ✅ | ❌ |
| manage_ujian | Kelola ujian | ✅ | ✅ | ✅ | ❌ |
| manage_proyek | Kelola proyek/tugas | ✅ | ✅ | ✅ | ❌ |
| view_reports | Lihat laporan dan statistik | ✅ | ✅ | ✅ | ✅ |
| manage_settings | Kelola pengaturan sistem | ✅ | ❌ | ❌ | ❌ |

## File yang Diubah/Ditambahkan

1. **smartedu_updated.sql** - Skema database baru dengan role terpusat
2. **application/models/Auth_model.php** - Model autentikasi yang diperbarui
3. **application/helpers/role_helper.php** - Helper baru untuk pemeriksaan role
4. **application/config/autoload.php** - Menambahkan helper role dan form

## Kompatibilitas

Jika Anda ingin tetap menggunakan kode lama yang mengakses tabel `admin`, `guru`, `siswa`, Anda bisa menggunakan VIEW yang sudah disediakan di `smartedu_updated.sql`:

- `admin_view` - View untuk admin (includes superadmin)
- `guru_view` - View untuk guru  
- `siswa_view` - View untuk siswa

Namun disarankan untuk migrasi penuh ke sistem baru untuk konsistensi.

## Contoh Membuat User Superadmin

```sql
-- Insert role jika belum ada
INSERT IGNORE INTO roles (id, nama, deskripsi) VALUES 
(1, 'superadmin', 'Akses penuh ke semua fitur sistem');

-- Insert user superadmin
INSERT INTO users (uuid, role_id, nama, username, password, email, status) VALUES
(UUID(), 1, 'Super Admin', 'superadmin', '$2y$10$hash_password_disini', 'superadmin@sekolah.ac.id', 'aktif');