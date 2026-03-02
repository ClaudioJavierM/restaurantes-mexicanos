# 🌐 Estrategia de Dominios Multiidioma - Plan Completo

## Dominios Disponibles
- `famousmexicanrestaurant.com` (Inglés)
- `restaurantesmexicanosfamosos.com` (Español)

---

## 🎯 ESTRATEGIA RECOMENDADA (La Mejor para SEO y UX)

### **Opción 1: Dominios Separados por Idioma** ⭐ RECOMENDADO
```
famousmexicanrestaurant.com → Sitio completo en INGLÉS
restaurantesmexicanosfamosos.com → Sitio completo en ESPAÑOL

VENTAJAS:
✅ Mejor SEO (Google indexa cada dominio por separado)
✅ URLs más claras y naturales
✅ Marketing específico por idioma
✅ Contenido optimizado para cada audiencia
✅ Sin redirecciones, mejor performance
✅ Fácil de administrar con Laravel

DESVENTAJAS:
⚠️ Duplicar contenido (solucionable con hreflang tags)
⚠️ 2 dominios a mantener (pero mismo código)
```

**Implementación:**
```env
# .env
APP_URL=https://famousmexicanrestaurant.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# O para español:
APP_URL=https://restaurantesmexicanosfamosos.com
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
```

**En el header de ambos sitios:**
```html
<!-- Link entre versiones -->
<link rel="alternate" hreflang="en" href="https://famousmexicanrestaurant.com" />
<link rel="alternate" hreflang="es" href="https://restaurantesmexicanosfamosos.com" />
<link rel="alternate" hreflang="x-default" href="https://famousmexicanrestaurant.com" />

<!-- Banner de cambio de idioma -->
<div class="language-switch">
    🇺🇸 También disponible en: <a href="https://restaurantesmexicanosfamosos.com">Español</a>
</div>
```

---

### **Opción 2: Un Dominio Principal + Redirección del Otro**
```
famousmexicanrestaurant.com → PRINCIPAL (con /es y /en)
restaurantesmexicanosfamosos.com → REDIRIGE a famousmexicanrestaurant.com/es

URLs:
- famousmexicanrestaurant.com/en/restaurants
- famousmexicanrestaurant.com/es/restaurantes
- restaurantesmexicanosfamosos.com → redirige a /es

VENTAJAS:
✅ Un solo sitio a mantener
✅ Toggle idioma fácil
✅ Sesión/cookies compartidos

DESVENTAJAS:
❌ SEO no tan fuerte como dominios separados
❌ URLs más largas (/en/ o /es/)
❌ Confusión con 2 dominios apuntando al mismo sitio
```

---

### **Opción 3: Detección Automática + Dominios Separados** 🚀 IDEAL
```
CONFIGURACIÓN:
1. Ambos dominios apuntan al MISMO servidor Laravel
2. Laravel detecta el dominio y establece el idioma automáticamente
3. Cada dominio muestra contenido en su idioma nativo
4. Toggle para cambiar idioma cambia de dominio

EJEMPLO:
Usuario visita: famousmexicanrestaurant.com
→ Laravel detecta dominio EN
→ Muestra sitio en inglés
→ Toggle "Español" → redirige a restaurantesmexicanosfamosos.com

Usuario visita: restaurantesmexicanosfamosos.com
→ Laravel detecta dominio ES
→ Muestra sitio en español
→ Toggle "English" → redirige a famousmexicanrestaurant.com
```

**Implementación en Laravel:**

```php
// app/Http/Middleware/SetLocaleFromDomain.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromDomain
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();

        if (str_contains($domain, 'famousmexicanrestaurant')) {
            app()->setLocale('en');
            config(['app.locale' => 'en']);
        } elseif (str_contains($domain, 'restaurantesmexicanosfamosos')) {
            app()->setLocale('es');
            config(['app.locale' => 'es']);
        }

        return $next($request);
    }
}
```

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\SetLocaleFromDomain::class,
        // ... otros middleware
    ],
];
```

```php
// Helper para URLs multiidioma
// app/Helpers/UrlHelper.php
<?php

namespace App\Helpers;

class UrlHelper
{
    public static function switchLanguageUrl($locale)
    {
        $currentUrl = request()->path();

        if ($locale === 'en') {
            return 'https://famousmexicanrestaurant.com/' . $currentUrl;
        }

        return 'https://restaurantesmexicanosfamosos.com/' . $currentUrl;
    }

    public static function getCurrentDomainByLocale()
    {
        return app()->getLocale() === 'en'
            ? 'famousmexicanrestaurant.com'
            : 'restaurantesmexicanosfamosos.com';
    }
}
```

---

## 🔧 CONFIGURACIÓN DE SERVIDOR

### DNS Setup (Ambos dominios)
```
A Record:
famousmexicanrestaurant.com → IP_DEL_SERVIDOR
restaurantesmexicanosfamosos.com → IP_DEL_SERVIDOR

