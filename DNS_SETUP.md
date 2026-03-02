# Configuración de DNS - Redirigir Dominios al VPS

## ✅ Lo que YA está hecho:

1. ✅ Aplicación subida al VPS
2. ✅ Base de datos SQLite configurada con datos (50 estados, 15 restaurantes, 36 items)
3. ✅ Nginx configurado y corriendo
4. ✅ Laravel optimizado
5. ✅ VPS respondiendo en: **72.167.150.82**

---

## 🎯 SIGUIENTE PASO: Actualizar DNS

Tus dominios actualmente están en **hosting compartido**. Necesitas cambiar los registros DNS para que apunten al VPS.

### Dominios a configurar:
- `restaurantesmexicanosfamosos.com` (versión en español)
- `famousmexicanrestaurants.com` (versión en inglés)

---

## Opción 1: Si los dominios están en GoDaddy

### Paso 1: Iniciar sesión en GoDaddy

1. Ve a https://www.godaddy.com
2. Inicia sesión con tu cuenta
3. Ve a **"My Products"** o **"Mis Productos"**
4. Encuentra tus dominios en la lista

### Paso 2: Actualizar DNS para restaurantesmexicanosfamosos.com

1. Haz clic en el botón **DNS** o **Manage** junto al dominio
2. Busca la sección **DNS Records** o **Registros DNS**
3. Encuentra los registros tipo **A**
4. Edita o agrega estos registros:

```
Tipo: A
Nombre: @
Valor: 72.167.150.82
TTL: 600 (o 3600)

Tipo: A
Nombre: www
Valor: 72.167.150.82
TTL: 600 (o 3600)
```

5. **Guarda los cambios**

### Paso 3: Repetir para famousmexicanrestaurants.com

Repite el Paso 2 para el segundo dominio.

---

## Opción 2: Si los dominios están en otro registrador

### Registradores comunes:

**Namecheap:**
1. Login → Domain List → Manage
2. Advanced DNS → Add New Record
3. Agrega los registros A mencionados arriba

**Google Domains:**
1. Login → My Domains → DNS
2. Custom resource records
3. Agrega los registros A

**Cloudflare:**
1. Login → Select Domain → DNS
2. Add Record
3. Type: A, Name: @, IPv4: 72.167.150.82
4. Type: A, Name: www, IPv4: 72.167.150.82
5. **IMPORTANTE**: Si usas Cloudflare, desactiva el proxy (nube gris) inicialmente para testing

---

## Registros DNS a configurar:

### Para ambos dominios (restaurantesmexicanosfamosos.com Y famousmexicanrestaurants.com):

| Tipo | Nombre/Host | Valor/Apunta a | TTL  |
|------|-------------|----------------|------|
| A    | @           | 72.167.150.82  | 3600 |
| A    | www         | 72.167.150.82  | 3600 |

**Notas:**
- `@` representa el dominio raíz (sin www)
- `www` es el subdominio www
- TTL en segundos (3600 = 1 hora)

---

## Verificar la propagación DNS

### Método 1: Comando dig (en tu Mac)

```bash
# Verificar dominio español
dig restaurantesmexicanosfamosos.com +short
dig www.restaurantesmexicanosfamosos.com +short

# Verificar dominio inglés
dig famousmexicanrestaurants.com +short
dig www.famousmexicanrestaurants.com +short
```

**Resultado esperado:** Deben mostrar `72.167.150.82`

### Método 2: Online

Visita estas páginas y busca tus dominios:
- https://www.whatsmydns.net/
- https://dnschecker.org/

Ingresa `restaurantesmexicanosfamosos.com` y verifica que muestre `72.167.150.82` en diferentes ubicaciones.

### Método 3: Ping

```bash
ping restaurantesmexicanosfamosos.com
```

Debe mostrar respuestas desde `72.167.150.82`

---

## Tiempo de propagación

- **Mínimo:** 15-30 minutos
- **Promedio:** 1-4 horas
- **Máximo:** 48 horas (raro)

**Tip:** Reducir el TTL antes del cambio acelera la propagación.

---

## Probar ANTES de que propague DNS (Opcional)

Si quieres probar la aplicación antes de que DNS propague, puedes modificar tu archivo `/etc/hosts` local:

```bash
sudo nano /etc/hosts
```

Agrega estas líneas:
```
72.167.150.82 restaurantesmexicanosfamosos.com
72.167.150.82 www.restaurantesmexicanosfamosos.com
72.167.150.82 famousmexicanrestaurants.com
72.167.150.82 www.famousmexicanrestaurants.com
```

Guarda (Ctrl+O, Enter, Ctrl+X)

Ahora puedes visitar `http://restaurantesmexicanosfamosos.com` en tu navegador y verás el sitio del VPS.

**IMPORTANTE:** Esto solo funciona en TU computadora. Otros usuarios no verán el sitio hasta que DNS propague.

---

## Después de DNS propagado

### 1. Verifica que el sitio carga

Visita en tu navegador:
- http://restaurantesmexicanosfamosos.com
- http://famousmexicanrestaurants.com

Deberías ver la página de inicio con los restaurantes.

### 2. Instala certificados SSL

