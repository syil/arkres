# PHP ve Apache içeren resmi PHP imajını kullan
FROM php:7.4-apache

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Sistem bağımlılıklarını yükle
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# PHP uzantılarını yükle
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo pdo_mysql

# Apache mod_rewrite'ı etkinleştir
RUN a2enmod rewrite

# .htaccess dosyalarının çalışması için AllowOverride'ı etkinleştir
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Uygulama dosyalarını kopyala
COPY . /var/www/html/

# Dizin izinlerini ayarla
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# veri dizinini oluştur (eğer yoksa)
RUN mkdir -p /var/www/html/veri && \
    chown -R www-data:www-data /var/www/html/veri

# Port 80'i aç
EXPOSE 80

# Apache'yi başlat
CMD ["apache2-foreground"]
