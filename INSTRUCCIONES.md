# Instrucciones de Uso - WhatsApp Masivo

## ⚡ Inicio Rápido

### 1. Configurar Green API

Antes de usar la plataforma, necesitas:

1. Ir a https://green-api.com y crear una cuenta
2. Crear una instancia de WhatsApp
3. Escanear el código QR con tu WhatsApp
4. Copiar tu **Instance ID** y **API Token**

### 2. Configurar el archivo .env

Abre el archivo `.env` y actualiza estas líneas:

```env
GREEN_API_INSTANCE_ID=1234567890  # Tu Instance ID
GREEN_API_TOKEN=tu_token_aqui     # Tu API Token
```

### 3. Iniciar la aplicación

Abre 2 terminales:

**Terminal 1 - Servidor web:**
```bash
php artisan serve
```

**Terminal 2 - Worker de colas (MUY IMPORTANTE):**
```bash
php artisan queue:work --queue=whatsapp,validation
```

⚠️ **IMPORTANTE**: El worker de colas DEBE estar corriendo para que se envíen los mensajes.

### 4. Acceder a la plataforma

Abre tu navegador en: http://localhost:8000

## 📋 Flujo de Trabajo

### Paso 1: Crear Grupos (Opcional)

1. Clic en "Grupos" en el sidebar
2. Crear grupos como: "Gerencia", "Operarios", "Administración"
3. Los grupos te permiten categorizar empleados

### Paso 2: Subir Contactos

1. Preparar un archivo CSV con este formato:

```csv
phone,name
5491112345678,Juan Pérez
5491187654321,María González
5491198765432,Pedro Rodríguez
```

**Importante:**
- La columna puede llamarse `phone`, `telefono` o `numero`
- La columna puede llamarse `name` o `nombre`
- Los números deben incluir código de país (ej: 549 para Argentina)

2. Ir a "Contactos"
3. Seleccionar un grupo (opcional)
4. Marcar "Validar números" para que el sistema verifique cuáles son válidos
5. Subir el archivo CSV
6. Esperar a que se importen

**Ejemplo para Argentina:**
- Formato: 549 + código de área (sin 0) + número
- Buenos Aires: 5491112345678
- Córdoba: 5493512345678

### Paso 3: Crear Campaña

1. Ir a "Campañas"
2. Clic en el botón de crear campaña
3. Completar:
   - **Nombre**: "Comunicado Importante - Enero 2026"
   - **Contenido**: Tu mensaje (puedes incluir links)
   - **Imagen**: Subir una imagen (opcional)
   - **Destinatarios**:
     - "Enviar a todos" para todos los contactos válidos
     - O seleccionar un grupo específico

4. Guardar

### Paso 4: Enviar Campaña

1. En la lista de campañas, buscar tu campaña
2. Clic en "Enviar"
3. El sistema enviará automáticamente con delay de 3 segundos entre mensajes

**¿Cuánto demora?**
- 100 mensajes = ~5 minutos
- 1,000 mensajes = ~50 minutos
- 9,000 mensajes = ~7.5 horas

### Paso 5: Ver Métricas

1. Ir al "Dashboard"
2. Verás:
   - Total de contactos
   - Contactos válidos
   - Mensajes enviados
   - Tasa de entrega
   - Tasa de lectura
   - Estadísticas por grupo

## 🔥 Casos de Uso

### Enviar Comunicado Urgente

```
1. Ir a Campañas
2. Crear nueva campaña
3. Contenido: "URGENTE: Comunicado importante..."
4. Enviar a: Todos
5. Enviar
```

### Enviar a un Grupo Específico

```
1. Ir a Grupos → Crear "Supervisores"
2. Ir a Contactos → Subir CSV con grupo "Supervisores"
3. Ir a Campañas → Crear campaña
4. Seleccionar grupo "Supervisores"
5. Enviar
```

### Enviar Mensaje con Imagen

```
1. Preparar imagen (JPG, PNG, max 5MB)
2. Crear campaña
3. Subir imagen
4. Escribir mensaje (será el caption de la imagen)
5. Enviar
```

