# 🌐 Configuración Final de Dominios y Sitemaps

## ✅ Dominios Correctos

Tienes **2 dominios** apuntando al mismo código:

| Dominio | Idioma | Target Audience | Google Analytics |
|---------|--------|-----------------|------------------|
| **restaurantesmexicanosfamosos.com** | 🇲🇽 Español | Hispanohablantes en USA | G-J6S51PLBZM |
| **famousmexicanrestaurants.com** | 🇺🇸 Inglés | Angloparlantes | G-3Y4S0P66Z6 |

---

## 📍 URLs de Sitemap

### **Producción**:
```
✅ https://restaurantesmexicanosfamosos.com/sitemap.xml
✅ https://famousmexicanrestaurants.com/sitemap.xml
```

### **Local (testing)**:
```
http://localhost:8002/sitemap.xml
```

---

## 🔧 Configuración Google Search Console

### **Paso 1: Dominio Español**
```
1. Ir a: https://search.google.com/search-console
2. Click "Agregar propiedad"
3. Ingresar: https://restaurantesmexicanosfamosos.com
4. Verificar dominio (HTML/DNS/GA)
5. Ir a "Sitemaps" (menú izquierdo)
6. Agregar: https://restaurantesmexicanosfamosos.com/sitemap.xml
7. Click "Enviar"
```

### **Paso 2: Dominio Inglés**
```
1. Click "Agregar propiedad"
2. Ingresar: https://famousmexicanrestaurants.com
3. Verificar dominio (HTML/DNS/GA)
4. Ir a "Sitemaps"
5. Agregar: https://famousmexicanrestaurants.com/sitemap.xml
6. Click "Enviar"
```

---

## 🧪 Testing Rápido

### **Test 1: Verificar Dominios**
```bash
# Check DNS - ambos deben apuntar a la misma IP
dig restaurantesmexicanosfamosos.com
dig famousmexicanrestaurants.com
```

### **Test 2: Sitemap Español**
```bash
curl https://restaurantesmexicanosfamosos.com/sitemap.xml | grep -o '<loc>https://[^<]*' | head -5
```

**✅ Debe mostrar**:
```
<loc>https://restaurantesmexicanosfamosos.com/
<loc>https://restaurantesmexicanosfamosos.com/restaurantes
<loc>https://restaurantesmexicanosfamosos.com/sugerir
<loc>https://restaurantesmexicanosfamosos.com/restaurante/...
```

### **Test 3: Sitemap Inglés**
```bash
curl https://famousmexicanrestaurants.com/sitemap.xml | grep -o '<loc>https://[^<]*' | head -5
```

**✅ Debe mostrar**:
```
<loc>https://famousmexicanrestaurants.com/
<loc>https://famousmexicanrestaurants.com/restaurantes
<loc>https://famousmexicanrestaurants.com/sugerir
<loc>https://famousmexicanrestaurants.com/restaurante/...
```

### **Test 4: Validar XML**
```
1. Ir a: https://www.xml-sitemaps.com/validate-xml-sitemap.html
2. Validar: https://restaurantesmexicanosfamosos.com/sitemap.xml
3. Validar: https://famousmexicanrestaurants.com/sitemap.xml
4. ✅ Ambos deben ser "Valid XML Sitemap"
```

---

## 📋 robots.txt

Ubicación: `/public/robots.txt`

```txt
# robots.txt for Famous Mexican Restaurants Directory

User-agent: *
Allow: /
Allow: /restaurantes
Allow: /restaurante/
Allow: /sugerir

# Disallow admin and private areas
Disallow: /admin
Disallow: /admin/
Disallow: /livewire/

# Sitemap (both domains)
Sitemap: https://restaurantesmexicanosfamosos.com/sitemap.xml
Sitemap: https://famousmexicanrestaurants.com/sitemap.xml

# Crawl-delay
Crawl-delay: 1

# Allow common search engines
User-agent: Googlebot
Allow: /

User-agent: Bingbot
Allow: /

User-agent: Slurp
Allow: /

User-agent: DuckDuckBot
Allow: /
```

**Acceso**:
```
https://restaurantesmexicanosfamosos.com/robots.txt
https://famousmexicanrestaurants.com/robots.txt
```

---

## 🎯 Cómo Funciona (Detección Automática)

El sitemap detecta automáticamente el dominio visitado:

```php
// app/Http/Controllers/SitemapController.php

$currentDomain = request()->getHost();

$baseUrl = match(true) {
    str_contains($currentDomain, 'famousmexicanrestaurants')
        => 'https://famousmexicanrestaurants.com',
    str_contains($currentDomain, 'restaurantesmexicanosfamosos')
        => 'https://restaurantesmexicanosfamosos.com',
    default => url('/'),
};
```

