# 🚀 Inicio Rápido - WhatsApp Masivo

## ⚡ 3 Pasos para Empezar

### 1️⃣ Configurar Green API

1. Ve a https://green-api.com
2. Crea una cuenta gratis
3. Crea una instancia de WhatsApp
4. Escanea el código QR con tu WhatsApp Business
5. Copia tu **Instance ID** y **API Token**

### 2️⃣ Editar el archivo .env

```bash
nano .env
```

Busca estas líneas al final y reemplaza con tus datos:

```env
GREEN_API_INSTANCE_ID=1234567890  # Pega tu Instance ID aquí
GREEN_API_TOKEN=abc123xyz         # Pega tu Token aquí
```

Guarda el archivo (Ctrl+O, Enter, Ctrl+X en nano)

### 3️⃣ Iniciar la aplicación

Abre **2 terminales**:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2 (MUY IMPORTANTE):**
```bash
php artisan queue:work --queue=whatsapp,validation
```

⚠️ **AMBAS terminales DEBEN estar corriendo** para que funcione todo.

---

## 🎯 Primer Uso

1. Abre tu navegador en: http://localhost:8000

2. **Crear un Grupo** (opcional):
   - Clic en "Grupos" → Crear "Empleados"

3. **Subir Contactos**:
   - Clic en "Contactos"
   - Prepara un CSV:
   ```csv
   phone,name
   5491112345678,Juan Pérez
   5491187654321,María González
   ```
   - Sube el archivo
   - Marca "Validar números" si quieres verificar cuáles son válidos

4. **Crear Campaña**:
   - Clic en "Campañas"
   - Rellena el formulario
   - Crea la campaña

5. **Enviar**:
   - En la lista, clic en "Enviar"
   - ¡Listo! Los mensajes se enviarán automáticamente

---

## 💰 Costos

**Green API Plan Business**: $50-80 USD/mes
- Mensajes ilimitados
- Para 36,000 mensajes/día es suficiente

**Comparado con:**
- WATI/WhatsApp Business API oficial: $200-450/mes
- **Ahorras 3-5x con Green API**

---

## ❓ Problemas Comunes

### "Los mensajes no se envían"
✅ **Solución**: Verifica que el queue worker esté corriendo
```bash
php artisan queue:work --queue=whatsapp,validation
```

### "Error de conexión con Green API"
✅ **Solución**:
1. Verifica que copiaste bien el Instance ID y Token en `.env`
2. Verifica que tu WhatsApp esté conectado en green-api.com

### "Los números no se validan"
✅ **Solución**: La validación demora. Para 9,000 números = ~1.25 horas

---

## 📱 Contacto

Si necesitas ayuda, revisa:
- `README.md` - Documentación completa
- `INSTRUCCIONES.md` - Guía detallada
- `storage/logs/laravel.log` - Ver errores

---

**¡Listo!** Ya puedes enviar comunicados a tus 9,000 empleados 🎉
