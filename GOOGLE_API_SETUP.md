# 🗺️ Configuración de Google Places API

## ✅ Lo que hemos implementado:

### 1. **Nuevos campos en la base de datos:**
- ✅ `google_place_id` - ID único de Google Places
- ✅ `google_maps_url` - Link directo a Google Maps
- ✅ `business_status` - Estado del negocio:
  - `operational` - Operando normalmente
  - `temporarily_closed` - Cerrado temporalmente
  - `permanently_closed` - Cerrado permanentemente
  - `coming_soon` - Próxima apertura
- ✅ `opening_date` - Fecha de apertura (para "coming soon")
- ✅ `google_verified` - Si fue verificado con Google
- ✅ `last_google_verification` - Última verificación
- ✅ `google_rating` - Rating de Google (1-5)
- ✅ `google_reviews_count` - Número de reviews en Google

### 2. **Servicio de Google Places API**
Creado en: `app/Services/GooglePlacesService.php`

**Funcionalidades disponibles:**
- ✅ Buscar restaurante por nombre y dirección
- ✅ Obtener detalles completos del lugar
- ✅ Verificar si está abierto ahora
- ✅ Obtener estado del negocio
- ✅ Geocodificar direcciones (obtener lat/lng)
- ✅ Descargar fotos del lugar
- ✅ Sincronizar restaurante con datos de Google

---

## 📝 Cómo configurar Google Places API:

### Paso 1: Crear proyecto en Google Cloud

1. Ve a: https://console.cloud.google.com/
2. Crea un nuevo proyecto o selecciona uno existente
3. Nombre sugerido: "restaurantesmexicanos-com"

### Paso 2: Habilitar APIs necesarias

En la consola de Google Cloud, habilita estas APIs:
- ✅ **Places API** (New)
- ✅ **Geocoding API**
- ✅ **Maps JavaScript API** (para mapas en el sitio)

**Link directo:** https://console.cloud.google.com/apis/library

### Paso 3: Crear API Key

1. Ve a: https://console.cloud.google.com/apis/credentials
2. Click en "CREATE CREDENTIALS" → "API Key"
3. **IMPORTANTE:** Haz click en "RESTRICT KEY"
4. Configura restricciones:
   - **Application restrictions:** HTTP referrers
   - **Website restrictions:** Agrega:
     - `http://localhost:8002/*`
     - `http://127.0.0.1:8002/*`
     - `https://restaurantesmexicanos.com/*` (cuando tengas dominio)
   - **API restrictions:** Restringe a:
     - Places API (New)
     - Geocoding API
     - Maps JavaScript API

### Paso 4: Agregar API Key al proyecto

Edita el archivo `.env` y agrega:

```env
GOOGLE_PLACES_API_KEY=tu_api_key_aqui
GOOGLE_MAPS_API_KEY=tu_api_key_aqui
```

---

## 💰 Costos de Google Places API:

### Plan Gratuito (Incluye $200 USD de crédito mensual):

**Places API:**
- Find Place: $17 USD por 1,000 requests
- Place Details: $17 USD por 1,000 requests
- **Gratis:** Hasta ~11,700 búsquedas mensuales

**Geocoding API:**
- $5 USD por 1,000 requests
- **Gratis:** Hasta ~40,000 geocodificaciones mensuales

**Maps JavaScript API:**
- $7 USD por 1,000 cargas de mapa
- **Gratis:** Hasta ~28,000 cargas mensuales

### Recomendación:
Para empezar, el plan gratuito es MÁS que suficiente. Solo pagarías si excedes $200 USD/mes de uso.

---

## 🚀 Cómo usar las funcionalidades:

### 1. Sincronizar un restaurante con Google Places

Desde el código:
```php
use App\Services\GooglePlacesService;
use App\Models\Restaurant;

$googleService = new GooglePlacesService();
$restaurant = Restaurant::find(1);

// Sincronizar con Google
$success = $googleService->syncRestaurantWithGoogle($restaurant);

if ($success) {
    echo "✅ Restaurante sincronizado con Google!";
    echo "Rating: " . $restaurant->google_rating;
    echo "Estado: " . $restaurant->business_status;
}
```

### 2. Comando artisan para sincronizar todos los restaurantes

Puedes crear un comando:
```bash
php artisan restaurants:sync-google
```

### 3. Verificar automáticamente si está abierto/cerrado

```php
$isOpen = $googleService->isOpenNow($restaurant->google_place_id);

if ($isOpen) {
    echo "🟢 ABIERTO AHORA";
} else {
    echo "🔴 CERRADO AHORA";
}
```

### 4. Obtener fotos del restaurante

```php
$photos = $googleService->getPlacePhotos($restaurant->google_place_id, 5);

foreach ($photos as $photoUrl) {
    echo "<img src='{$photoUrl}' alt='Foto del restaurante'>";
}
```

---

## 🎯 Próximos pasos sugeridos:

1. **Configurar la API Key** (sigue los pasos arriba)

2. **Agregar botón en Filament Admin** para sincronizar restaurantes

3. **Crear tarea programada** para verificar estados diariamente:
   ```php
   // En app/Console/Kernel.php
   $schedule->command('restaurants:verify-status')->daily();
   ```

4. **Mostrar en el frontend:**
   - Badge "Abierto ahora" / "Cerrado"
   - Rating de Google
   - Link a Google Maps
   - Fotos desde Google

5. **Validar direcciones al crear restaurantes** en Filament

---

## 📊 Ejemplo de uso completo:

```php
// Buscar y sincronizar
$place = $googleService->findPlace(
    "Pedro's Tacos and Tequila",
    "123 Main St",
    "Gulfport",
    "Mississippi"
);

if ($place) {
    $details = $googleService->getPlaceDetails($place['place_id']);

    // Datos disponibles:
    echo $details['name'];              // Nombre verificado
    echo $details['formatted_address']; // Dirección completa
    echo $details['formatted_phone_number']; // Teléfono real
    echo $details['website'];           // Sitio web real
    echo $details['rating'];            // Rating de Google
    echo $details['user_ratings_total']; // Total de reviews
    echo $details['business_status'];   // OPERATIONAL, CLOSED_TEMPORARILY, etc.
    echo $details['opening_hours']['open_now']; // true/false
}
```

---

## ⚠️ Importante:

- **NO subas tu API Key a GitHub** (ya está en .gitignore)
- Usa restricciones de API para evitar uso no autorizado
- Monitorea tu uso en: https://console.cloud.google.com/apis/dashboard
- Configura alertas de billing si te preocupa el costo

---

## 🆘 Soporte:

Si necesitas ayuda con la configuración:
1. Documentación oficial: https://developers.google.com/maps/documentation/places/web-service
2. Precios: https://cloud.google.com/maps-platform/pricing
3. Consola: https://console.cloud.google.com/

---

✨ **¡Listo!** Una vez configurada la API Key, todo funcionará automáticamente.
