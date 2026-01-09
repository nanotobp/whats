#!/bin/bash

# Script para corregir permisos después de subir a DirectAdmin
# Ejecutar este script después de descomprimir en el servidor

echo "🔧 Corrigiendo permisos para DirectAdmin..."

# Establecer propietario correcto (reemplaza 'usuario' con tu usuario de DirectAdmin)
# chown -R usuario:usuario .

# Permisos para directorios (755)
echo "📁 Configurando permisos de directorios..."
find . -type d -exec chmod 755 {} \;

# Permisos para archivos (644)
echo "📄 Configurando permisos de archivos..."
find . -type f -exec chmod 644 {} \;

# Permisos especiales para storage y bootstrap/cache (775)
echo "💾 Configurando permisos de storage..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Permisos ejecutables para artisan
echo "⚙️ Configurando permisos de artisan..."
chmod +x artisan

# Crear directorios si no existen
echo "📂 Verificando directorios necesarios..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Permisos para estos directorios
chmod -R 775 storage/framework
chmod -R 775 storage/logs
chmod -R 775 bootstrap/cache

echo "✅ Permisos corregidos!"
echo ""
echo "Ahora ejecuta:"
echo "  php artisan config:clear"
echo "  php artisan cache:clear"
echo "  php artisan view:clear"
