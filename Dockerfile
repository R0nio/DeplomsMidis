# Используем PHP 8.4 с FPM на Alpine
FROM php:8.4-fpm-alpine

# Устанавливаем Nginx и необходимые расширения
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    libpq

# Устанавливаем PHP расширения для PostgreSQL
RUN docker-php-ext-install pdo_pgsql pgsql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем рабочую директорию
WORKDIR /var/www/html

# Копируем все файлы проекта
COPY . .

# Устанавливаем PHP зависимости (без dev пакетов)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Настраиваем права на storage и cache
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Создаем конфигурацию Nginx
RUN echo 'server { \
    listen 8080; \
    server_name localhost; \
    root /var/www/html/public; \
    index index.php; \
    charset utf-8; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location = /favicon.ico { access_log off; log_not_found off; } \
    location = /robots.txt { access_log off; log_not_found off; } \
    error_page 404 /index.php; \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
}' > /etc/nginx/http.d/default.conf

# Создаем простой скрипт запуска (без Supervisor)
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

# Открываем порт 8080
EXPOSE 8080

# Запускаем скрипт
CMD ["/start.sh"]