FROM php:8.4-cli

# Instalar extensiones necesarias para Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

# Configurar Zona Horaria a nivel de Sistema Operativo
ENV TZ=America/Lima
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Establecer directorio de trabajo
WORKDIR /var/www/html
