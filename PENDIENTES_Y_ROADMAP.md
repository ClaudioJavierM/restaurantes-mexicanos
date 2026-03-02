# 📋 Pendientes y Roadmap - Restaurantes Mexicanos Famosos

## ✅ **COMPLETADO** (Fases 1 y 2)

### **✨ Fase 1 - Filtros Avanzados + UX/UI**:
- ✅ Filtros Avanzados Mexicanos (🌶️ picante, 🇲🇽 región, 💰 precio, etc.)
- ✅ Badges visuales en tarjetas de restaurantes
- ✅ Laravel Notifications (ReviewApproved, SuggestionApproved)
- ✅ Laravel Queue (ProcessRestaurantImage job)
- ✅ Laravel Cache (Home, RestaurantList con TTL optimizados)
- ✅ Google Analytics 4 (bilingual tracking)
- ✅ Google Maps Embed
- ✅ Open Graph + Schema.org
- ✅ Sistema bilingüe completo (EN/ES)
- ✅ Social Sharing

### **✨ Fase 2 - SEO + Menú Interactivo**:
- ✅ Google Places API Autocomplete en formulario de sugerencias
- ✅ RestaurantObserver (auto cache invalidation)
- ✅ Sitemap.xml dinámico multi-dominio
- ✅ robots.txt optimizado
- ✅ Modelo MenuItem completo con fotos
- ✅ Filament Resource para gestión de menú (admin)
- ✅ Migración menu_items table
- ✅ Relaciones Restaurant ↔ MenuItem

---

## ⏳ **PENDIENTE** - Priorizado por Impacto

### **🔥 PRIORIDAD ALTA** (2-3 semanas)

#### **1. Vista Frontend del Menú Interactivo** ⭐⭐⭐⭐⭐
**Tiempo estimado**: 6-8 horas
**Impacto**: CRÍTICO (+60-80% engagement)

**Por qué es importante**:
- Es tu diferenciador #1 vs Yelp
- El backend YA está listo (Filament + MenuItem)
- Solo falta mostrar el menú en la página del restaurante
- **Yelp NO tiene esto** - tendrás ventaja competitiva

**Funcionalidades**:
```
- URL: /restaurante/{slug}#menu
- Tabs por categoría (Tacos, Burritos, Desserts, etc.)
- Grid de platillos con fotos
- Modal de detalles al hacer click
- Filtros: Por categoría, dieta, nivel de picante
- Platillos populares destacados
- Compartir platillo en redes sociales
- Responsive (mobile-first)
```

**Archivos a crear**:
```
resources/views/livewire/
├── restaurant-detail.blade.php (modificar)
└── partials/
    ├── restaurant-menu.blade.php (nuevo)
    └── menu-item-modal.blade.php (nuevo)
```

**Mockup visual**:
```
┌─────────────────────────────────────┐
│ [Información] [Reseñas] [📋 Menú]  │  ← Tabs
├─────────────────────────────────────┤
│ 🔍 Buscar platillos...              │
│ [Todos] [Tacos] [Burritos] [🌶️3+] │  ← Filtros rápidos
├─────────────────────────────────────┤
│ ⭐ PLATILLOS POPULARES              │
│ ┌──────┐ ┌──────┐ ┌──────┐         │
│ │ 📸   │ │ 📸   │ │ 📸   │         │
│ │Tacos │ │Burr. │ │Ench. │         │
│ │$12.99│ │$14.99│ │$13.99│         │
│ │🌶️🌶️🌶️│ │🌶️🌶️  │ │🌶️    │         │
│ └──────┘ └──────┘ └──────┘         │
├─────────────────────────────────────┤
│ 🌮 TACOS                            │
│ Grid de tacos con fotos...          │
├─────────────────────────────────────┤
│ 🌯 BURRITOS                         │
│ Grid de burritos...                 │
└─────────────────────────────────────┘
```

---

#### **2. Sistema de Claim para Dueños** ⭐⭐⭐⭐⭐
**Tiempo estimado**: 8-10 horas
**Impacto**: CRÍTICO (contenido actualizado por dueños)

