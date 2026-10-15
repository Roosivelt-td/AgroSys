FROM php:8.4-cli

# Instalar dependencias del sistema y extensiones necesarias para MySQL y PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    tzdata \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Zona Horaria a nivel de Sistema Operativo
ENV TZ=America/Lima
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Establecer directorio de trabajo
WORKDIR /var/www/html
