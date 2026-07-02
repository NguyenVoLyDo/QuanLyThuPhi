FROM php:8.2-apache

# Cài đặt các thư viện hệ thống và các tiện ích mở rộng PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli \
    && a2enmod rewrite

# Cấu hình cho phép đọc file .htaccess (AllowOverride All) trong Apache
RUN sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Sao chép mã nguồn của dự án (thư mục code) vào thư mục Web Root
COPY ./code/ /var/www/html/

# Phân quyền cho Apache truy cập và ghi file (đặc biệt là thư mục uploads)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/be/uploads \
    && chmod -R 777 /var/www/html/be/uploads

# Mở cổng 80 cho web
EXPOSE 80