```bash
ssh isaacjv@72.167.150.82

# Instalar Certbot
sudo yum install certbot python3-certbot-nginx -y

# Obtener certificados SSL
sudo certbot --nginx \
  -d restaurantesmexicanosfamosos.com \
  -d www.restaurantesmexicanosfamosos.com \
  -d famousmexicanrestaurants.com \
  -d www.famousmexicanrestaurants.com

# Seguir las instrucciones en pantalla
# Ingresa tu email
# Acepta términos
# Certbot configurará SSL automáticamente
```

### 3. Verificar SSL

Visita:
- https://restaurantesmexicanosfamosos.com
- https://famousmexicanrestaurants.com

Ambos deben cargar con el candado verde (HTTPS).

### 4. Configurar auto-renovación SSL

```bash
# Probar renovación
sudo certbot renew --dry-run

# Si funciona, el cron job ya está configurado
```

---

## Configurar Cron para Scraper

Una vez el sitio esté en producción, configura el scraper para agregar restaurantes automáticamente:

```bash
ssh isaacjv@72.167.150.82
crontab -e
```

Agrega esta línea:
```cron
# Scraper diario a las 2 AM
0 2 * * * cd /var/www/restaurantesmexicanos.com && php artisan scrape:restaurants --limit=50 >> /var/www/restaurantesmexicanos.com/storage/logs/scraper.log 2>&1
```

Guarda y cierra.

---

## Probar el Scraper manualmente

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com

# Probar con dry-run (no guarda)
php artisan scrape:restaurants --state=CA --limit=10 --dry-run

# Ejecutar real (guarda en BD)
php artisan scrape:restaurants --state=CA --limit=20

# Ver resultados
php artisan tinker
# En tinker:
\App\Models\Restaurant::count()
\App\Models\Restaurant::latest()->take(5)->get(['name', 'city', 'state_id'])
exit
```

---

## Migrar de SQLite a MySQL (FUTURO)

**NOTA:** Actualmente estás usando SQLite porque no pudimos acceder a MySQL root.

**Cuando obtengas acceso a MySQL root**, podrás migrar a MySQL:

### Paso 1: Crear base de datos MySQL

```bash
ssh isaacjv@72.167.150.82
mysql -u root -p

# En MySQL:
CREATE DATABASE restaurantesmexicanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restaurantesmexicanos_user'@'localhost' IDENTIFIED BY 'Password123!';
GRANT ALL PRIVILEGES ON restaurantesmexicanos.* TO 'restaurantesmexicanos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Paso 2: Exportar de SQLite

```bash
cd /var/www/restaurantesmexicanos.com

# Instalar herramienta de conversión
composer require --dev ifsnop/mysqldump-php

# O exportar manualmente vía seeders
php artisan db:seed --force
```

### Paso 3: Actualizar .env

```bash
nano .env
```

Cambiar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurantesmexicanos
DB_USERNAME=restaurantesmexicanos_user
DB_PASSWORD=Password123!
```

### Paso 4: Migrar y probar

```bash
php artisan migrate:fresh --seed --force
php artisan config:cache
```

---

## Resumen de URLs

Después de DNS propagado:

| URL | Descripción |
|-----|-------------|
| https://restaurantesmexicanosfamosos.com | Homepage (Español) |
| https://famousmexicanrestaurants.com | Homepage (English) |
| https://restaurantesmexicanosfamosos.com/restaurantes | Lista de restaurantes |
| https://restaurantesmexicanosfamosos.com/admin | Panel admin Filament |
| https://restaurantesmexicanosfamosos.com/sitemap.xml | Sitemap SEO |

---

## Troubleshooting

### DNS no propaga

```bash
# Limpiar caché DNS en tu Mac
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder

# Verificar qué DNS estás usando
scutil --dns | grep 'nameserver'
```

### Sitio muestra error 502

```bash
ssh isaacjv@72.167.150.82

# Verificar PHP-FPM
sudo systemctl status php-fpm
sudo systemctl restart php-fpm

# Verificar Nginx
sudo systemctl status nginx
sudo nginx -t

# Ver logs
sudo tail -50 /var/log/nginx/restaurantesmexicanos_error.log
```

### Sitio muestra error 500

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com

# Ver logs de Laravel
tail -50 storage/logs/laravel.log

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verificar permisos
sudo chown -R nginx:nginx /var/www/restaurantesmexicanos.com
sudo chmod -R 775 storage bootstrap/cache
```

---

## Información de Acceso

**VPS:**
- IP: 72.167.150.82
- Usuario SSH: isaacjv
- Password: LhWOY8q!QZWz@Fqu

**Proyecto:**
- Path: /var/www/restaurantesmexicanos.com
- Base de datos: SQLite en database/database.sqlite
- Logs: storage/logs/laravel.log

**Dominios:**
- restaurantesmexicanosfamosos.com
- famousmexicanrestaurants.com

---

## ¿Dónde están tus dominios actualmente?

Para ayudarte mejor, necesito saber:

1. **¿En qué registrador compraste los dominios?**
   - GoDaddy
   - Namecheap
   - Google Domains
   - Otro

2. **¿Dónde apuntan actualmente?**
   - Hosting compartido (¿cuál?)
   - Otro servidor

3. **¿Tienes acceso al panel de DNS?**
   - Sí, puedo entrar
   - No, necesito contactar soporte

---

Una vez me confirmes esta información, te puedo dar instrucciones más específicas para tu caso!

**El sitio ya está 95% listo en el VPS. Solo falta actualizar DNS y configurar SSL.** 🚀
