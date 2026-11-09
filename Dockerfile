Dockerfile
FROM php:8.4-apache

# Instalar dependencias del sistema y librerías de PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Instalar Composer oficial dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar extensiones de PHP incluyendo MySQL y PostgreSQL
RUN docker-php-ext-install pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Configurar Apache para apuntar a la carpeta public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

RUN a2enmod rewrite

COPY . /var/www/html

WORKDIR /var/www/html

# Instalar dependencias de Composer sin entorno de desarrollo
RUN composer install --no-dev --optimize-autoloader

# Crear archivos de log y cache si no existen y asignar permisos totales a www-data
RUN mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copiar y dar permisos al script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
