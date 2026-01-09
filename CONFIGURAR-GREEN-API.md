# Configurar Green API - Paso a Paso

## ⚠️ Estado Actual

He configurado tus credenciales:
- **Instance ID**: 7105
- **API Token**: cf31c4ac6115434d99bca04d413f1d3a4a7b6815123a4414b9
- **API URL**: https://7105.api.greenapi.com

**Problema detectado**: Error 403 Forbidden al intentar conectar.

## 🔧 Posibles Causas y Soluciones

### 1. Instancia No Autorizada

**Verifica en tu panel de Green API:**
1. Ve a https://console.green-api.com
2. Inicia sesión
3. Busca tu instancia **7105**
4. Verifica que esté en estado **"Authorized"** o **"Autorizada"**

**Si no está autorizada:**
- Haz clic en "Scan QR" o "Escanear QR"
- Escanea el código con tu WhatsApp Business
- Espera a que cambie a estado "Authorized"

### 2. Token Incorrecto

**Verifica el token:**
1. En tu panel de Green API
2. Ve a la instancia 7105
3. Copia el **API Token** completo
4. Compara con el que tienes configurado

**Si es diferente:**
```bash
nano .env

# Actualiza esta línea con el token correcto:
GREEN_API_TOKEN=tu_token_correcto_aqui
```

### 3. Instancia Suspendida o Expirada

**Verifica el estado de tu cuenta:**
- Revisa si tu plan está activo
- Verifica que no haya expirado
- Confirma que tengas créditos disponibles

### 4. IP Bloqueada

**Si estás usando un firewall o VPN:**
- Temporalmente desactiva VPN
- Verifica que tu IP no esté bloqueada
- Intenta desde otra red

## ✅ Cómo Verificar que Funciona

Una vez que hayas solucionado el problema, ejecuta:

```bash
php artisan tinker
```

Luego dentro de tinker:

```php
$service = new App\Services\GreenApiService();
$result = $service->getStateInstance();
print_r($result);
```

**Resultado esperado:**
```
Array
(
    [success] => 1
    [state] => authorized
    [data] => Array
        (
            [stateInstance] => authorized
        )
)
```

## 🔍 Debugging Manual

**Prueba con cURL desde tu terminal:**

```bash
curl -X GET "https://7105.api.green-api.com/waInstance7105/GetStateInstance/cf31c4ac6115434d99bca04d413f1d3a4a7b6815123a4414b9"
```

**Respuestas posibles:**

**✅ Si funciona:**
```json
{"stateInstance":"authorized"}
```

**❌ Si no funciona:**
```
401 Unauthorized - Token incorrecto
403 Forbidden - Instancia no autorizada o suspendida
404 Not Found - Instance ID incorrecto
```

## 📋 Checklist de Configuración

Marca cada paso:

- [ ] Cuenta de Green API creada
- [ ] Instancia 7105 creada
- [ ] WhatsApp escaneó el QR code
- [ ] Estado de instancia: "Authorized"
- [ ] Plan activo (no expirado)
- [ ] API Token copiado correctamente en `.env`
- [ ] Instance ID (7105) correcto en `.env`
- [ ] Servidor reiniciado después de cambiar `.env`

## 🔄 Después de Solucionar

1. **Reinicia el servidor:**
```bash
# Si estaba corriendo, detén con Ctrl+C y vuelve a iniciar:
php artisan serve
```

2. **Reinicia el queue worker:**
```bash
# Si estaba corriendo, detén con Ctrl+C y vuelve a iniciar:
php artisan queue:work --queue=whatsapp,validation
```

3. **Prueba enviando un mensaje de prueba:**
```bash
php artisan tinker
```

```php
$service = new App\Services\GreenApiService();
$result = $service->sendMessage('5491112345678', 'Mensaje de prueba');
print_r($result);
```

## 📞 Soporte de Green API

Si ninguna solución funciona:

1. **Email**: support@green-api.com
2. **Panel**: https://console.green-api.com → Chat de soporte
3. **Documentación**: https://green-api.com/en/docs/

## 🚨 Alternativas si Green API No Funciona

Si Green API continúa dando problemas, considera:

1. **Crear una nueva instancia** en Green API
2. **Usar otra cuenta** de Green API
3. **Contactar soporte** de Green API para activar tu cuenta
4. **Alternativa**: Usar otro servicio (aunque será más caro)

---

**Próximo Paso**: Una vez que Green API esté funcionando, podrás:
- Validar números
- Enviar mensajes de prueba
- Subir tus 9,000 contactos
- Enviar campañas masivas

¿Necesitas ayuda con algún paso específico?
