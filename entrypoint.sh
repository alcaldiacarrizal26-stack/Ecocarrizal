#!/bin/bash

# Si no existe un archivo .env pero existe el .env.example, créalo para que Laravel lea las variables de Render
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

# Generar APP_KEY si las variables de entorno están vacías
php artisan key:generate --force --no-interaction

# Crear el archivo SQLite si tu aplicación usa SQLite y no existe
if [ "$DB_CONNECTION" = "sqlite" ] && [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Limpiar y optimizar la configuración para producción
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Iniciar Apache en primer plano
apache2-foreground
