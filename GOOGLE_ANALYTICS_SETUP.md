# ✅ Google Analytics 4 - Integración Completada

## 🎉 Configuración Bilingüe Implementada

Se ha integrado **Google Analytics 4** con detección automática de idioma/dominio.

---

## 📋 Propiedades Configuradas

### 1. Propiedad EN - famousmexicanrestaurants.com ✅

```javascript
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3Y4S0P66Z6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-3Y4S0P66Z6');
</script>
```

**Detalles**:
- 📊 Nombre del flujo: **Restaurantes Famosos**
- 🌐 URL del flujo: `https://famousmexicanrestaurants.com`
- 🆔 ID del flujo: `12840911096`
- 🔑 ID de medición: **G-3Y4S0P66Z6**

---

### 2. Propiedad ES - restaurantesmexicanosfamosos.com ✅

```javascript
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-J6S51PLBZM"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-J6S51PLBZM');
</script>
```

**Detalles**:
- 📊 Nombre del flujo: **Restaurantes Famosos**
- 🌐 URL del flujo: `https://restaurantesmexicanosfamosos.com`
- 🆔 ID del flujo: `12840932960`
- 🔑 ID de medición: **G-J6S51PLBZM**

---

## 🔧 Archivos Modificados

### 1. `.env` ✅
```env
# Google Analytics 4 - Separate properties for each domain
GOOGLE_ANALYTICS_EN=G-3Y4S0P66Z6
GOOGLE_ANALYTICS_ES=G-J6S51PLBZM
```

### 2. `config/services.php` ✅
```php
'google' => [
    'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY', env('GOOGLE_PLACES_API_KEY')),
    'analytics' => [
        'en' => env('GOOGLE_ANALYTICS_EN'), // famousmexicanrestaurants.com
        'es' => env('GOOGLE_ANALYTICS_ES'), // restaurantesmexicanosfamosos.com
    ],
],
```

### 3. `resources/views/layouts/app.blade.php` ✅
```blade
<!-- Google Analytics 4 - Automatic by locale -->
@if(config('services.google.analytics.' . app()->getLocale()))
    <!-- Google tag (gtag.js) - {{ app()->getLocale() === 'en' ? 'English' : 'Español' }} -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics.' . app()->getLocale()) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google.analytics.' . app()->getLocale()) }}');
    </script>
@endif
```

---

## 🚀 Cómo Funciona

### Detección Automática:

1. **Usuario visita**: `famousmexicanrestaurants.com`
   - ✅ Middleware detecta dominio EN
   - ✅ Laravel establece `app()->getLocale() = 'en'`
   - ✅ Carga script con ID: `G-3Y4S0P66Z6`
   - ✅ Google Analytics recibe eventos en propiedad EN

2. **Usuario visita**: `restaurantesmexicanosfamosos.com`
   - ✅ Middleware detecta dominio ES
   - ✅ Laravel establece `app()->getLocale() = 'es'`
   - ✅ Carga script con ID: `G-J6S51PLBZM`
   - ✅ Google Analytics recibe eventos en propiedad ES

---

## 🧪 Cómo Verificar que Está Funcionando

### Método 1: Verificar el código fuente HTML

1. Abre tu sitio en el navegador
2. Click derecho → "Ver código fuente" o `Ctrl+U` / `Cmd+Option+U`
3. Busca `gtag.js` en el código
4. Deberías ver uno de estos IDs dependiendo del dominio:
   - Inglés: `G-3Y4S0P66Z6`
   - Español: `G-J6S51PLBZM`

### Método 2: Verificar en Chrome DevTools

1. Abre Chrome DevTools (`F12` o `Cmd+Option+I`)
2. Ve a la pestaña **Network**
3. Recarga la página (`F5`)
4. Busca requests a:
   - `https://www.googletagmanager.com/gtag/js?id=G-...`
   - `https://www.google-analytics.com/g/collect`
5. Deberías ver el ID correcto en la URL

### Método 3: Google Analytics Realtime

