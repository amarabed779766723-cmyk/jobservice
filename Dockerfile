# استخدام بيئة PHP 7.4 الرسمية مع سيرفر Apache
FROM php:7.4-apache

# تثبيت الإضافات المطلوبة لـ Laravel و MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تفعيل موديل ReWrite في Apache لتوجيه روابط Laravel
RUN a2enmod rewrite

# ضبط المجلد الرئيسي لسيرفر Apache ليكون داخل مجلد public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# نسخ ملفات المشروع إلى السيرفر
COPY . /var/www/html

# إعطاء الصلاحيات المجلدات الخاصة بالـ Storage و Cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

EXPOSE 80
