# Upload perangkat pembelajaran

Perangkat hanya menerima PDF sampai 10 MB. PDF di atas 2 MB dikompres di server menggunakan Ghostscript (preset kualitas sedang, `/ebook`) terlebih dahulu, lalu MuPDF `mutool` sebagai alternatif. MuPDF membersihkan objek yang tidak digunakan dan mengompres stream font, gambar, dan konten tanpa mengubah resolusi gambar. Jika kedua alat gagal atau hasilnya tidak lebih kecil, upload ditolak agar pengguna dapat mengompres file secara manual.

Persyaratan server:

- Pasang Ghostscript dan/atau MuPDF command-line tools. Pastikan `gs` dan `mutool` dapat dijalankan oleh PHP. Jika executable tidak ada di PATH standar, atur variabel lingkungan `GHOSTSCRIPT_BINARY` dan/atau `MUTOOL_BINARY` ke path absolut executable.
- Atur `upload_max_filesize` sekurang-kurangnya `10M` dan `post_max_size` sekurang-kurangnya `12M` pada `php.ini`, kemudian restart Apache/PHP.
- Bila menggunakan XAMPP, edit `xamppfiles/etc/php.ini` yang digunakan Apache, bukan hanya konfigurasi PHP CLI.

Contoh pemasangan Ghostscript:

- macOS dengan Homebrew: `brew install ghostscript`
- Debian/Ubuntu: `sudo apt install ghostscript`

Contoh pemasangan MuPDF sebagai alternatif:

- macOS dengan Homebrew: `brew install mupdf-tools`
- Debian/Ubuntu: `sudo apt install mupdf-tools`
