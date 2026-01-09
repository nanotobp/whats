# WhatsApp Masivo - Sistema de Envíos

Plataforma Laravel para envío masivo de mensajes de WhatsApp usando Green API.

## Características

✅ Envío masivo de mensajes a 9,000+ contactos
✅ Validación automática de números válidos de WhatsApp
✅ Upload de contactos vía CSV
✅ Categorización de contactos por grupos
✅ Creador de mensajes con texto, links e imágenes
✅ Sistema de colas con rate limiting (wait entre envíos)
✅ Dashboard con métricas de apertura y entrega
✅ Estadísticas individuales y por grupos
✅ Rate limiting automático para evitar bloqueos

## Requisitos

- PHP 8.2 o superior
- Composer
- SQLite (o MySQL/PostgreSQL)
- Cuenta en Green API (https://green-api.com)

## Instalación

### 1. Instalar dependencias

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configurar Green API

Regístrate en https://green-api.com y obtén tu Instance ID y API Token.

Edita `.env`:

```env
GREEN_API_INSTANCE_ID=tu_instance_id
GREEN_API_TOKEN=tu_api_token
```

### 3. Migrar base de datos

```bash
php artisan migrate
php artisan storage:link
```

### 4. Iniciar la aplicación

```bash
# Terminal 1 - Servidor
php artisan serve

# Terminal 2 - Queue worker (IMPORTANTE)
php artisan queue:work --queue=whatsapp,validation
```

Visita: http://localhost:8000

## Uso Rápido

1. **Crear Grupos**: Ve a "Grupos" y crea categorías
2. **Subir CSV**: Ve a "Contactos" y sube tu archivo CSV (phone,name)
3. **Crear Campaña**: Ve a "Campañas", crea mensaje y envía
4. **Ver Métricas**: Dashboard muestra estadísticas en tiempo real

## Costos Green API

- **Plan Business**: ~$50-80 USD/mes (mensajes ilimitados)
- **Sin costo por mensaje** (a diferencia de WhatsApp API oficial)
- Para 36,000 mensajes/día: **$50-80/mes**

### ¿Por qué es económico?

Green API usa WhatsApp Web (no la API oficial de Meta), por eso no cobra por mensaje.

**Comparación con WhatsApp Business API oficial:**
- API Oficial: ~$0.004-0.01 por mensaje
- 36,000 mensajes/día = $150-350/mes SOLO en mensajes
- Green API: $50-80/mes total

## Seguridad

⚠️ Green API usa técnica no oficial (WhatsApp Web)
✅ Bajo riesgo para uso interno con empleados
❌ No usar para spam o marketing masivo

## Troubleshooting

**Los mensajes no se envían:**
```bash
php artisan queue:work --queue=whatsapp,validation
```

**Ver logs:**
```bash
tail -f storage/logs/laravel.log
```

## Arquitectura

```
Laravel 11 + Livewire + Green API + SQLite
├── Jobs + Queue (envíos asincrónicos)
├── Rate Limiting (3 seg entre mensajes)
└── Dashboard con métricas en tiempo real
```

## Licencia

Uso privado - No redistribuir
