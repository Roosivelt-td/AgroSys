FROM php:8.4-fpm

# Instalar dependencias, Nginx, Node y extensiones
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libpng-dev libonig-dev libxml2-dev zip nginx supervisor \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# Instalar dependencias PHP y compilar assets
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# Permisos de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx (escuchando en el puerto 80)
RUN rm /etc/nginx/sites-enabled/default
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php index.html; \
    location / { try_files $uri $uri/ /index.php?$query_string; } \
    location ~ \.php$ { \
        include snippets/fastcgi-php.conf; \
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock; \
    } \
}' > /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Configurar Supervisor para Nginx y PHP-FPM
RUN echo '[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm\nautostart=true\nautorestart=true\n\n[program:nginx]\ncommand=nginx -g "daemon off;"\nautostart=true\nautorestart=true' > /etc/supervisor/conf.d/supervisord.conf

# Script de arranque: SOLO migraciones, SIN cachear config
RUN echo '#!/usr/bin/env bash\n\
rm -f /var/www/html/.env\n\
rm -f /var/www/html/.env.example\n\
php artisan config:clear\n\
php artisan migrate --force\n\
supervisord -c /etc/supervisor/conf.d/supervisord.conf' > /usr/local/bin/deploy.sh && chmod +x /usr/local/bin/deploy.sh

EXPOSE 80
CMD ["/usr/local/bin/deploy.sh"]