#!/bin/bash

echo "🚀 Iniciando Queue Worker de WhatsApp Masivo..."
echo ""
echo "⚠️  IMPORTANTE: Este proceso DEBE estar corriendo para enviar mensajes"
echo "   Para detener: presiona Ctrl+C"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

php artisan queue:work --queue=whatsapp,validation --tries=3 --timeout=300
