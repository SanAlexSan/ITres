# Используем официальный образ PHP 8.2 с Apache
FROM php:8.2-apache

# Устанавливаем расширения для PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Включаем mod_rewrite для .htaccess (для красивых URL)
RUN a2enmod rewrite

# Копируем все файлы проекта в папку веб-сервера
COPY . /var/www/html/

# Даём права на запись (для сессий и загрузок)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Добавляем ServerName, чтобы избежать предупреждений
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf