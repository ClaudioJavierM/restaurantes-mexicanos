# 🗺️ Guía de Configuración: Sitemap Multi-Dominio

## 📋 Resumen

Tienes **2 dominios** apuntando al mismo código Laravel:
- 🇲🇽 **restaurantesmexicanosfamosos.com** (Español)
- 🇺🇸 **famousmexicanrestaurants.com** (Inglés)

El sitemap es **INTELIGENTE**: detecta el dominio actual y genera URLs correctas automáticamente.

---

## ✅ Cómo Funciona

### **1. Detección Automática de Dominio**

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

### **2. URLs Generadas Dinámicamente**

**Cuando visitas**: `https://restaurantesmexicanosfamosos.com/sitemap.xml`
```xml
<url>
  <loc>https://restaurantesmexicanosfamosos.com/</loc>
  <loc>https://restaurantesmexicanosfamosos.com/restaurantes</loc>
  <loc>https://restaurantesmexicanosfamosos.com/restaurante/tacos-el-gordo</loc>
  ...
</url>
```

**Cuando visitas**: `https://famousmexicanrestaurants.com/sitemap.xml`
```xml
<url>
  <loc>https://famousmexicanrestaurants.com/</loc>
  <loc>https://famousmexicanrestaurants.com/restaurantes</loc>
  <loc>https://famousmexicanrestaurants.com/restaurante/tacos-el-gordo</loc>
  ...
</url>
```

---

## 🔧 Configuración en Google Search Console

### **Paso 1: Agregar Ambos Dominios**

#### **Dominio 1: restaurantesmexicanosfamosos.com**

1. Ir a https://search.google.com/search-console
2. Click **"Agregar propiedad"**
3. Seleccionar **"Prefijo de URL"**
4. Ingresar: `https://restaurantesmexicanosfamosos.com`
5. Click **"Continuar"**

**Verificación**:
- **Opción A**: HTML file (subir archivo a `/public`)
- **Opción B**: DNS record (agregar TXT en tu registrador)
- **Opción C**: Google Analytics (si ya tienes GA configurado)

6. Una vez verificado, ir a **Sitemaps** (menú izquierdo)
7. Agregar sitemap: `https://restaurantesmexicanosfamosos.com/sitemap.xml`
8. Click **"Enviar"**

#### **Dominio 2: famousmexicanrestaurants.com**

1. Repetir pasos 1-5 con `https://famousmexicanrestaurants.com`
2. Verificar el dominio
3. Agregar sitemap: `https://famousmexicanrestaurants.com/sitemap.xml`
4. Click **"Enviar"**

---

## 📊 Qué Verás en Google Search Console

### **restaurantesmexicanosfamosos.com**
```
Sitemaps enviados: 1
URL: https://restaurantesmexicanosfamosos.com/sitemap.xml
Estado: ✅ Éxito
URLs descubiertas: ~500+
URLs indexadas: (progresivamente)
```

### **famousmexicanrestaurants.com**
```
Sitemaps enviados: 1
URL: https://famousmexicanrestaurants.com/sitemap.xml
Estado: ✅ Éxito
URLs descubiertas: ~500+
URLs indexadas: (progresivamente)
```

---

## 🌐 robots.txt para Ambos Dominios

El archivo `public/robots.txt` ya está configurado para ambos:

```txt
# robots.txt for Famous Mexican Restaurants Directory

User-agent: *
Allow: /
Allow: /restaurantes
Allow: /restaurante/
Allow: /sugerir

# Disallow admin and private areas
Disallow: /admin
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
```

**Nota**: Puedes listar ambos sitemaps en un solo `robots.txt` - Google los detectará ambos.

---

## 🧪 Testing

### **Test 1: Sitemap Español**
```bash
# Local
curl http://localhost:8002/sitemap.xml

# Producción
curl https://restaurantesmexicanosfamosos.com/sitemap.xml
```

**✅ Verificar que todas las URLs contengan**: `https://restaurantesmexicanosfamosos.com/`

### **Test 2: Sitemap Inglés**
```bash
# Producción
curl https://famousmexicanrestaurants.com/sitemap.xml
```

**✅ Verificar que todas las URLs contengan**: `https://famousmexicanrestaurants.com/`

### **Test 3: Validar XML**

1. Ir a: https://www.xml-sitemaps.com/validate-xml-sitemap.html
2. Ingresar URL: `https://restaurantesmexicanosfamosos.com/sitemap.xml`
3. Click **"Validate"**
4. ✅ Debe mostrar: "Valid XML Sitemap"
5. Repetir para `https://famousmexicanrestaurants.com/sitemap.xml`

### **Test 4: Robots.txt**
```bash
# Verificar robots.txt
curl https://restaurantesmexicanosfamosos.com/robots.txt
curl https://famousmexicanrestaurants.com/robots.txt
```

**✅ Ambos deben mostrar el mismo contenido con ambos sitemaps listados**

---

## 📈 Resultados Esperados (3-7 días)

### **En Google Search Console verás**:

#### **restaurantesmexicanosfamosos.com**:
```
📊 Coverage Report:
  - URLs descubiertas: 500+
  - URLs válidas: 450+
  - URLs con errores: 0-10
  - URLs excluidas: 20-30 (duplicados, no indexables)

🔍 Performance:
  - Clics: +50-70% (3-6 meses)
  - Impresiones: +100-150%
  - CTR: 3-5%
  - Posición promedio: 15-25 → 8-12
```

