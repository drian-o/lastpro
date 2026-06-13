# Menggunakan image resmi OpenLiteSpeed dengan PHP 8.2
FROM litespeedtech/openlitespeed:1.8.2-lsphp82

# Mengatur working directory
WORKDIR /var/www/html

# Instal ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    lsphp82-mysql \
    lsphp82-common \
    && rm -rf /var/www/html/*

# Copy file kodingan
COPY . /var/www/html/

# ========================================================
# FIX HAK AKSES & KONFIGURASI OLS
# ========================================================
# Di OLS, user untuk proses web biasanya adalah 'lsadm' atau 'nobody'
RUN chown -R lsadm:lsadm /var/www/html

# Mengatur batas upload di PHP (LiteSpeed menggunakan path ini untuk PHP)
RUN echo "upload_max_filesize = 64M" > /usr/local/lsws/lsphp82/etc/php/8.2/mods-available/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/lsws/lsphp82/etc/php/8.2/mods-available/uploads.ini

# ========================================================
# CONFIGURASI OLS (Virtual Host)
# ========================================================
# OpenLiteSpeed biasanya sudah dikonfigurasi untuk menangani semua request ke /var/www/html secara default
# Tidak perlu vhost terpisah jika menggunakan setting default image ini.

EXPOSE 80 443 7080
