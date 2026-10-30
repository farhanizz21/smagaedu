# Upload perangkat pembelajaran

Perangkat hanya menerima PDF sampai 10 MB. PDF di atas 2 MB dikompres otomatis dengan Ghostscript menggunakan preset kualitas sedang (`/ebook`). Jika hasil kompresi tidak lebih kecil, PDF asli tetap disimpan.

Persyaratan server:

- Pasang Ghostscript dan pastikan `gs` dapat dijalankan oleh PHP. Jika executable tidak ada di PATH standar, atur variabel lingkungan `GHOSTSCRIPT_BINARY` ke path absolut executable.
- Atur `upload_max_filesize` sekurang-kurangnya `10M` dan `post_max_size` sekurang-kurangnya `12M` pada `php.ini`, kemudian restart Apache/PHP.
- Bila menggunakan XAMPP, edit `xamppfiles/etc/php.ini` yang digunakan Apache, bukan hanya konfigurasi PHP CLI.

Contoh pemasangan Ghostscript:

- macOS dengan Homebrew: `brew install ghostscript`
- Debian/Ubuntu: `sudo apt install ghostscript`
