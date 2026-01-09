# 🚀 Guía de Despliegue en DirectAdmin

## Error 403 - Solución

El error 403 en DirectAdmin ocurre típicamente por:
1. **Permisos incorrectos** después de descomprimir
2. **Falta del archivo .htaccess**
3. **Document Root incorrecto**

---

## 📋 Pasos para Desplegar

### 1. Preparar el archivo ZIP

Desde tu Mac, ejecuta:

```bash
# Limpiar cache antes de comprimir
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Crear el ZIP (sin node_modules ni archivos innecesarios)
zip -r whatsapp-app.zip . -x "*.git*" "node_modules/*" "*.DS_Store" "storage/logs/*" "bootstrap/cache/*"
```

### 2. Subir a DirectAdmin

1. **Login en DirectAdmin**
2. Ve a **File Manager**
3. Sube `whatsapp-app.zip` a tu directorio home (no a `public_html`)
4. **Descomprime** el archivo
5. Deberías tener esta estructura:
   ```
   /home/usuario/
   ├── whatsapp-app/
   │   ├── app/
   │   ├── public/
   │   ├── storage/
   │   ├── .env
   │   └── ...
   ```

### 3. Configurar Document Root

**IMPORTANTE:** El Document Root debe apuntar a la carpeta `public`

1. En DirectAdmin, ve a **Domain Setup**
2. Selecciona tu dominio
3. Cambia el **Document Root** a:
   ```
   /home/usuario/whatsapp-app/public
   ```
4. Guarda los cambios

### 4. Corregir Permisos

Conéctate por SSH y ejecuta:

```bash
cd /home/usuario/whatsapp-app

# Dar permisos de ejecución al script
chmod +x fix-permissions.sh

# Ejecutar el script de permisos
./fix-permissions.sh
```

O manualmente:

```bash
# Permisos generales
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# Permisos especiales para storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Artisan ejecutable
chmod +x artisan
```

### 5. Configurar Variables de Entorno

Edita el archivo `.env`:

```bash
nano .env
```

Asegúrate de tener:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# Base de datos Supabase
DB_CONNECTION=pgsql
DB_HOST=db.exuzhgusqbfaavrtvcer.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=0vX05KIBzC4k8F2w
DB_SSLMODE=require

# Supabase Storage
SUPABASE_URL=https://exuzhgusqbfaavrtvcer.supabase.co
SUPABASE_ANON_KEY=tu_anon_key
SUPABASE_SERVICE_KEY=tu_service_key

# Green API
GREEN_API_INSTANCE_ID=7105462109
GREEN_API_TOKEN=cf31c4ac6115434d99bca04d413f1d3a4a7b6815123a4414b9
```

### 6. Instalar Dependencias y Migrar

```bash
cd /home/usuario/whatsapp-app

# Instalar dependencias de Composer
composer install --optimize-autoloader --no-dev

# Limpiar y optimizar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (si aún no lo hiciste)
php artisan migrate --force
```

### 7. Verificar PHP

DirectAdmin debe tener PHP 8.1 o superior:

```bash
php -v
```

Si necesitas cambiar la versión de PHP:
1. Ve a **PHP Version Selector** en DirectAdmin
2. Selecciona PHP 8.1 o superior

---

## 🔧 Solución de Problemas

### Error 403 Persistente

1. **Verifica el Document Root**
   ```
   Debe ser: /home/usuario/whatsapp-app/public
   No: /home/usuario/public_html
   ```

2. **Verifica permisos de .htaccess**
   ```bash
   chmod 644 public/.htaccess
   chmod 644 .htaccess
   ```

3. **Verifica que el archivo index.php existe**
   ```bash
   ls -la /home/usuario/whatsapp-app/public/index.php
   ```

4. **Verifica los logs de Apache**
   ```bash
   tail -f /var/log/httpd/domains/tudominio.com.error.log
   ```

### Error 500

1. **Revisa los logs de Laravel**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verifica permisos de storage**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

3. **Regenera el key**
   ```bash
   php artisan key:generate
   ```

### No se puede conectar a Supabase

1. **Verifica que tienes la extensión pgsql**
   ```bash
   php -m | grep pgsql
   ```

2. **Si no está instalada, instálala desde DirectAdmin:**
   - Ve a **PHP Extensions**
   - Activa `pgsql` y `pdo_pgsql`

3. **Verifica la conexión**
   ```bash
   php artisan tinker
   # Dentro de tinker:
   DB::connection()->getPdo();
   ```

---

## 📦 Estructura Correcta en Servidor

```
/home/usuario/
├── whatsapp-app/              # Raíz de Laravel
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                 # Document Root debe apuntar aquí
│   │   ├── .htaccess
│   │   └── index.php
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   │   ├── app/
│   │   ├── framework/
│   │   └── logs/
│   ├── vendor/
│   ├── .env
│   ├── .htaccess
│   ├── artisan
│   └── composer.json
```

---

## ✅ Checklist Final

- [ ] Archivo ZIP subido y descomprimido
- [ ] Document Root configurado a `/public`
- [ ] Permisos corregidos (755 dirs, 644 files, 775 storage)
- [ ] `.env` configurado correctamente
- [ ] Composer dependencies instaladas
- [ ] Migraciones ejecutadas
- [ ] Cache optimizado
- [ ] PHP 8.1+ activo
- [ ] Extensión pgsql instalada
- [ ] Conexión a Supabase verificada

---

## 🆘 Soporte

Si sigues teniendo problemas:

1. Verifica los logs: `tail -f storage/logs/laravel.log`
2. Verifica Apache logs en DirectAdmin
3. Intenta acceder a: `https://tudominio.com/index.php` directamente
4. Verifica que mod_rewrite está activo en Apache