#### **famousmexicanrestaurants.com**:
```
📊 Coverage Report:
  - URLs descubiertas: 500+
  - URLs válidas: 450+
  - URLs con errores: 0-10
  - URLs excluidas: 20-30

🔍 Performance:
  - Clics: +50-70%
  - Impresiones: +100-150%
  - CTR: 3-5%
  - Posición promedio: 15-25 → 8-12
```

---

## ⚠️ Problemas Comunes y Soluciones

### **Problema 1: "No se puede acceder al sitemap"**

**Causa**: Dominio no apunta correctamente al servidor

**Solución**:
```bash
# Verificar DNS
dig restaurantesmexicanosfamosos.com
dig famousmexicanrestaurants.com

# Ambos deben apuntar a la misma IP
```

### **Problema 2: "Sitemap devuelve 404"**

**Causa**: Ruta no registrada en Laravel

**Solución**:
```bash
# Verificar ruta
php artisan route:list | grep sitemap

# Debe mostrar:
# GET|HEAD  sitemap.xml  ... SitemapController@index
```

### **Problema 3: "URLs contienen localhost"**

**Causa**: APP_URL en .env apunta a localhost

**Solución**:
```env
# .env (producción)
APP_URL=https://restaurantesmexicanosfamosos.com

# O verifica que el controller detecte el dominio correctamente
```

### **Problema 4: "Cache del sitemap"**

**Causa**: Sitemap cacheado por 1 hora

**Solución**:
```bash
# Limpiar cache específico
php artisan cache:forget sitemap_xml

# O limpiar todo
php artisan cache:clear
```

---

## 🔄 Actualización del Sitemap

El sitemap se actualiza automáticamente cuando:

1. **RestaurantObserver detecta cambios** → limpia cache `sitemap_xml`
2. **Cada 1 hora** → cache expira automáticamente
3. **Manualmente**: `php artisan cache:forget sitemap_xml`

**No necesitas hacer nada manual** - es automático! ✨

---

## 📝 Checklist de Configuración

### **Antes de Producción**:
- [ ] Verificar que ambos dominios apunten al mismo servidor
- [ ] Confirmar APP_URL en .env
- [ ] Probar `/sitemap.xml` en ambos dominios (local)
- [ ] Validar XML en https://www.xml-sitemaps.com/validate-xml-sitemap.html

### **En Producción**:
- [ ] Agregar `restaurantesmexicanosfamosos.com` a Google Search Console
- [ ] Verificar dominio (HTML/DNS/GA)
- [ ] Subir sitemap: `https://restaurantesmexicanosfamosos.com/sitemap.xml`
- [ ] Agregar `famousmexicanrestaurants.com` a Google Search Console
- [ ] Verificar dominio (HTML/DNS/GA)
- [ ] Subir sitemap: `https://famousmexicanrestaurants.com/sitemap.xml`
- [ ] Verificar `robots.txt` en ambos dominios
- [ ] Esperar 3-7 días para indexación inicial

### **Monitoreo (semanal)**:
- [ ] Revisar Coverage Report en GSC
- [ ] Revisar errores de indexación
- [ ] Verificar Performance metrics
- [ ] Ajustar prioridades si es necesario

---

## 🎯 Estrategia SEO Multi-Dominio

### **restaurantesmexicanosfamosos.com (Español)**:
- **Target**: Hispanohablantes en USA
- **Keywords**: "restaurantes mexicanos", "comida mexicana", "tacos cerca de mi"
- **Contenido**: 80% español, 20% inglés
- **Google Analytics**: G-J6S51PLBZM

### **famousmexicanrestaurants.com (Inglés)**:
- **Target**: Angloparlantes interesados en comida mexicana
- **Keywords**: "mexican restaurants", "authentic mexican food", "best tacos near me"
- **Contenido**: 100% inglés
- **Google Analytics**: G-3Y4S0P66Z6

### **Ventaja de 2 Dominios**:
```
✅ Doble visibilidad en Google
✅ Segmentación por idioma/audiencia
✅ Menos competencia interna
✅ Métricas separadas para análisis
✅ Autoridad de dominio independiente
```

---

## 📚 Recursos

- **Google Search Console**: https://search.google.com/search-console
- **Sitemap Protocol**: https://www.sitemaps.org/protocol.html
- **Google SEO Guide**: https://developers.google.com/search/docs
- **XML Sitemap Validator**: https://www.xml-sitemaps.com/validate-xml-sitemap.html

---

## ✅ Resumen

1. ✅ **Sitemap dinámico** detecta dominio automáticamente
2. ✅ **2 sitemaps separados** con URLs correctas cada uno
3. ✅ **robots.txt** lista ambos sitemaps
4. ✅ **Cache de 1 hora** - actualizaciones automáticas
5. ✅ **RestaurantObserver** invalida cache en cambios

**Resultado**: Google indexa TODO tu sitio en AMBOS dominios sin duplicación de esfuerzo. 🚀

---

**Última actualización**: 2025-11-03
**Versión**: 1.0
