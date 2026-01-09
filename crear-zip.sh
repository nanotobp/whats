#!/bin/bash

echo "📦 Preparando aplicación para despliegue..."

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Crear directorios necesarios si no existen
echo "📁 Verificando directorios..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Limpiar logs antiguos
echo "🗑️ Limpiando logs..."
rm -f storage/logs/*.log

# Crear archivo .gitignore para el ZIP
echo "📝 Preparando archivos..."
cat > .zipignore << 'EOF'
.git
.gitignore
node_modules
.env.example
.DS_Store
tests
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
*.log
storage/logs/*
bootstrap/cache/*
.idea
.vscode
EOF

# Crear el ZIP excluyendo archivos innecesarios
echo "🗜️ Creando archivo ZIP..."
zip -r ../whatsapp-app-deploy.zip . \
  -x "*.git*" \
  -x "node_modules/*" \
  -x "*.DS_Store" \
  -x "tests/*" \
  -x ".phpunit.result.cache" \
  -x "storage/logs/*" \
  -x "bootstrap/cache/*" \
  -x ".idea/*" \
  -x ".vscode/*" \
  -x "*.log"

# Limpiar archivo temporal
rm -f .zipignore

echo ""
echo "✅ ¡Listo! Archivo creado:"
echo "📦 ../whatsapp-app-deploy.zip"
echo ""
echo "📋 Próximos pasos:"
echo "1. Sube whatsapp-app-deploy.zip a DirectAdmin"
echo "2. Descomprime en /home/usuario/"
echo "3. Configura Document Root a: /home/usuario/whatsapp-app/public"
echo "4. Ejecuta: ./fix-permissions.sh"
echo "5. Edita el archivo .env con tus credenciales"
echo "6. Ejecuta: composer install --no-dev"
echo "7. Ejecuta: php artisan migrate --force"
echo ""
echo "📖 Lee DESPLIEGUE_DIRECTADMIN.md para más detalles"