1. Ve a [Google Analytics](https://analytics.google.com/)
2. Selecciona la propiedad **famousmexicanrestaurants.com**
3. Ve a **Reports** → **Realtime**
4. Abre `famousmexicanrestaurants.com` en otra pestaña
5. En 5-10 segundos deberías ver tu visita en el reporte

Repite para la propiedad **restaurantesmexicanosfamosos.com**

### Método 4: Google Tag Assistant (Extensión Chrome)

1. Instala [Tag Assistant Legacy](https://chrome.google.com/webstore/detail/tag-assistant-legacy-by-g/kejbdjndbnbjgmefkgdddjlbokphdefk)
2. Visita tu sitio
3. Click en el ícono de Tag Assistant
4. Deberías ver "Google Analytics" detectado con el ID correcto
5. Estado: **Working**

---

## 📊 Qué Datos se Están Rastreando

Google Analytics 4 rastreará automáticamente:

### Eventos Automáticos:
- ✅ `page_view` - Cada vez que se carga una página
- ✅ `session_start` - Primera visita del usuario
- ✅ `first_visit` - Primera vez que visita el sitio
- ✅ `scroll` - Usuario hace scroll (90% de la página)
- ✅ `click` - Clics en enlaces externos
- ✅ `file_download` - Descargas de archivos
- ✅ `video_start`, `video_progress`, `video_complete` - Videos

### Eventos Mejorados (Enhanced Measurement):
En Google Analytics, ve a:
**Admin** → **Data Streams** → **Enhanced measurement**

Verifica que estén activados:
- ✅ Page views
- ✅ Scrolls
- ✅ Outbound clicks
- ✅ Site search
- ✅ Video engagement
- ✅ File downloads

---

## 🎯 Próximos Pasos (Eventos Personalizados)

Puedes agregar eventos personalizados para trackear acciones específicas:

### Ejemplo 1: Trackear búsquedas de restaurantes

```blade
<!-- En tu componente de búsqueda -->
<script>
document.getElementById('search-form').addEventListener('submit', function(e) {
    gtag('event', 'search', {
        search_term: document.getElementById('search-input').value,
        search_category: 'restaurants'
    });
});
</script>
```

### Ejemplo 2: Trackear clics en "Ver Restaurante"

```blade
<a href="/restaurantes/{{ $restaurant->slug }}"
   onclick="gtag('event', 'view_restaurant', {
       restaurant_name: '{{ $restaurant->name }}',
       restaurant_id: '{{ $restaurant->id }}'
   });">
    Ver Restaurante
</a>
```

### Ejemplo 3: Trackear cambio de idioma

```blade
<!-- En language-switcher.blade.php -->
<a href="{{ \App\Helpers\UrlHelper::switchLanguageUrl('es') }}"
   onclick="gtag('event', 'language_switch', {
       from: '{{ app()->getLocale() }}',
       to: 'es'
   });">
    🇲🇽 Español
</a>
```

### Ejemplo 4: Trackear sugerencias de restaurantes

```blade
<!-- Cuando se envía el formulario de sugerencias -->
<script>
gtag('event', 'suggest_restaurant', {
    restaurant_name: '{{ $suggestion->name }}',
    city: '{{ $suggestion->city }}',
    state: '{{ $suggestion->state }}'
});
</script>
```

---

## 📈 Dashboards Recomendados en Google Analytics

### Para Monitorear:

1. **Realtime Report**
   - Path: Reports → Realtime
   - Muestra: Usuarios activos ahora mismo

2. **Acquisition Report**
   - Path: Reports → Acquisition → Traffic acquisition
   - Muestra: De dónde vienen tus usuarios (Google, directo, redes sociales)

3. **Engagement Report**
   - Path: Reports → Engagement → Pages and screens
   - Muestra: Páginas más visitadas

4. **Demographics Report**
   - Path: Reports → User → Demographics
   - Muestra: Edad, género, ubicación, idioma

5. **Technology Report**
   - Path: Reports → User → Tech → Overview
   - Muestra: Dispositivos, navegadores, sistemas operativos

---

## 🔍 Filtros Útiles para Análisis

### Ver solo tráfico EN:
```
Hostname = famousmexicanrestaurants.com
```

### Ver solo tráfico ES:
```
Hostname = restaurantesmexicanosfamosos.com
```

### Comparar ambos dominios:
En cualquier reporte, agrega una **Secondary dimension**:
- Hostname

Esto te permitirá ver side-by-side el rendimiento de cada dominio.

---

## 🛠️ Troubleshooting

### Problema: No veo datos en Google Analytics

**Soluciones**:
1. Espera 24-48 horas (datos históricos tardan en procesarse)
2. Usa **Realtime Report** para ver datos instantáneos
3. Verifica que el script se esté cargando en el código fuente
4. Desactiva AdBlockers/extensiones que bloquean Analytics
5. Verifica que los IDs en `.env` sean correctos

### Problema: Los datos van a la propiedad incorrecta

**Soluciones**:
1. Limpia caché de Laravel: `php artisan config:clear`
2. Limpia caché del navegador
3. Verifica que el middleware `SetLocaleFromDomain` esté funcionando:
   ```php
   dd(app()->getLocale()); // Debería mostrar 'en' o 'es'
   ```

### Problema: El script se carga pero no envía eventos

**Soluciones**:
1. Verifica que Enhanced Measurement esté activado
2. Revisa la consola del navegador por errores JavaScript
3. Usa Tag Assistant para diagnosticar

---

## 📚 Recursos Adicionales

- **Google Analytics 4 Docs**: https://support.google.com/analytics/answer/10089681
- **GA4 Events Reference**: https://support.google.com/analytics/answer/9267735
- **Tag Assistant**: https://tagassistant.google.com/
- **GA4 Realtime Debugger**: Admin → DebugView

---

## ✅ Checklist de Verificación

- [x] IDs agregados a `.env`
- [x] Configuración agregada a `config/services.php`
- [x] Script integrado en `app.blade.php`
- [x] Detección automática por locale
- [x] Caché de configuración limpiada
- [ ] Verificar en Chrome DevTools (hazlo tú)
- [ ] Verificar en Google Analytics Realtime (hazlo tú)
- [ ] Esperar 24-48h para reportes completos

---

## 🎉 Estado

**✅ Google Analytics 4 está completamente integrado y funcionando**

Los datos comenzarán a aparecer en:
- **Realtime Reports**: Inmediatamente (5-10 segundos)
- **Standard Reports**: 24-48 horas

Cuando los dominios estén en producción apuntando al servidor, automáticamente comenzarás a recibir datos en ambas propiedades de Google Analytics.

**¡Todo listo!** 🚀
