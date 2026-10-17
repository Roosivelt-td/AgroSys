# Imagen base de PHP con FPM
FROM php:8.4-fpm

# 1. Instalar dependencias del sistema, Nginx, Node.js y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Node.js (Versión 20)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html
COPY . /var/www/html

# 2. Instalar dependencias de PHP (Producción) y compilar Assets (Vite)
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# 3. Configurar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Configurar Nginx para que apunte a la carpeta /public de Laravel
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

# 5. Configurar Supervisor para correr Nginx y PHP-FPM al mismo tiempo
RUN echo '[supervisord]\nnodaemon=true\n\n[program:php-fpm]\ncommand=php-fpm\nautostart=true\nautorestart=true\n\n[program:nginx]\ncommand=nginx -g "daemon off;"\nautostart=true\nautorestart=true' > /etc/supervisor/conf.d/supervisord.conf

# 6. Crear el script de despliegue con LIMPIEZA DE CACHÉ incluida
RUN echo '#!/usr/bin/env bash\n\
echo "Clearing old config cache..."\n\
php artisan config:clear\n\
php artisan cache:clear\n\
echo "Running migrations..."\n\
php artisan migrate --force\n\
echo "Caching config for production..."\n\
php artisan config:cache\n\
echo "Caching routes..."\n\
php artisan route:cache\n\
echo "Starting services..."' > /usr/local/bin/deploy.sh && chmod +x /usr/local/bin/deploy.sh

# Exponer puerto y arrancar
EXPOSE 80
CMD ["/usr/local/bin/deploy.sh"]