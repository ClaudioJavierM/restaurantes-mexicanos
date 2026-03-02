# 🎁 Sistema de Cupones y Promociones

El sistema de suscripciones incluye un sistema completo de cupones y promociones para fomentar registros.

## 📋 Tipos de Promociones Disponibles

### 1. Descuento Porcentual
Descuento basado en porcentaje del precio (ej: 50% off)

**Ejemplos de uso:**
- Lanzamiento del servicio
- Promociones de temporada
- Black Friday / Cyber Monday
- Aniversario de la plataforma

### 2. Descuento Fijo
Descuento de cantidad fija en dólares (ej: $10 off)

**Ejemplos de uso:**
- Primer mes gratis
- Descuento de bienvenida
- Referidos

### 3. Duración del Descuento

#### `once` - Una sola vez
El descuento aplica solo al primer pago.

**Ejemplo:** "Primer mes 50% off"

#### `forever` - Para siempre
El descuento aplica a TODOS los pagos futuros.

**Ejemplo:** "Plan premium a mitad de precio de por vida"

#### `repeating` - Por X meses
El descuento aplica por un número específico de meses.

**Ejemplo:** "3 meses con 30% de descuento"

---

## 🚀 Crear Cupones con Artisan Command

### Sintaxis Base
```bash
php artisan stripe:coupon {CODE} [OPTIONS]
```

### Ejemplos Prácticos

#### 1. Lanzamiento - 50% Off Primer Mes
```bash
php artisan stripe:coupon LAUNCH50 --percent=50 --duration=once
```
- Código: `LAUNCH50`
- 50% de descuento
- Solo primer pago

#### 2. Early Bird - 30% Off por 3 Meses
```bash
php artisan stripe:coupon EARLYBIRD --percent=30 --duration=repeating --months=3
```
- Código: `EARLYBIRD`
- 30% de descuento
- Primeros 3 meses

#### 3. Primer Mes Gratis
```bash
php artisan stripe:coupon FREE1MONTH --percent=100 --duration=once
```
- Código: `FREE1MONTH`
- 100% de descuento (gratis)
- Solo primer mes

#### 4. Black Friday - $20 Off
```bash
php artisan stripe:coupon BLACKFRIDAY --amount=20 --duration=once --max=100 --expires=2025-11-30
```
- Código: `BLACKFRIDAY`
- $20 de descuento
- Solo primer pago
- Máximo 100 usos
- Expira el 30 de noviembre

#### 5. Lifetime Deal - 40% Off Forever
```bash
php artisan stripe:coupon LIFETIME40 --percent=40 --duration=forever --max=50
```
- Código: `LIFETIME40`
- 40% de descuento
- Para SIEMPRE
- Solo 50 personas (exclusivo)

#### 6. Referido - $10 Off
```bash
php artisan stripe:coupon REFERRAL10 --amount=10 --duration=once
```
- Código: `REFERRAL10`
- $10 de descuento
- Primer pago
- Sin límite de usos

---

## 💡 Estrategias de Promoción Recomendadas

### Para Lanzamiento (Primeros 30 días)
```bash
# 1. Super Early Bird - Solo 25 personas
php artisan stripe:coupon FOUNDING --percent=60 --duration=forever --max=25

# 2. Early Adopter - Primeros 100
php artisan stripe:coupon EARLY50 --percent=50 --duration=repeating --months=6 --max=100

# 3. Lanzamiento General
php artisan stripe:coupon LAUNCH2025 --percent=30 --duration=repeating --months=3
```

### Para Crecimiento Continuo
```bash
# 1. Primer mes gratis
php artisan stripe:coupon FIRSTFREE --percent=100 --duration=once

# 2. 3 meses con descuento
php artisan stripe:coupon SAVE3MONTHS --percent=25 --duration=repeating --months=3

# 3. Descuento de $15
php artisan stripe:coupon SAVE15 --amount=15 --duration=once
```

### Para Eventos Especiales
```bash
# Black Friday
php artisan stripe:coupon BLACKFRIDAY --percent=50 --duration=once --expires=2025-11-30

# Navidad
php artisan stripe:coupon NAVIDAD --percent=40 --duration=repeating --months=2 --expires=2025-12-31

# Año Nuevo
php artisan stripe:coupon NEWYEAR2026 --percent=35 --duration=repeating --months=3 --expires=2026-01-15
```

### Para Programas de Referidos
```bash
# Referidor gana $20 off
php artisan stripe:coupon REFERRER20 --amount=20 --duration=once

# Referido gana 25% off
php artisan stripe:coupon REFERRED25 --percent=25 --duration=once
```

---

## 🎯 Casos de Uso por Plan

### Plan Claimed ($9.99/mes)

