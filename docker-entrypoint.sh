#!/bin/sh

# Menjalankan migrasi database jika diperlukan
php migrate.php

# Menjalankan PHP-FPM
exec "$@"