CNAME:
www.famousmexicanrestaurant.com → famousmexicanrestaurant.com
www.restaurantesmexicanosfamosos.com → restaurantesmexicanosfamosos.com
```

### Nginx Configuration
```nginx
server {
    listen 80;
    listen 443 ssl http2;

    server_name famousmexicanrestaurant.com www.famousmexicanrestaurant.com
                restaurantesmexicanosfamosos.com www.restaurantesmexicanosfamosos.com;

    root /var/www/restaurantesmexicanos/public;
    index index.php;

    # SSL certificates (Let's Encrypt para ambos dominios)
    ssl_certificate /etc/letsencrypt/live/famousmexicanrestaurant.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/famousmexicanrestaurant.com/privkey.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📝 ARCHIVOS DE TRADUCCIÓN

### Estructura de carpetas
```
resources/
├── lang/
    ├── en/
    │   ├── app.php
    │   ├── navigation.php
    │   ├── restaurants.php
    │   └── reviews.php
    └── es/
        ├── app.php
        ├── navigation.php
        ├── restaurants.php
        └── reviews.php
```

### Ejemplo de archivos

**resources/lang/en/app.php**
```php
<?php
return [
    'site_name' => 'Famous Mexican Restaurant',
    'tagline' => 'Discover the Best Mexican Restaurants in the USA',
    'search' => 'Search',
    'search_placeholder' => 'Tacos, Mariscos, City...',
    'all_states' => 'All States',
    'categories' => 'Categories',
    'featured' => 'Featured',
    'reviews' => 'Reviews',
    'write_review' => 'Write a Review',
    'suggest_restaurant' => 'Suggest Restaurant',
];
```

**resources/lang/es/app.php**
```php
<?php
return [
    'site_name' => 'Restaurantes Mexicanos Famosos',
    'tagline' => 'Descubre los Mejores Restaurantes Mexicanos en USA',
    'search' => 'Buscar',
    'search_placeholder' => 'Tacos, Mariscos, Ciudad...',
    'all_states' => 'Todos los Estados',
    'categories' => 'Categorías',
    'featured' => 'Destacados',
    'reviews' => 'Reseñas',
    'write_review' => 'Escribir Reseña',
    'suggest_restaurant' => 'Sugerir Restaurante',
];
```

---

## 🎨 COMPONENTE DE CAMBIO DE IDIOMA

```blade
<!-- resources/views/components/language-switcher.blade.php -->
<div class="flex items-center space-x-2">
    @if(app()->getLocale() === 'en')
        <a href="{{ \App\Helpers\UrlHelper::switchLanguageUrl('es') }}"
           class="flex items-center space-x-1 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all">
            <span class="text-lg">🇲🇽</span>
            <span class="text-sm font-medium">Español</span>
        </a>
    @else
        <a href="{{ \App\Helpers\UrlHelper::switchLanguageUrl('en') }}"
           class="flex items-center space-x-1 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all">
            <span class="text-lg">🇺🇸</span>
            <span class="text-sm font-medium">English</span>
        </a>
    @endif
</div>
```

**En navigation:**
```blade
<div class="flex items-center space-x-4">
    <a href="/">{{ __('app.home') }}</a>
    <a href="/restaurantes">{{ __('app.restaurants') }}</a>
    <a href="/sugerir">{{ __('app.suggest_restaurant') }}</a>

    <!-- Language Switcher -->
    <x-language-switcher />
</div>
```

---

## 🔍 SEO OPTIMIZATION

### Meta tags en layout
```blade
<!-- resources/views/layouts/app.blade.php -->
<head>
    <!-- Título dinámico por idioma -->
    <title>{{ $title ?? __('app.site_name') }} - {{ __('app.tagline') }}</title>

    <!-- Hreflang tags -->
    <link rel="alternate" hreflang="en"
          href="https://famousmexicanrestaurant.com{{ request()->path() }}" />
    <link rel="alternate" hreflang="es"
          href="https://restaurantesmexicanosfamosos.com{{ request()->path() }}" />
    <link rel="alternate" hreflang="x-default"
          href="https://famousmexicanrestaurant.com{{ request()->path() }}" />

    <!-- Open Graph por idioma -->
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'es_US' }}" />
    <meta property="og:site_name" content="{{ __('app.site_name') }}" />
</head>
```

---

## 📊 ANALYTICS POR IDIOMA

### Google Analytics 4 Configuration
```javascript
// Tracking separado por dominio/idioma
gtag('config', 'G-XXXXXXXXXX', {
    'custom_map': {
        'dimension1': 'language',
        'dimension2': 'domain'
    }
});

gtag('event', 'page_view', {
    'language': '{{ app()->getLocale() }}',
    'domain': '{{ request()->getHost() }}'
});
```

---

## ✅ MI RECOMENDACIÓN FINAL

**Usar OPCIÓN 3**: Dominios separados con detección automática

### Por qué:
1. **SEO**: Google indexa ambos dominios por separado → más visibilidad
2. **UX**: URLs limpias sin /en/ o /es/
3. **Branding**: Cada mercado tiene su dominio natural
4. **Marketing**: Puedes hacer campañas específicas por idioma
5. **Performance**: Sin redirecciones innecesarias
6. **Mantenimiento**: Un solo código, configuración simple

### Configuración en 5 pasos:
```bash
1. Apuntar ambos dominios al mismo servidor
2. Crear middleware SetLocaleFromDomain
3. Crear archivos de traducción (en/ y es/)
4. Agregar hreflang tags al layout
5. Implementar language switcher en navbar
```

---

## 🚀 IMPLEMENTACIÓN

¿Quieres que implemente esto ahora? Incluye:
- ✅ Middleware de detección de dominio
- ✅ Archivos de traducción base
- ✅ Language switcher component
- ✅ Helper functions
- ✅ SEO meta tags
- ✅ Actualizar todas las vistas principales

**Tiempo estimado:** 30-45 minutos

¿Empezamos? 🎯
