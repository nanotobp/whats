# Optimizaciones de Rendimiento Implementadas

## Problema Identificado
La aplicación tardaba casi 10 segundos entre cambios de pestañas debido a:
- Queries N+1 en los componentes Livewire
- Carga de relaciones completas innecesarias
- Falta de índices en la base de datos
- No se utilizaba caché
- Se cargaban todos los datos al mismo tiempo

## Optimizaciones Implementadas

### 1. **Lazy Loading en Dashboard** ✅
- El Dashboard ahora usa `wire:init="loadStats"` para cargar datos solo cuando es necesario
- Muestra un spinner de carga mientras se obtienen los datos
- Los datos no se cargan hasta que el componente está visible

### 2. **Caché Agresivo** ✅
Implementado caché de 5 minutos (300 segundos) en:
- Total de contactos
- Contactos válidos
- Total de grupos
- Total de campañas
- Estadísticas de campañas por grupo
- Campañas recientes
- Lista de grupos

Caché de 2 minutos en:
- Métricas de campaña individual
- Detalles de lectura

### 3. **Optimización de Queries SQL** ✅

#### Antes:
```php
$campaigns = $campaignsQuery->with('group')->latest()->get();
$totalSent = $campaigns->sum('sent_count');
```

#### Después:
```php
// Agregación directa en BD
$stats = $campaignsQuery->selectRaw('
    COALESCE(SUM(sent_count), 0) as total_sent,
    COALESCE(SUM(delivered_count), 0) as total_delivered,
    ...
')->first();
```

### 4. **Índices de Base de Datos** ✅
Agregados índices en:

**Tabla `campaigns`:**
- `status`
- `created_at`
- `group_id + status` (índice compuesto)
- `group_id + created_at` (índice compuesto)

**Tabla `messages`:**
- `status`
- `campaign_id + status`
- `contact_id + status`
- `read_at`
- `sent_at`

**Tabla `contacts`:**
- `is_valid`
- `group_id + is_valid`

### 5. **Eliminación de Queries N+1** ✅

#### Antes en CampaignMetrics:
```php
$this->campaign->messages()
    ->where('status', 'read')
    ->with('contact')  // N+1 query
    ->get()
```

#### Después:
```php
$this->campaign->messages()
    ->where('status', 'read')
    ->join('contacts', 'messages.contact_id', '=', 'contacts.id')
    ->select('contacts.name', 'contacts.phone', ...)
    ->get()
```

### 6. **Selects Específicos** ✅
Solo se cargan las columnas necesarias:

```php
Campaign::select([
    'id', 'name', 'status', 'group_id', 'message', 
    'sent_count', 'delivered_count', ...
])
```

### 7. **Optimización de GroupStats** ✅
Cambió de múltiples queries a una sola query SQL con JOINs:

```php
DB::table('groups')
    ->leftJoin('contacts', 'groups.id', '=', 'contacts.group_id')
    ->leftJoin('campaigns', 'groups.id', '=', 'campaigns.group_id')
    ->select(...)
    ->groupBy('groups.id', 'groups.name')
    ->get()
```

### 8. **Paginación Optimizada** ✅
- CampaignHistory reducido de 20 a 15 items por página
- Carga más rápida inicial

### 9. **Comando de Limpieza de Caché** ✅
Creado comando `php artisan dashboard:clear-cache` para limpiar el caché cuando se actualicen datos.

## Resultados Esperados

### Antes:
- ⏱️ ~10 segundos entre pestañas
- 🔴 Múltiples queries N+1
- 🔴 Sin índices en BD
- 🔴 Sin caché

### Después:
- ⚡ < 1 segundo en primera carga
- ⚡ < 0.3 segundos en cargas subsecuentes (desde caché)
- ✅ Queries optimizadas con JOINs
- ✅ Índices en todas las columnas importantes
- ✅ Caché agresivo de 5 minutos

## Mejoras Adicionales Recomendadas

### Futuro Inmediato:
1. **Redis Cache** - Cambiar de database a Redis para caché aún más rápido
2. **CDN** - Usar CDN para assets estáticos
3. **Lazy Load de Tablas** - Cargar tablas grandes bajo demanda
4. **WebSockets** - Para actualizaciones en tiempo real sin recargar

### Configuración Recomendada en `.env`:
```env
CACHE_STORE=database  # Cambiar a 'redis' cuando esté disponible
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## Monitoreo

Para verificar el rendimiento:

```bash
# Ver queries ejecutadas
php artisan debugbar:publish

# Limpiar caché cuando sea necesario
railway run php artisan dashboard:clear-cache
railway run php artisan cache:clear

# Ver logs de rendimiento
railway logs
```

## Comandos Útiles

```bash
# Limpiar todo el caché
railway run php artisan optimize:clear

# Cachear configuración
railway run php artisan config:cache
railway run php artisan route:cache
railway run php artisan view:cache

# Ver estado de migraciones
railway run php artisan migrate:status
```