**Por qué es importante**:
- Dueños mantienen su información actualizada (sin ti)
- Más confianza = más tráfico
- Competitive advantage vs directorios estáticos
- Los dueños se convierten en tus "contribuidores"

**Funcionalidades**:
```
USER FLOW:
1. Usuario visita /restaurante/tacos-el-gordo
2. Ve botón "¿Eres el dueño? Reclama este negocio"
3. Click → Formulario de verificación
4. Email de confirmación enviado
5. Admin revisa y aprueba en Filament
6. Dueño recibe acceso a Dashboard exclusivo

DASHBOARD DEL DUEÑO:
- Ver estadísticas (visitas, clicks)
- Editar información básica
- Gestionar menú (agregar/editar platillos)
- Subir fotos
- Responder a reseñas
- Crear eventos (happy hours, etc.)
- Ver reseñas pendientes
```

**Base de datos**:
```sql
-- Migration: create_business_claims_table
- id
- restaurant_id (FK)
- user_id (FK) nullable
- claimer_name
- claimer_email
- claimer_phone
- verification_method (email, phone, document)
- verification_code
- documents (JSON: license, ID, etc.)
- status (pending, approved, rejected)
- verified_at
- admin_notes
- created_at
- updated_at
```

**Roles en Filament**:
```php
- Super Admin (tú)
- Restaurant Owner (dueño verificado)
  - Can: Edit own restaurant
  - Can: Manage menu items
  - Can: Upload photos
  - Can: Respond to reviews
  - Cannot: Edit other restaurants
  - Cannot: Access admin panel fully
```

---

#### **3. Autenticación de Usuarios (Laravel Breeze)** ⭐⭐⭐⭐
**Tiempo estimado**: 4-5 horas
**Impacto**: ALTO (habilita features sociales)

**Por qué es importante**:
- Requisito para check-ins, fotos, favoritos
- Engagement de usuarios registrados es 3-5x mayor
- Datos de usuarios = insights valiosos
- Personalización y recomendaciones

**Implementación**:
```bash
# Instalar Breeze
composer require laravel/breeze --dev
php artisan breeze:install livewire
npm install && npm run build
php artisan migrate

# Personalizar views
resources/views/auth/
├── login.blade.php (con diseño mexicano)
├── register.blade.php
├── forgot-password.blade.php
└── verify-email.blade.php
```

**Funcionalidades**:
```
- Registro con email + password
- Login social (Google, Facebook opcional)
- Verificación de email
- Reset de contraseña
- Profile management
- Preferencias de idioma (ES/EN)
```

---

### **🔥 PRIORIDAD MEDIA** (3-5 semanas)

#### **4. Sistema de Check-ins Sociales** ⭐⭐⭐⭐
**Tiempo estimado**: 10-12 horas
**Impacto**: ALTO (gamificación + community)

**Funcionalidades**:
```
- Check-in con geolocalización (verificación real)
- Feed de actividad social
- "Juan hizo check-in en Tacos El Gordo hace 2h"
- Badges de frecuencia:
  - 🥉 Bronze: 5 check-ins
  - 🥈 Silver: 15 check-ins
  - 🥇 Gold: 50 check-ins
  - 👑 VIP: 100 check-ins
- "Top Fans" por restaurante
- Leaderboard de usuarios más activos
- Notificaciones: "3 personas hicieron check-in aquí hoy"
```

**Base de datos**:
```sql
-- Migration: create_check_ins_table
- id
- user_id (FK)
- restaurant_id (FK)
- latitude
- longitude
- distance_from_restaurant (meters)
- comment (opcional)
- is_verified (geolocation check)
- created_at
```

---

#### **5. Sistema de Fotos por Usuarios** ⭐⭐⭐⭐
**Tiempo estimado**: 6-8 horas
**Impacto**: MEDIO-ALTO (contenido generado por usuarios)

**Funcionalidades**:
```
- Upload de fotos por usuarios autenticados
- Max 5 fotos por día por usuario
- Moderación en Filament antes de publicar
- Gallery en página de restaurante
- Atribución: "Foto por @username hace 3 días"
- Likes en fotos
- Reportar foto inapropiada
- Filtros: "Fotos de platillos", "Fotos del lugar", "Fotos del ambiente"
```