**Promoción Recomendada: Primer mes gratis**
```bash
php artisan stripe:coupon FREECLAIMED --percent=100 --duration=once
```
Valor: $9.99 ahorrado

### Plan Premium ($39/mes)

**Promoción Recomendada: 3 meses a mitad de precio**
```bash
php artisan stripe:coupon HALFPRICE3 --percent=50 --duration=repeating --months=3
```
Valor: $58.50 ahorrado ($19.50 x 3 meses)

### Plan Elite ($99/mes)

**Promoción Recomendada: $100 off primeros 2 meses**
```bash
php artisan stripe:coupon ELITE100 --amount=50 --duration=repeating --months=2
```
Valor: $100 ahorrado

---

## 📊 Tracking de Cupones

### Verificar Cupón en Stripe Dashboard
1. Ve a: https://dashboard.stripe.com/coupons
2. Busca el código
3. Ve estadísticas:
   - Veces usado
   - Ingresos afectados
   - Clientes que lo usaron

### Eliminar/Desactivar Cupón
En Stripe Dashboard puedes:
- Desactivar el cupón (ya no se puede usar)
- Ver historial de uso
- Crear reportes

---

## 🔥 Promociones de Ejemplo para Lanzamiento

### Semana 1: Super Exclusivo
```bash
php artisan stripe:coupon FOUNDERS --percent=70 --duration=forever --max=10
```
"Los primeros 10 restaurantes obtienen 70% off DE POR VIDA"

### Semana 2-3: Early Bird
```bash
php artisan stripe:coupon EARLY100 --percent=50 --duration=repeating --months=6 --max=100
```
"Primeros 100 restaurantes: 50% off por 6 meses"

### Mes 1: Lanzamiento General
```bash
php artisan stripe:coupon LAUNCH --percent=40 --duration=repeating --months=3
```
"Mes de lanzamiento: 40% off primeros 3 meses"

### Después del mes 1: Estándar
```bash
php artisan stripe:coupon TRYIT --percent=100 --duration=once
```
"Prueba gratis el primer mes"

---

## ⚡ Opciones Avanzadas

### Todos los Parámetros Disponibles
```bash
php artisan stripe:coupon {CODE}
  --percent=50              # Porcentaje de descuento (0-100)
  --amount=10               # Cantidad fija en dólares
  --duration=once           # once, repeating, forever
  --months=3                # Meses (si duration=repeating)
  --max=100                 # Máximo número de usos
  --expires=2025-12-31      # Fecha de expiración (Y-m-d)
```

### Validación Automática
- El código se convierte automáticamente a MAYÚSCULAS
- No puedes usar --percent y --amount al mismo tiempo
- --months solo funciona con --duration=repeating
- Stripe valida que percent_off esté entre 1-100

---

## 📈 Mejores Prácticas

### ✅ DO (Hacer)
- Usar códigos cortos y memorables (`LAUNCH50`, `SAVE20`)
- Establecer límites de usos para ofertas exclusivas
- Poner fechas de expiración en promociones de temporada
- Trackear qué cupones convierten mejor
- Ofrecer descuentos más grandes en planes más caros

### ❌ DON'T (No hacer)
- No crear códigos demasiado largos (`SUPER-MEGA-ULTRA-DISCOUNT-2025`)
- No dejar cupones del 100% off sin límite de usos
- No usar `duration=forever` sin analizar el impacto financiero
- No duplicar códigos (Stripe te dará error)
- No olvidar comunicar la promoción a los usuarios

---

## 🎁 Ejemplos Listos Para Usar

Copia y pega estos comandos para crear promociones populares:

```bash
# Lanzamiento Básico
php artisan stripe:coupon LAUNCH2025 --percent=30 --duration=repeating --months=3

# Primer Mes Gratis
php artisan stripe:coupon FIRSTFREE --percent=100 --duration=once

# Black Friday
php artisan stripe:coupon BF2025 --percent=50 --duration=once --expires=2025-11-30

# Navidad
php artisan stripe:coupon XMAS25 --percent=40 --duration=repeating --months=2 --expires=2025-12-31

# Referidos
php artisan stripe:coupon FRIEND20 --amount=20 --duration=once

# VIP Lifetime
php artisan stripe:coupon VIP50 --percent=50 --duration=forever --max=20
```

---

## 🔗 Recursos Adicionales

- **Stripe Coupons Docs**: https://stripe.com/docs/api/coupons
- **Promotion Codes**: https://stripe.com/docs/api/promotion_codes
- **Best Practices**: https://stripe.com/docs/billing/subscriptions/coupons

---

**¿Necesitas ayuda?**
Ejecuta: `php artisan stripe:coupon --help`
