#!/bin/sh
set -e

echo "🚀 Iniciando despliegue..."

# 1. Ejecutar migraciones
echo "🛠 Ejecutando migraciones..."
php artisan migrate --force

# 2. Iniciar Apache
echo "🌐 Iniciando servidor Apache..."
exec apache2-foreground