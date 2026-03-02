# Google Places API - Sistema de Control de Costos

## 🎯 Objetivo
Mantener el uso de Google Places API **SIEMPRE dentro de los $200 USD mensuales gratuitos** para evitar cargos inesperados.

## 💰 Límites Configurados

- **Presupuesto Mensual**: $180 USD (buffer del 10% para seguridad)
- **Umbral de Alerta**: $150 USD (75% del presupuesto)
- **Límite Diario de Requests**: 200 requests/día (~6,000/mes)

## 📊 Costos por API

| API | Costo por 1,000 requests |
|-----|--------------------------|
| Text Search (buscar restaurantes) | $32.00 |
| Place Details (detalles del lugar) | $17.00 |
| Street View (descargar fotos) | $7.00 |

## 🔍 Verificar Uso Actual

```bash
php artisan api:check-usage
```

Este comando te mostrará:
- ✅ Requests y costo de HOY
- ✅ Costo total del MES ACTUAL
- ✅ Presupuesto restante
- ✅ Porcentaje usado con barra de progreso
- ✅ Alertas si te acercas al límite

## ⚙️ Cómo Funciona el Sistema

### 1. Tracking Automático
Cada llamada a la API de Google queda registrada en la base de datos (`api_usage` table) con:
- Servicio usado (text_search, place_details, street_view)
- Número de requests
- Costo estimado
- Fecha y metadatos

### 2. Límites Estrictos
Antes de hacer scraping, el sistema verifica:
- ¿Se ha excedido el límite diario de requests?
- ¿Se ha excedido el presupuesto mensual?

Si cualquiera de los dos está excedido, **EL SCRAPING SE DETIENE AUTOMÁTICAMENTE**.

### 3. Alertas
- **75% del presupuesto ($150)**: WARNING en los logs
- **100% del presupuesto ($180)**: ERROR CRÍTICO en los logs

## 🚀 Uso Seguro del Scraping

### ✅ RECOMENDADO: Scraping Controlado

```bash
# Scrapear MÁXIMO 10 restaurantes por día (costo ~$0.50/día = $15/mes)
php artisan scrape:restaurants --city="Los Angeles" --state=CA --limit=10
```

### ⚠️ CUIDADO: Scraping Masivo

```bash
# Esto puede costar $20-50 USD en un solo comando
php artisan scrape:restaurants --state=CA --limit=500
```

### ❌ PELIGROSO: Scripts Automáticos

**NO EJECUTAR** los scripts `scrape_cities.sh` o `scrape_more_cities.sh` sin antes verificar el presupuesto.

## 📈 Estrategia Recomendada para Crecimiento

### Opción 1: Crecimiento Lento y Gratis (RECOMENDADA)
- Scrapear **10 restaurantes/día** = 300/mes
- Costo mensual: **$15-20 USD** ✅ GRATIS (dentro de $200)
- En 6 meses: +1,800 restaurantes adicionales

### Opción 2: Crecimiento Orgánico
- Dejar que los **dueños registren sus restaurantes**
- Incentivos para usuarios que agreguen restaurantes
- **Costo: $0** permanente ✅

### Opción 3: Crecimiento Rápido con Control
- Scrapear **50 restaurantes/día** solo los primeros 10 días del mes
- Costo: ~$80-100/mes ✅ GRATIS (dentro de $200)
- Resto del mes: solo actualizaciones

## 🔧 Configuración de Límites

Puedes ajustar los límites en el archivo `.env`:

```env
GOOGLE_DAILY_REQUEST_LIMIT=200        # Requests por día
GOOGLE_MONTHLY_BUDGET_LIMIT=180       # Presupuesto mensual en USD
GOOGLE_ALERT_THRESHOLD=150            # Alerta al llegar a este monto
```

## 📋 Comandos Disponibles

```bash
# Ver uso actual de la API
php artisan api:check-usage

# Scrapear con límites seguros
php artisan scrape:restaurants --city="Houston" --state=TX --limit=10

# Ver restaurantes en DB
php artisan tinker --execute="echo \App\Models\Restaurant::count();"
```

## 🚨 Qué Hacer Si Excedes el Límite

Si ves que te acercas a los $180 USD:

1. **DETÉN todo scraping** inmediatamente
2. Espera hasta el 1° del mes siguiente (los límites se resetean)
3. Revisa el uso con `php artisan api:check-usage`
4. Ajusta tu estrategia de scraping

## 💡 Tips para Ahorrar Costos

1. ✅ Usar `--dry-run` para probar sin hacer llamadas reales
2. ✅ Scrapear solo ciudades específicas (no estados enteros)
3. ✅ Limitar a 10-20 restaurantes por ejecución
4. ✅ Ejecutar solo 1 vez al día
5. ✅ Verificar uso diario con `api:check-usage`

## 📞 Contacto

Si tienes dudas sobre el sistema de tracking o necesitas ajustar límites, contacta al desarrollador.

---

**IMPORTANTE**: Este sistema está diseñado para PREVENIR cargos inesperados. Siempre verifica el uso antes de hacer scraping masivo.
