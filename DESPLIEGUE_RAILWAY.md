# 🚂 Despliegue en Railway

## 📋 Requisitos Previos

- Cuenta en [Railway](https://railway.app)
- Cuenta en [Supabase](https://supabase.com) con proyecto configurado
- Repositorio GitHub: https://github.com/nanotobp/whats.git

---

## 🚀 Pasos para Desplegar

### 1. Crear Nuevo Proyecto en Railway

1. Ve a https://railway.app
2. Click en **New Project**
3. Selecciona **Deploy from GitHub repo**
4. Autoriza Railway a acceder a tu cuenta de GitHub
5. Selecciona el repositorio: `nanotobp/whats`
6. Railway detectará automáticamente que es una aplicación Laravel

### 2. Configurar Variables de Entorno

En Railway, ve a **Variables** y agrega las siguientes:

#### Variables de Aplicación

```env
APP_NAME=WhatsApp-ZN
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-app.up.railway.app
```

**IMPORTANTE**: Genera el `APP_KEY` ejecutando localmente:
```bash
php artisan key:generate --show
```

#### Variables de Base de Datos (Supabase)

```env
DB_CONNECTION=pgsql
DB_HOST=db.exuzhgusqbfaavrtvcer.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=0vX05KIBzC4k8F2w
DB_SSLMODE=require
```

#### Variables de Sesión y Cache

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

#### Variables de ZN API (Green API)

```env
GREEN_API_INSTANCE_ID=7105462109
GREEN_API_TOKEN=cf31c4ac6115434d99bca04d413f1d3a4a7b6815123a4414b9
```

#### Variables de Supabase Storage

```env
SUPABASE_URL=https://exuzhgusqbfaavrtvcer.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImV4dXpoZ3VzcWJmYWF2cnR2Y2VyIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjUyMTM0OTgsImV4cCI6MjA4MDc4OTQ5OH0.YtINo5CdHsGWNKJ72kYJolhnfXhcl3UszmUWt157Kns
SUPABASE_SERVICE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImV4dXpoZ3VzcWJmYWF2cnR2Y2VyIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2NTIxMzQ5OCwiZXhwIjoyMDgwNzg5NDk4fQ.Zwre1gNLKvz1DcBPWtrnTtCbaA5geTAX5Vv84qxmKM8
```

#### Variables de Log

```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error
```

### 3. Configurar Dominio (Opcional)

1. En Railway, ve a **Settings** > **Domains**
2. Railway te dará un dominio automático: `tu-app.up.railway.app`
3. Opcionalmente, puedes agregar tu dominio personalizado
4. Actualiza `APP_URL` con tu dominio

### 4. Configurar RLS en Supabase

En Supabase SQL Editor, ejecuta el script:

```bash
# El archivo está en tu proyecto:
database/supabase_rls_policies.sql
```

Copia y pega el contenido completo en el SQL Editor de Supabase y ejecuta.

### 5. Verificar Despliegue

Railway automáticamente:
- ✅ Instalará dependencias con Composer
- ✅ Ejecutará `npm install` y `npm run build`
- ✅ Ejecutará las migraciones (`php artisan migrate --force`)
- ✅ Generará cache de configuración
- ✅ Iniciará el servidor

Monitorea el proceso en la pestaña **Deployments** de Railway.

---

## 🔧 Configuración Adicional

### Worker para Queue (Opcional)

Si quieres procesar trabajos en background:

1. En Railway, click en **+ New**
2. Selecciona **Empty Service**
3. Vincúlalo al mismo repositorio
4. En **Settings** > **Start Command**, pon:
   ```
   php artisan queue:work --tries=3 --timeout=90
   ```
5. Agrega las mismas variables de entorno

### Configurar Webhook de Green API

Una vez desplegado, configura el webhook en Green API:

```
https://tu-app.up.railway.app/api/webhook
```

Métodos permitidos: `POST`

---

## 🔍 Verificación Post-Despliegue

### 1. Verificar que la Aplicación Está Activa

```bash
curl -I https://tu-app.up.railway.app
```

Deberías ver: `HTTP/2 200`

### 2. Verificar Base de Datos

```bash
# En Railway CLI o mediante shell
php artisan tinker
# Ejecuta:
\DB::connection()->getPdo();
```

### 3. Verificar Conexión a Supabase Storage

```bash
# Prueba subiendo una imagen desde la interfaz
# Ve a /campaigns y crea una campaña con imagen
```

### 4. Verificar Queue

```bash
php artisan queue:work --once
```

---

## 📊 Monitoreo

### Logs en Railway

1. Ve a tu servicio en Railway
2. Click en la pestaña **Logs**
3. Filtra por nivel: `error`, `warning`, `info`

### Logs de Laravel

Los logs se escriben en `storage/logs/laravel.log`, pero Railway los captura automáticamente.

### Métricas

Railway proporciona:
- CPU usage
- Memory usage
- Network traffic
- Request rate

---

## 🔄 Actualizar Despliegue

### Desde Git (Automático)

Railway detecta cambios automáticamente:

```bash
git add .
git commit -m "Update: descripción del cambio"
git push origin main
```

Railway desplegará automáticamente.

### Forzar Re-despliegue

En Railway:
1. Ve a **Deployments**
2. Click en los 3 puntos del último despliegue
3. Selecciona **Redeploy**

---

## ⚠️ Troubleshooting

### Error: "Application key not set"

**Solución**: Genera una nueva APP_KEY:

```bash
php artisan key:generate --show
```

Copia el resultado y agrégalo en Railway Variables.

### Error: "Connection refused" (Base de datos)

**Solución**: Verifica las credenciales de Supabase:

1. Ve a Supabase > Project Settings > Database
2. Copia las credenciales correctas
3. Actualiza las variables en Railway

### Error: "Storage bucket not found"

**Solución**: 

1. Ve a Supabase > Storage
2. Crea el bucket "archivos" si no existe
3. Configura como público
4. Aplica las políticas RLS desde `database/supabase_rls_policies.sql`

### Error: "Queue not processing"

**Solución**: Configura el worker separado (ver sección de Worker arriba)

### Imágenes no se suben

**Verificaciones**:

1. Bucket "archivos" existe en Supabase
2. Bucket es público
3. SUPABASE_SERVICE_KEY está configurado correctamente
4. RLS policies aplicadas

---

## 🎯 Checklist de Despliegue

- [ ] Proyecto creado en Railway
- [ ] Repositorio GitHub vinculado
- [ ] Todas las variables de entorno configuradas
- [ ] APP_KEY generado y configurado
- [ ] Base de datos Supabase conectada
- [ ] Migraciones ejecutadas correctamente
- [ ] Bucket "archivos" creado en Supabase Storage
- [ ] RLS policies aplicadas en Supabase
- [ ] Webhook configurado en Green API
- [ ] Aplicación accesible desde el dominio
- [ ] Login funciona correctamente
- [ ] Creación de campañas funciona
- [ ] Subida de imágenes funciona
- [ ] Envío de mensajes funciona
- [ ] Worker configurado (opcional)

---

## 📱 Comandos Útiles en Railway CLI

Instala Railway CLI:

```bash
npm i -g @railway/cli
```

Comandos:

```bash
# Login
railway login

# Vincular proyecto
railway link

# Ver logs en vivo
railway logs

# Abrir shell
railway shell

# Ejecutar comando
railway run php artisan migrate

# Ver variables
railway variables
```

---

## 💡 Tips

1. **Usa Railway CLI** para debugging rápido
2. **Monitorea los logs** regularmente durante los primeros días
3. **Configura alertas** en Railway para recibir notificaciones
4. **Usa el worker** para procesar mensajes en background de forma más eficiente
5. **Habilita auto-deploy** desde main para despliegues automáticos

---

## 🆘 Soporte

- **Railway Docs**: https://docs.railway.app
- **Supabase Docs**: https://supabase.com/docs
- **Laravel Docs**: https://laravel.com/docs

---

## 📋 Resumen de URLs

- **Aplicación**: https://tu-app.up.railway.app
- **Panel Railway**: https://railway.app/project/tu-proyecto
- **Panel Supabase**: https://supabase.com/dashboard/project/exuzhgusqbfaavrtvcer
- **GitHub Repo**: https://github.com/nanotobp/whats

---

## ✅ Verificación Final

Una vez desplegado, prueba:

1. Login: `https://tu-app.up.railway.app/login`
2. Dashboard: Ver métricas
3. Crear grupo: Subir CSV con contactos
4. Crear campaña: Con imagen y texto
5. Enviar campaña: Verificar que los mensajes se envíen
6. Ver historial: Verificar campañas completadas

Si todo funciona, ¡tu aplicación está lista! 🎉
