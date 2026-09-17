# Exam Feature Integrated into Bab (Sub Bab)

## Summary
Fitur ujian telah diintegrasikan langsung ke halaman Bab. Setiap Bab menampilkan daftar Sub Bab-nya, dan setiap Sub Bab memiliki tombol **Tambah Ujian** untuk membuat ujian dengan deadline. Guru dapat menambahkan soal, peserta, dan memberikan nilai.

## Database
Tabel `ujian` sudah memiliki kolom `sub_materi_uuid` untuk menghubungkan ujian ke sub bab.

Permission `manage_ujian` sudah di-assign ke: admin, guru, superadmin.

## Alur Navigasi
1. **Mata Pelajaran** (`materi`) → daftar mata pelajaran
2. **Bab** (`bab/index/[materi_uuid]`) → daftar bab, menampilkan sub bab di dalam setiap kartu bab
3. **Tambah Ujian** (`ujian/tambah_sub/[sub_materi_uuid]`) → form buat ujian dari sub bab

## Files Changed
- `application/controllers/Bab.php` - load `sub_materi_model` & `ujian_model`, passing sub bab + ujian data to view
- `application/views/materi/bab.php` - tampilkan sub bab dan tombol "Tambah Ujian" di setiap sub bab
- `application/controllers/Ujian.php` - tambah method `tambah_sub($sub_materi_uuid)`
- `application/models/Ujian_model.php` - tambah `insert_sub()`, `get_by_sub_materi()`, `get_ujian_count_by_sub_materi()`
- `application/views/ujian/ujian-tambah-sub.php` - form tambah ujian langsung dari sub bab

## Removed
- `application/controllers/Sub_materi.php`
- `application/views/materi/sub_materi.php`
- `application/views/materi/sub_materi-tambah.php`
- `application/views/materi/sub_materi-edit.php`

## Usage
1. Guru/Admin login → Menu **Mata Pelajaran**
2. Pilih mata pelajaran untuk melihat daftar Bab
3. Di dalam setiap kartu Bab, lihat **Sub Bab** terdaftar
4. Klik **Tambah Ujian** pada sub bab yang diinginkan
5. Isi nama ujian, tanggal mulai, tanggal selesai
6. Klik **Simpan & Tambah Soal** untuk menambahkan soal
7. Klik **Peserta** untuk menambahkan siswa
8. Siswa mengerjakan di menu **Ujian**
9. Guru beri nilai di halaman **Peserta** → **Nilai**