## 💰 Costos

### Green API
- **Plan gratuito**: 1,000 mensajes/día
- **Plan Business**: $50-80 USD/mes, mensajes ilimitados

Para 9,000 empleados × 4 mensajes/día = 36,000 mensajes/día
→ Necesitas el **Plan Business**

### Comparación con WATI

**WATI (WhatsApp Business API oficial):**
- Usa la API de Meta
- Cobra por mensaje: ~$0.004-0.01
- 36,000 mensajes/día = $150-350/mes SOLO mensajes
- Más plataforma: $40-100/mes
- **Total: $200-450/mes**

**Green API:**
- Usa WhatsApp Web (no oficial)
- Sin costo por mensaje
- Plan fijo: $50-80/mes
- **Total: $50-80/mes**

Green API es 3-5 veces más económico porque NO usa la API oficial de Meta.

## ⚠️ Límites y Precauciones

### Límites de Green API

- **Máximo mensajes por segundo**: ~1 mensaje cada 2-3 segundos
- **Mensajes por día**: Ilimitados (plan Business)
- **Validaciones por hora**: ~500-1000

### Precauciones

✅ **Hacer:**
- Usar solo para comunicados internos a empleados
- Respetar el delay de 3 segundos entre mensajes
- Validar números antes de enviar campañas grandes

❌ **NO hacer:**
- Enviar spam
- Usar para marketing masivo a clientes
- Enviar más de 4-5 campañas por día
- Enviar mensajes idénticos repetitivamente

### Riesgo de Ban

**Bajo riesgo** si:
- Solo envías a empleados (números conocidos)
- No superas 40,000 mensajes/día
- Mensajes son informativos (no spam)

**Alto riesgo** si:
- Envías spam
- Números te reportan
- Envías a números desconocidos masivamente

## 🛠️ Troubleshooting

### Los mensajes no se envían

**Problema**: Creé una campaña pero no se envía nada.

**Solución**:
1. Verificar que el queue worker esté corriendo:
```bash
php artisan queue:work --queue=whatsapp,validation
```

2. Ver si hay errores en los logs:
```bash
tail -f storage/logs/laravel.log
```

### Los números no se validan

**Problema**: Subí contactos pero aparecen como "no válidos".

**Solución**:
1. Verificar que el queue worker esté corriendo
2. La validación puede demorar (0.5 seg por número)
3. Para 9,000 números = ~1.25 horas

### La imagen no se carga

**Problema**: Subí una imagen pero no aparece.

**Solución**:
```bash
php artisan storage:link
```

### Error de conexión con Green API

**Problema**: "Failed to connect to Green API".

**Solución**:
1. Verificar Instance ID y Token en `.env`
2. Verificar que tu instancia de WhatsApp esté conectada en green-api.com
3. Verificar que escaneaste el QR code

## 📊 Formato del CSV

### Ejemplo Completo

```csv
phone,name
5491112345678,Juan Pérez
5491187654321,María González
5491198765432,Pedro Rodríguez
5493512345678,Ana Martínez
5493814567890,Carlos López
```

### Variaciones Aceptadas

**Opción 1:**
```csv
phone,name
5491112345678,Juan
```

**Opción 2:**
```csv
telefono,nombre
5491112345678,Juan
```

**Opción 3:**
```csv
numero,nombre
5491112345678,Juan
```

## 🚀 Próximos Pasos

Una vez que hayas enviado tu primera campaña:

1. Monitorear el Dashboard para ver estadísticas
2. Revisar qué números no son válidos
3. Crear grupos para segmentar mejor
4. Programar campañas regulares (4 veces al día según necesites)

## 📞 Soporte

Si tienes problemas, revisa:
1. Los logs: `storage/logs/laravel.log`
2. Que el queue worker esté corriendo
3. Que Green API esté conectado

---

**¡Listo!** Ya puedes empezar a enviar comunicados a tus 9,000 empleados.
