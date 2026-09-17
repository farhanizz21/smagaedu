<?php
// Script untuk membuat password hash superadmin
// Jalankan via CLI: php create_superadmin.php

// Include CodeIgniter
require_once 'application/config/database.php';

// Buat password hash
$password = 'superadmin123'; // Ganti password ini
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n\n";

echo "SQL untuk membuat superadmin:\n";
echo "INSERT INTO users (uuid, role_id, nama, username, password, email, status) VALUES\n";
echo " (UUID(), 1, 'Super Admin Name', 'superadmin', '" . $hash . "', 'superadmin@sekolah.ac.id', 'aktif');\n";