**Base de datos**:
```sql
-- Migration: create_user_photos_table
- id
- user_id (FK)
- restaurant_id (FK)
- photo_path
- caption
- status (pending, approved, rejected)
- likes_count
- is_featured
- moderated_at
- moderator_id (FK users)
- created_at
```

---

#### **6. Calendario de Eventos** ⭐⭐⭐⭐
**Tiempo estimado**: 6-8 horas
**Impacto**: MEDIO-ALTO (tráfico recurrente)

**Funcionalidades**:
```
TIPOS DE EVENTOS:
- Happy Hours (día/hora específicos)
- Noches de Mariachi
- Eventos especiales (Dia de los Muertos, Cinco de Mayo)
- Live music
- Promociones (2x1, descuentos)

FEATURES:
- Calendario mensual visual
- Filtro: "Restaurantes con mariachi hoy"
- Filtro: "Happy hours esta semana"
- Notificaciones: "Tu restaurante favorito tiene happy hour en 1h"
- iCal export (agregar a Google Calendar)
- Recordatorios automáticos
```

**Base de datos**:
```sql
-- Migration: create_restaurant_events_table
- id
- restaurant_id (FK)
- title
- description
- event_type (happy_hour, mariachi, special, promo)
- start_date
- end_date
- start_time
- end_time
- recurrence (none, daily, weekly, monthly)
- is_active
- created_by (FK users - puede ser dueño)
- created_at
- updated_at
```

---

### **🔥 PRIORIDAD BAJA** (1-3 meses)

#### **7. Redis Setup para Producción** ⭐⭐⭐
**Tiempo estimado**: 2-3 horas
**Impacto**: MEDIO (performance en producción)

**Implementación**:
```bash
# Instalar Redis
brew install redis  # Mac
apt install redis   # Linux

# Configurar .env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache tags (mejor que file cache)
Cache::tags(['restaurants', 'menu'])->flush();
```

---

#### **8. Laravel Horizon** ⭐⭐⭐
**Tiempo estimado**: 1-2 horas
**Impacto**: BAJO-MEDIO (monitoring de queues)

**Implementación**:
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate

# Dashboard
http://localhost:8002/horizon
```

---

#### **9. Sistema de Favoritos** ⭐⭐⭐
**Tiempo estimado**: 4-5 horas
**Impacto**: MEDIO (engagement)

**Funcionalidades**:
```
- Botón "❤️ Guardar" en cada restaurante
- Lista de favoritos en perfil de usuario
- Notificaciones cuando favorito actualiza menú
- "3 de tus amigos guardaron este restaurante"
```

---

#### **10. Sistema de Reservaciones (Integración)** ⭐⭐
**Tiempo estimado**: 8-10 horas
**Impacto**: BAJO (depende de terceros)

**Opciones**:
- OpenTable API
- Resy API
- Sistema propio (complejo)

---

#### **11. Monetización** ⭐⭐⭐
**Tiempo estimado**: 10-12 horas
**Impacto**: $$$ (ingresos)

**Planes**:
```
FREE:
- Listing básico
- 1 foto
- Información básica

BASIC ($29/mes):
- Hasta 10 fotos
- Menú completo con fotos
- Badge "Verificado"
- Responder a reseñas

PRO ($79/mes):
- Todo de Basic +
- Featured en búsquedas
- Aparece en homepage
- Promocionar eventos
- Analytics avanzado
- Support prioritario

