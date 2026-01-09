# 🔴 Solución Error 403 en DirectAdmin

## Causa Principal del Error 403

El error 403 (Forbidden) en DirectAdmin ocurre principalmente por:

1. **Document Root Incorrecto** ⚠️ (Causa #1 más común)
2. Permisos incorrectos de archivos/directorios
3. Archivo `.htaccess` faltante o mal configurado
4. Mod_rewrite desactivado

---

## ✅ Solución Paso a Paso

### Paso 1: Verificar Document Root (MUY IMPORTANTE)

**El Document Root DEBE apuntar a la carpeta `public` de Laravel**

1. Login en DirectAdmin
2. Ve a **Domain Setup** o **Domain Manager**
3. Selecciona tu dominio
4. Busca la opción **Document Root** o **Custom Document Root**
5. Cámbialo a:
   ```
   /home/USUARIO/whatsapp-app/public
   ```
   Reemplaza `USUARIO` con tu nombre de usuario de DirectAdmin

6. **Guarda los cambios**
7. **Espera 1-2 minutos** para que se apliquen

### Paso 2: Verificar Estructura de Archivos

Conéctate por SSH o usa File Manager en DirectAdmin:

```
/home/USUARIO/
└── whatsapp-app/           ← Carpeta principal de Laravel
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/              ← DOCUMENT ROOT debe apuntar aquí
    │   ├── .htaccess
    │   └── index.php
    ├── resources/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── .htaccess
    └── artisan
```

### Paso 3: Corregir Permisos (SSH)

Conéctate por SSH:

```bash
cd /home/USUARIO/whatsapp-app

# Método 1: Usar el script automático
chmod +x fix-permissions.sh
./fix-permissions.sh

# Método 2: Manual
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod +x artisan
```

### Paso 4: Verificar .htaccess en public/

Verifica que existe el archivo `public/.htaccess`:

```bash
cat public/.htaccess
```

Debe contener:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Si no existe, créalo con ese contenido.

### Paso 5: Verificar archivo index.php

```bash
ls -la public/index.php
```

Debe existir y tener permisos `644`:

```bash
chmod 644 public/index.php
```

### Paso 6: Limpiar Cache de Laravel

```bash
cd /home/USUARIO/whatsapp-app

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 🔍 Verificaciones Adicionales

### Verificar Propietario de Archivos

```bash
# Ver propietario actual
ls -la

# Si es necesario, cambiar propietario (reemplaza USUARIO)
chown -R USUARIO:USUARIO /home/USUARIO/whatsapp-app
```

### Verificar mod_rewrite

En DirectAdmin, verifica que `mod_rewrite` esté activo:

1. Ve a **Apache Modules** o **PHP/Apache Configuration**
2. Busca `mod_rewrite`
3. Asegúrate de que esté **activado**

### Probar Acceso Directo

Intenta acceder directamente a:

```
https://tudominio.com/index.php
```

Si funciona pero sin `/index.php` no, el problema es `mod_rewrite`.

---

## 🚨 Errores Comunes

### Error: "Document root does not exist"

**Solución**: La ruta está mal escrita. Verifica:

```bash
# Ver tu usuario
whoami

# Ver la ruta completa
pwd
```

La ruta correcta debe ser:
```
/home/TU_USUARIO_REAL/whatsapp-app/public
```

### Error: "Permission denied"

**Solución**: Permisos incorrectos. Ejecuta:

```bash
cd /home/USUARIO/whatsapp-app
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Error: ".htaccess not found"

**Solución**: El archivo `.htaccess` está oculto. Verifica:

```bash
ls -la public/.htaccess
```

Si no existe, créalo desde File Manager marcando "Show hidden files".

---

## 📱 Verificación Final

Después de todos los pasos, verifica:

```bash
# 1. Document Root correcto
cat /usr/local/directadmin/data/users/USUARIO/domains/tudominio.com.conf | grep documentroot

# 2. Permisos correctos
ls -la public/

# 3. .htaccess existe
cat public/.htaccess

# 4. index.php existe
cat public/index.php | head -n 5

# 5. Prueba la aplicación
curl -I https://tudominio.com
```

---

## ✅ Checklist de Solución 403

- [ ] Document Root apunta a `/home/USUARIO/whatsapp-app/public`
- [ ] Esperé 1-2 minutos después de cambiar Document Root
- [ ] Archivo `public/index.php` existe
- [ ] Archivo `public/.htaccess` existe y tiene el contenido correcto
- [ ] Permisos: directorios 755, archivos 644
- [ ] Storage y bootstrap/cache tienen permisos 775
- [ ] mod_rewrite está activo en Apache
- [ ] Cache de Laravel limpiado
- [ ] Propietario de archivos es correcto

---

## 🆘 Si Todo Falla

1. **Revisa los logs de Apache**:
   ```bash
   tail -f /var/log/httpd/domains/tudominio.com.error.log
   ```

2. **Revisa los logs de Laravel**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Intenta con un archivo HTML simple**:
   Crea `public/test.html`:
   ```html
   <!DOCTYPE html>
   <html>
   <body>
       <h1>Test OK</h1>
   </body>
   </html>
   ```

   Accede a: `https://tudominio.com/test.html`

   - Si funciona: El problema es con Laravel/PHP
   - Si no funciona: El problema es con Document Root o Apache

4. **Contacta al soporte de tu hosting** con:
   - Los logs de error
   - Capturas de tu configuración de Document Root
   - Confirmación de que mod_rewrite está activo
