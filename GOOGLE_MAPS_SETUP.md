# 🗺️ Google Maps Integration - Setup Guide

## ✅ Integración Completada

Se ha integrado **Google Maps Embed API** en el sitio para mostrar la ubicación de cada restaurante de manera interactiva.

---

## 🎯 Qué Se Implementó

### 1. **Componente Reutilizable** ✅
**Archivo**: `resources/views/components/google-map.blade.php`

**Características**:
- 🗺️ Mapa interactivo embebido
- 📍 Marcador automático en la ubicación del restaurante
- 🎨 Diseño moderno con bordes y sombras
- 🔘 Botón "Abrir en Google Maps" con efecto hover
- 🌐 Traducciones en inglés y español
- ⚡ Lazy loading para mejor rendimiento
- 💪 Fallback cuando no hay API key configurada

**Uso**:
```blade
<x-google-map
    name="Taquería El Poblano"
    address="123 Main St, Los Angeles, CA 90001"
    height="450"
    zoom="15"
/>
```

### 2. **Integración en Vista de Detalle** ✅
**Archivo**: `resources/views/livewire/restaurant-detail.blade.php` (líneas 127-136)

El mapa se muestra automáticamente en cada página de restaurante con:
- Nombre del restaurante
- Dirección completa
- Ciudad, estado y código postal
- Zoom apropiado (15)

### 3. **Traducciones** ✅
**Archivos**:
- `lang/es/app.php` (líneas 99-104)
- `lang/en/app.php` (líneas 99-104)

Textos traducidos:
- "Abrir en Google Maps" / "Open in Google Maps"
- "Ver en Google Maps" / "View in Google Maps"
- "Mapa no disponible" / "Map unavailable"
- "Ubicación" / "Location"
- "Direcciones" / "Directions"

---

## 🔑 Cómo Obtener tu Google Maps API Key

### Paso 1: Ir a Google Cloud Console
```
https://console.cloud.google.com/
```

### Paso 2: Crear o Seleccionar un Proyecto
1. Click en el dropdown del proyecto (arriba)
2. Click en "Nuevo Proyecto"
3. Nombre: "Restaurantes Mexicanos"
4. Click en "Crear"

### Paso 3: Habilitar Google Maps Embed API
1. Ve a **APIs & Services** → **Library**
2. Busca: "Maps Embed API"
3. Click en "Maps Embed API"
4. Click en **"ENABLE"** (HABILITAR)

**IMPORTANTE**: ⚠️ Maps Embed API es **COMPLETAMENTE GRATIS** - Sin límites, sin cargos.

