#!/bin/bash

# Generar APP_KEY si no está definida en las variables de entorno
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Ejecutar migraciones o limpiar caché si es necesario
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Iniciar Apache en primer plano
apache2-foreground
