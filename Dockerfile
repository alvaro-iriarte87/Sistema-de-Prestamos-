# Usa una imagen base que tenga PHP y Composer
FROM php:8.1-cli

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar Node.js y npm
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Instalar extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Crear un directorio de trabajo y copiar el código de la aplicación
WORKDIR /app
COPY . .

# Instalar dependencias de PHP y Node.js
RUN composer install --no-dev --optimize-autoloader
RUN npm install

# Construir la aplicación de Vue.js
RUN npm run build

# Iniciar Laravel y Vue.js
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=80 & npm run dev"]
