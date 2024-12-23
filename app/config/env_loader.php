<?php
// Fungsi untuk memuat variabel lingkungan dari file .env
function loadEnv($path) {
    // Cek apakah file .env ada
    if (!file_exists($path)) {
        throw new Exception("The .env file does not exist."); // Lempar Error jika file tidak ada
    }

    // Baca file .env dan simpan setiap baris sebagai elemen dalam array
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Lewati baris yang dimulai dengan karakter #
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Pisahkan baris menjadi nama variabel dan nilai variabel
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name); // Hapus spasi di sekitar nama variabel
        $value = trim($value); // Hapus spasi di sekitar nilai variabel

        // Set variabel lingkungan jika belum ada di $_SERVER dan $_ENV
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value)); // Set variabel lingkungan
            $_ENV[$name] = $value; // Tambahkan ke array $_ENV
            $_SERVER[$name] = $value; // Tambahkan ke array $_SERVER
        }
    }
}
