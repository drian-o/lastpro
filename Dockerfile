# Menggunakan image resmi OpenLiteSpeed
FROM litespeedtech/openlitespeed:1.8.2-lsphp82

# Set working directory
WORKDIR /var/www/html

# Update dan install ekstensi PHP yang umum dipakai
RUN apt-get update && apt-get install -y \
    lsphp82-mysql \
    lsphp82-curl \
    lsphp82-gd \
    lsphp82-intl \
    lsphp82-mbstring \
    lsphp82-xml \
    lsphp82-zip \
    && rm -rf /var/lib/apt/lists/*

# Copy source code ke target folder OLS
COPY . /var/www/html/

# FIX: Ubah DocumentRoot default OLS ke folder kodingan kita
# Ini krusial agar tidak 404
RUN sed -i 's|docRoot                   $SERVER_ROOT/Example/html|docRoot                   /var/www/html|g' /usr/local/lsws/conf/vhosts/Example/vhconf.conf

# FIX: Aktifkan .htaccess (Wajib untuk aplikasi PHP/Framework)
RUN sed -i 's|enableRewrite             0|enableRewrite             1|g' /usr/local/lsws/conf/vhosts/Example/vhconf.conf && \
    sed -i 's|autoLoadHtaccess          0|autoLoadHtaccess          1|g' /usr/local/lsws/conf/vhosts/Example/vhconf.conf

# Set permission agar user web bisa baca
RUN chown -R lsadm:lsadm /var/www/html

# OLS butuh port 80 untuk web, dan 7080 untuk admin (Coolify hanya butuh 80)
EXPOSE 80

# Jalankan LiteSpeed
CMD ["/usr/local/lsws/bin/openlitespeed", "-d"]
