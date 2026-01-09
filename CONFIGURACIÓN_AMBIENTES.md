# 🔄 Configuración de Ambientes (Local vs Producción)

## 📍 Situación Actual

Tu proyecto ahora soporta **dos ambientes**:

1. **Local (desarrollo)** - Usa SQLite
2. **Producción (DirectAdmin)** - Usa PostgreSQL/Supabase

---

## 💻 Desarrollo Local (tu Mac)

### Archivo: `.env` (configurado para SQLite)

```env
DB_CONNECTION=sqlite
```

### Para trabajar localmente:

```bash
# 1. Asegúrate de tener SQLite
php -m | grep sqlite

# 2. Crea la base de datos
touch database/database.sqlite

# 3. Ejecuta migraciones
php artisan migrate:fresh

# 4. Inicia el servidor
php artisan serve
```

### ✅ Ventajas en Local:

- No necesitas conexión a internet para la DB
- Más rápido para desarrollo
- Base de datos en un solo archivo
- No consume recursos de Supabase

---

## 🚀 Producción (DirectAdmin/Supabase)

### Archivo: `.env.production` (configurado para PostgreSQL)

```env
DB_CONNECTION=pgsql
DB_HOST=db.exuzhgusqbfaavrtvcer.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=0vX05KIBzC4k8F2w
```

### Para desplegar en producción:

1. **Crea el ZIP con el script:**
   ```bash
   ./crear-zip.sh
   ```

2. **Sube a DirectAdmin** el archivo `whatsapp-app-deploy.zip`

3. **En el servidor, copia el archivo de producción:**
   ```bash
   cd /home/usuario/whatsapp-app
   cp .env.production .env
   ```

4. **Edita el `.env` para ajustar:**
   ```bash
   nano .env
   ```

   Cambia:
   - `APP_URL` a tu dominio real
   - `APP_KEY` (genera uno nuevo con `php artisan key:generate`)
   - Cualquier otra configuración específica

5. **Ejecuta las migraciones:**
   ```bash
   php artisan migrate --force
   ```

---

## 🔀 Cambiar Entre Ambientes

### De Local a Producción:

```bash
# Opción 1: Copiar el archivo
cp .env.production .env

# Opción 2: Editar manualmente
nano .env
# Cambia DB_CONNECTION de sqlite a pgsql
```

### De Producción a Local:

```bash
# Restaurar configuración local
# Edita .env y cambia a:
DB_CONNECTION=sqlite

# Recrear base de datos SQLite
touch database/database.sqlite
php artisan migrate:fresh
```

---

## 📦 Archivos de Configuración

```
/whatsapp-app/
├── .env                    ← Local (SQLite) - No incluir en ZIP
├── .env.production         ← Producción (PostgreSQL/Supabase)
├── .env.example            ← Plantilla de ejemplo
└── database/
    └── database.sqlite     ← Base de datos local
```

---

## ⚠️ Importante

### ❌ NO subas el `.env` local al servidor

El archivo `.env` de tu Mac tiene configuración de SQLite que no funcionará en producción.

**Siempre usa `.env.production` en el servidor:**

```bash
# En DirectAdmin después de descomprimir:
cd /home/usuario/whatsapp-app
rm .env                  # Eliminar el .env de desarrollo
cp .env.production .env  # Usar el de producción
```

### ✅ Checklist antes de desplegar:

- [ ] El ZIP fue creado con `./crear-zip.sh`
- [ ] En el servidor, copiar `.env.production` a `.env`
- [ ] Editar `.env` con el `APP_URL` correcto
- [ ] Ejecutar `php artisan key:generate --force`
- [ ] Ejecutar `php artisan migrate --force`
- [ ] Ejecutar `php artisan config:cache`

---

## 🗄️ Diferencias entre Ambientes

| Característica | Local (SQLite) | Producción (Supabase) |
|----------------|----------------|----------------------|
| Base de datos | SQLite | PostgreSQL |
| Ubicación | `database/database.sqlite` | Cloud (Supabase) |
| Almacenamiento | `storage/app/public` | Supabase Storage |
| Velocidad | Muy rápida | Depende de internet |
| Backups | Manual | Automático |
| Escalabilidad | Limitada | Alta |

---

## 🔍 Verificar Ambiente Actual

```bash
# Ver qué base de datos estás usando
php artisan tinker

# Dentro de tinker:
config('database.default')
# Debería retornar: "sqlite" (local) o "pgsql" (producción)
```

---

## 🛠️ Troubleshooting

### Error: "could not translate host name"

**Problema:** Intentas conectar a Supabase en local

**Solución:** Cambia a SQLite en `.env`:
```env
DB_CONNECTION=sqlite
```

### Error: "database.sqlite does not exist"

**Solución:**
```bash
touch database/database.sqlite
php artisan migrate:fresh
```

### Las imágenes no se suben en local

**Normal:** En local, las imágenes se guardan en `storage/app/public`. En producción se suben a Supabase Storage.

Para ver las imágenes en local:
```bash
php artisan storage:link
```

---

## 📋 Flujo de Trabajo Recomendado

### 1. Desarrollar en Local

```bash
# Usar SQLite
DB_CONNECTION=sqlite

# Desarrollar y probar
php artisan serve
```

### 2. Preparar para Producción

```bash
# Crear el ZIP
./crear-zip.sh
```

### 3. Desplegar

```bash
# En el servidor
cp .env.production .env
nano .env  # Ajustar configuraciones
php artisan migrate --force
php artisan config:cache
```

### 4. Verificar

```bash
# Verificar que funciona
curl -I https://tudominio.com
```

---

## 🎯 Resumen

- **Local:** SQLite, rápido, sin internet necesaria
- **Producción:** PostgreSQL/Supabase, escalable, con backups
- **Nunca** mezclar archivos `.env` entre ambientes
- **Siempre** usar el script `crear-zip.sh` para desplegar