### Paso 4: Crear API Key
1. Ve a **APIs & Services** → **Credentials**
2. Click en **"+ CREATE CREDENTIALS"** → **"API key"**
3. Se generará tu API key
4. Copia el valor (algo como: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`)

### Paso 5: Restringir la API Key (IMPORTANTE para seguridad)

#### Restricción de Aplicación:
1. Click en tu API key para editarla
2. En "Application restrictions", selecciona **"HTTP referrers (web sites)"**
3. Click en **"ADD AN ITEM"**
4. Agrega estos dominios:

```
famousmexicanrestaurant.com/*
www.famousmexicanrestaurant.com/*
restaurantesmexicanosfamosos.com/*
www.restaurantesmexicanosfamosos.com/*
localhost:8002/*
127.0.0.1:8002/*
```

#### Restricción de API:
1. En "API restrictions", selecciona **"Restrict key"**
2. Selecciona **"Maps Embed API"**
3. Click en **"SAVE"**

---

## 🛠️ Configuración en tu Proyecto

### 1. Agregar API Key al .env ✅ (Ya está configurado)

Edita tu archivo `.env` y agrega:

```env
GOOGLE_MAPS_API_KEY=TU_API_KEY_AQUI
```

Reemplaza `TU_API_KEY_AQUI` con la API key que copiaste de Google Cloud Console.

**Ejemplo**:
```env
GOOGLE_MAPS_API_KEY=AIzaSyABCDEF123456789GHIJKLMN0123456789
```

### 2. Limpiar caché de configuración

Después de agregar la API key:

```bash
php artisan config:clear
php artisan config:cache
```

### 3. Verificar que funciona

Visita cualquier restaurante en tu sitio, ejemplo:
```
http://localhost:8002/restaurantes/el-poblano
```

Deberías ver:
- ✅ Un mapa interactivo con la ubicación del restaurante
- ✅ Un botón "Abrir en Google Maps" en la esquina inferior derecha
- ✅ Capacidad de hacer zoom in/out
- ✅ Capacidad de arrastrar el mapa

---

## 💰 Costos

### Maps Embed API: **GRATIS** ✅
- ✅ **$0** por mes
- ✅ **Ilimitadas** embeds
- ✅ **Sin límites** de requests
- ✅ No requiere tarjeta de crédito
- ✅ No requiere billing account

### Otras APIs de Google Maps (opcional, no implementadas aún):

| API | Costo | Uso |
|-----|-------|-----|
| **Maps JavaScript API** | $7/1000 cargas | Mapas más avanzados |
| **Places API** | $17/1000 requests | Buscar restaurantes, autocomplete |
| **Geocoding API** | $5/1000 requests | Convertir direcciones a coordenadas |
| **Directions API** | $5/1000 requests | Rutas y direcciones |

**Nota**: Todas incluyen $200 de crédito gratis por mes.

---

## 🎨 Personalización del Componente

### Cambiar altura del mapa:
```blade
<x-google-map
    name="{{ $restaurant->name }}"
    address="{{ $restaurant->address }}"
    height="600"  <!-- Altura en píxeles -->
/>
```

### Cambiar nivel de zoom:
```blade
<x-google-map
    name="{{ $restaurant->name }}"
    address="{{ $restaurant->address }}"
    zoom="18"  <!-- 1-20, donde 20 es máximo acercamiento -->
/>
```

### Cambiar modo:
```blade
<!-- Modo Place (por defecto) -->
<x-google-map mode="place" ... />

<!-- Modo Search -->
<x-google-map mode="search" ... />
```

### Agregar clases CSS personalizadas:
```blade
<x-google-map
    name="{{ $restaurant->name }}"
    address="{{ $restaurant->address }}"
    class="my-custom-class shadow-2xl border-4 border-red-500"
/>
```

---

## 🔍 Troubleshooting

### Problema: El mapa no aparece

**Posibles causas**:

1. **API Key no configurada**
   - Verifica que `GOOGLE_MAPS_API_KEY` esté en `.env`
   - Run: `php artisan config:clear`

2. **API no habilitada en Google Cloud**
   - Ve a Google Cloud Console
   - Verifica que "Maps Embed API" esté habilitada

3. **Restricciones incorrectas**
   - Verifica que tu dominio esté en la lista de referrers autorizados
   - Para desarrollo local, agrega `localhost:8002/*`

4. **API Key inválida**
   - Verifica que copiaste la key completa
   - No debe tener espacios al inicio o final

### Problema: Aparece "Map unavailable"

Esto significa que la API key no está configurada. Es el fallback intencional que muestra:
- Un ícono de ubicación
- Un enlace directo a Google Maps

**Solución**: Agrega tu API key al archivo `.env`

### Problema: El mapa muestra "This page can't load Google Maps correctly"

**Causa**: Restricciones de la API key no coinciden con tu dominio

**Solución**:
1. Ve a Google Cloud Console → Credentials
2. Edita tu API key
3. En HTTP referrers, asegúrate de tener:
   ```
   localhost:8002/*
   ```

### Problema: El mapa no centra correctamente el restaurante

**Causa**: La dirección no es reconocida por Google Maps

**Solución**:
1. Verifica que la dirección esté completa
2. Asegúrate de incluir ciudad, estado y código postal
3. Considera usar coordenadas GPS (latitud/longitud) si la dirección es inexacta

---

## 🚀 Mejoras Futuras (Opcionales)

### 1. Usar Maps JavaScript API (más avanzado)
**Ventajas**:
- Marcadores personalizados
- Ventanas de información (popups)
- Múltiples marcadores
- Rutas y direcciones

**Costo**: $7/1000 cargas (incluye $200 gratis/mes = ~28,500 cargas gratis)

### 2. Agregar Directions API
**Función**: Botón "Cómo llegar desde mi ubicación"

**Costo**: $5/1000 requests

### 3. Agregar Places API
**Función**:
- Autocomplete de direcciones
- Información adicional (horarios, fotos, reviews de Google)

**Costo**: $17/1000 requests

---

## 📊 Ejemplos de Uso

### En Restaurant Detail (actual):
```blade
<x-google-map
    :name="$restaurant->name"
    :address="$restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state->code"
    height="400"
    zoom="15"
/>
```

### En Restaurant List (miniatura):
```blade
<x-google-map
    :name="$restaurant->name"
    :address="$restaurant->address"
    height="200"
    zoom="14"
    class="rounded-lg"
/>
```

### En Homepage (destacados con mapa):
```blade
@foreach($featuredRestaurants as $restaurant)
    <div class="card">
        <h3>{{ $restaurant->name }}</h3>
        <x-google-map
            :name="$restaurant->name"
            :address="$restaurant->address"
            height="250"
        />
    </div>
@endforeach
```

---

## 📱 Responsive Design

El componente es **completamente responsive**:
- ✅ Se adapta al ancho del contenedor
- ✅ Altura fija configurable
- ✅ Touch gestures habilitados en móviles
- ✅ Zoom con pellizco en pantallas táctiles

---

## 🌐 Características Bilingües

El componente respeta automáticamente el idioma del sitio:

**Español** (`restaurantesmexicanosfamosos.com`):
- "Abrir en Google Maps"
- "Ubicación"
- "Mapa no disponible"

**Inglés** (`famousmexicanrestaurant.com`):
- "Open in Google Maps"
- "Location"
- "Map unavailable"

---

## ✅ Checklist de Implementación

- [x] Componente `google-map.blade.php` creado
- [x] Traducciones agregadas (es/en)
- [x] Integrado en `restaurant-detail.blade.php`
- [x] Variable `GOOGLE_MAPS_API_KEY` en `.env`
- [x] Configuración en `config/services.php`
- [x] Fallback cuando no hay API key
- [x] Botón "Abrir en Google Maps" funcional
- [x] Diseño responsive
- [ ] **Obtener API key de Google Cloud** ← Tu siguiente paso
- [ ] **Agregar API key al .env**
- [ ] **Probar en navegador**

---

## 🎉 Resultado Final

Cuando configures tu API key, los usuarios verán:

1. **Mapa interactivo** con la ubicación exacta del restaurante
2. **Marcador** en la ubicación
3. **Controles** de zoom (+/-)
4. **Street View** (arrastrar el muñeco naranja)
5. **Botón flotante** para abrir en Google Maps app
6. **Navegación completa** con gestos touch en móvil

Todo esto **100% gratis** y sin límites de uso.

---

## 📚 Recursos Adicionales

- **Google Maps Embed API Docs**: https://developers.google.com/maps/documentation/embed/get-started
- **API Key Best Practices**: https://developers.google.com/maps/api-security-best-practices
- **Google Cloud Console**: https://console.cloud.google.com/
- **Maps Embed Pricing**: https://mapsplatform.google.com/pricing/ (Embed es gratis)

---

**¡La integración de Google Maps está lista!** 🗺️

Solo necesitas agregar tu API key al archivo `.env` y los mapas funcionarán automáticamente en todas las páginas de restaurantes.