**Resultado**:
- Usuario visita `restaurantesmexicanosfamosos.com/sitemap.xml` → URLs con `restaurantesmexicanosfamosos.com`
- Usuario visita `famousmexicanrestaurants.com/sitemap.xml` → URLs con `famousmexicanrestaurants.com`

---

## 📊 Contenido del Sitemap (cada uno)

Cada sitemap incluye:

| Tipo | Cantidad | Prioridad | Frecuencia |
|------|----------|-----------|------------|
| Homepage | 1 | 1.0 | daily |
| Restaurantes index | 1 | 0.9 | daily |
| Sugerir form | 1 | 0.5 | monthly |
| **Cada restaurante** | ~N | 0.8 | weekly |
| **Estados** | ~50 | 0.7 | daily |
| **Categorías** | ~10 | 0.7 | daily |
| **Regiones mexicanas** | 6 | 0.6 | weekly |
| **Rangos de precio** | 3 | 0.6 | weekly |

**Total estimado**: ~500-600 URLs por sitemap

---

## ⏱️ Cache y Actualizaciones

### **Cache**:
- TTL: 1 hora (3600 segundos)
- Key: `sitemap_xml`

### **Auto-invalidación**:
El sitemap se actualiza automáticamente cuando:
- Se crea/edita/elimina un restaurante (RestaurantObserver)
- Cada 1 hora (cache expira)

### **Limpiar cache manualmente**:
```bash
php artisan cache:forget sitemap_xml
# o
php artisan cache:clear
```

---

## ✅ Checklist Pre-Producción

### **Antes de lanzar**:
- [x] SitemapController detecta ambos dominios ✅
- [x] robots.txt lista ambos sitemaps ✅
- [ ] Ambos dominios apuntan al mismo servidor
- [ ] DNS configurado correctamente
- [ ] SSL/HTTPS activo en ambos dominios
- [ ] Probar `/sitemap.xml` en local
- [ ] Validar XML en validator

### **En producción**:
- [ ] Agregar `restaurantesmexicanosfamosos.com` a GSC
- [ ] Verificar con HTML/DNS/GA
- [ ] Subir sitemap español
- [ ] Agregar `famousmexicanrestaurants.com` a GSC
- [ ] Verificar con HTML/DNS/GA
- [ ] Subir sitemap inglés
- [ ] Verificar ambos `robots.txt`
- [ ] Monitorear indexación (7-14 días)

---

## 📈 Resultados Esperados

### **Primeras 48 horas**:
- ✅ Sitemaps descubiertos por Google
- ✅ Primeras URLs escaneadas

### **7 días**:
- ✅ 50-100 URLs indexadas
- ✅ Coverage report disponible

### **30 días**:
- ✅ 300-400 URLs indexadas
- ✅ Primeras apariciones en SERPs
- ✅ Métricas de performance disponibles

### **90 días**:
- ✅ 450-500 URLs indexadas
- 📈 +50-70% tráfico orgánico
- 📈 +100-150% impresiones
- 📈 Posición promedio mejora 8-15 posiciones

---

## 🚨 Problemas Comunes

### **1. "Sitemap devuelve 404"**
```bash
# Verificar ruta registrada
php artisan route:list | grep sitemap

# Debe mostrar:
# GET  sitemap.xml  ... SitemapController@index
```

### **2. "URLs contienen localhost"**
```bash
# Verificar .env
APP_URL=https://restaurantesmexicanosfamosos.com

# O verificar detección de dominio en controller
```

### **3. "Google no puede acceder"**
```bash
# Verificar firewall
# Verificar que el servidor esté público
# Verificar SSL
curl -I https://restaurantesmexicanosfamosos.com/sitemap.xml
```

### **4. "Ambos sitemaps muestran mismo dominio"**
```bash
# El servidor podría tener un redirect
# Verificar configuración de Nginx/Apache
# Asegurar que ambos dominios sirvan el contenido correctamente
```

---

## 📚 Documentación Relacionada

- [FASE2_IMPLEMENTACION_COMPLETA.md](FASE2_IMPLEMENTACION_COMPLETA.md) - Implementación completa
- [SITEMAP_MULTI_DOMINIO_GUIA.md](SITEMAP_MULTI_DOMINIO_GUIA.md) - Guía detallada
- [FASE1_RESUMEN_COMPLETO.md](FASE1_RESUMEN_COMPLETO.md) - Features Fase 1

---

## 🎉 Resumen Final

```
✅ 2 dominios configurados
✅ 2 sitemaps independientes (mismo código)
✅ Detección automática de dominio
✅ robots.txt optimizado
✅ Cache inteligente (1 hora)
✅ Auto-invalidación en cambios
✅ ~500-600 URLs por sitemap
✅ Listo para Google Search Console
```

**Próximo paso**: Subir ambos sitemaps a Google Search Console y esperar 7 días para ver resultados iniciales.

---

**Última actualización**: 2025-11-03
**Versión**: 1.1 (dominios corregidos)
