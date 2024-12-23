# Menggunakan image PHP 8.3 dengan FPM
FROM php:8.3-fpm

# Menginstal ekstensi yang diperlukan
RUN docker-php-ext-install pdo pdo_mysql

# Menginstal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Mengatur direktori kerja
WORKDIR /var/www/html

# Menyalin file aplikasi ke dalam container
COPY . .

# Menyalin file .env ke dalam container
COPY .env /var/www/html/.env

# Mengatur izin untuk direktori storage dan cache
RUN mkdir -p /var/www/html/storage
# /var/www/sessions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 777 /var/www/html/storage

# Menyalin konfigurasi Nginx
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./nginx/fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf

# Menyalin script entrypoint
COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Menjalankan script entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Menjalankan PHP-FPM
CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