ENTERPRISE ($199/mes):
- Todo de Pro +
- Multiple locations
- API access
- White-label mobile app
- Dedicated account manager
```

**Stripe Integration**:
```bash
composer require laravel/cashier
php artisan cashier:install
```

---

## 📊 **Roadmap Recomendado**

### **Semana 1-2** (Impacto Inmediato):
```
✅ Vista Frontend del Menú (6-8h)
✅ Sistema de Claim (8-10h)
Total: 14-18 horas
```

### **Semana 3-4** (Features Sociales):
```
✅ Autenticación Breeze (4-5h)
✅ Sistema de Fotos (6-8h)
✅ Calendario de Eventos (6-8h)
Total: 16-21 horas
```

### **Semana 5-6** (Engagement):
```
✅ Check-ins Sociales (10-12h)
✅ Sistema de Favoritos (4-5h)
Total: 14-17 horas
```

### **Mes 2-3** (Optimización):
```
✅ Redis Setup (2-3h)
✅ Horizon (1-2h)
✅ Performance tuning (4-6h)
Total: 7-11 horas
```

### **Mes 3+** (Monetización):
```
✅ Planes de pago (10-12h)
✅ Stripe Integration (8-10h)
✅ Dashboard de métricas para dueños (6-8h)
Total: 24-30 horas
```

---

## 🎯 **Próximo Paso RECOMENDADO**

### **Vista Frontend del Menú** 🍽️

**Por qué AHORA**:
1. ✅ El backend está 100% listo (MenuItem + Filament)
2. ✅ Es tu mayor diferenciador vs Yelp
3. ✅ Rápido de implementar (6-8h)
4. ✅ Impacto visual inmediato
5. ✅ No requiere autenticación (puede ser público)

**ROI esperado**:
- 📈 +60-80% tiempo en sitio
- 📈 +45% conversión (sitio → restaurante físico)
- 📈 +35% engagement
- 📈 Mejora posicionamiento SEO (contenido rico)

---

## 📈 **Impacto Estimado por Feature**

| Feature | Esfuerzo | Impacto | ROI | Prioridad |
|---------|----------|---------|-----|-----------|
| **Menú Frontend** | 6-8h | ⭐⭐⭐⭐⭐ | 🔥🔥🔥🔥🔥 | 1 |
| **Sistema Claim** | 8-10h | ⭐⭐⭐⭐⭐ | 🔥🔥🔥🔥🔥 | 2 |
| **Auth Usuarios** | 4-5h | ⭐⭐⭐⭐ | 🔥🔥🔥🔥 | 3 |
| **Check-ins** | 10-12h | ⭐⭐⭐⭐ | 🔥🔥🔥🔥 | 4 |
| **Fotos Usuarios** | 6-8h | ⭐⭐⭐⭐ | 🔥🔥🔥 | 5 |
| **Calendario Eventos** | 6-8h | ⭐⭐⭐⭐ | 🔥🔥🔥🔥 | 6 |
| **Redis** | 2-3h | ⭐⭐⭐ | 🔥🔥 | 7 |
| **Favoritos** | 4-5h | ⭐⭐⭐ | 🔥🔥🔥 | 8 |
| **Horizon** | 1-2h | ⭐⭐ | 🔥 | 9 |
| **Monetización** | 24-30h | ⭐⭐⭐⭐⭐ | 💰💰💰💰💰 | 10 |

---

## 🚀 **Plan de 30 Días**

### **Día 1-7**: Vista del Menú
- Día 1-2: Diseño y layout
- Día 3-4: Implementación tabs y filtros
- Día 5-6: Modal de detalles
- Día 7: Testing y polish

### **Día 8-14**: Sistema de Claim
- Día 8-9: Formulario de claim
- Día 10-11: Verificación y admin
- Día 12-13: Dashboard de dueño
- Día 14: Testing

### **Día 15-21**: Features Sociales Básicas
- Día 15-17: Autenticación Breeze
- Día 18-21: Sistema de fotos básico

### **Día 22-30**: Eventos + Polish
- Día 22-26: Calendario de eventos
- Día 27-30: Bug fixes, testing, optimización

---

## ✅ **¿Qué sigue?**

**Mi recomendación**: Empezar con **Vista Frontend del Menú**

**Razones**:
1. Mayor impacto visual inmediato
2. Diferenciador #1 vs Yelp
3. Backend ya listo (solo frontend)
4. No requiere dependencias (auth, etc.)
5. ROI comprobado en otros directorios

**¿Quieres que empiece con esto?** 🚀

---

**Última actualización**: 2025-11-03
**Total pendientes**: 11 features principales
**Esfuerzo total estimado**: ~100-120 horas
**Timeline recomendado**: 2-3 meses para MVP completo
