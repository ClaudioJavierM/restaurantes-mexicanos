# FAMER — Famous Mexican Restaurants

## Proyecto
- **Stack:** Laravel 12 + Livewire 3 + Filament 4 + Tailwind CSS v3 + Vite
- **Repo:** `isaacjv79/restaurantes-mexicanos` (GitHub, private)
- **Domains:** restaurantesmexicanosfamosos.com.mx (MX) | restaurantesmexicanosfamosos.com (US ES) | famousmexicanrestaurants.com (US EN)

## Servidor Producción
- **Path:** `/var/www/restaurantesmexicanosfamosos.com.mx` (CRITICO: NO `/var/www/restaurantesmexicanos.com`)
- **Branch:** `main`
- **Owner archivos:** nginx:nginx
- **Deploy key:** `/var/www/.ssh/id_ed25519` (owner: nginx, SSH config en `/var/www/.ssh/config`)
- **DB:** MySQL `restaurantesmexicanos`

## Deploy Producción (MANUAL)
```bash
ssh mfgroup@160.153.183.38
cd /var/www/restaurantesmexicanosfamosos.com.mx
sudo -u nginx GIT_SSH_COMMAND="ssh -i /var/www/.ssh/id_ed25519 -o IdentitiesOnly=yes" git fetch origin main
sudo -u nginx git reset --hard origin/main
sudo -u nginx composer install --no-dev --optimize-autoloader
sudo -u nginx php artisan migrate --force
sudo -u nginx php artisan config:cache
sudo -u nginx php artisan route:clear  # NO route:cache — closure routes in web.php prevent proper caching
sudo -u nginx php artisan view:clear
sudo chown -R nginx:nginx storage bootstrap/cache
sudo systemctl reload php-fpm  # OBLIGATORIO — limpia OPcache (sin esto, PHP sirve archivos viejos de memoria)
npm run build  # si hay cambios de assets/tailwind
sudo chmod -R 777 public/build  # si npm build falla por permisos en sw.js / workbox-*.js
```

## Servidor Staging
- **Path:** `/var/www/staging.restaurantesmexicanos.com`
- **URL:** `http://staging.restaurantesmexicanosfamosos.com` (HTTP, sin SSL)
- **Nginx config:** `/etc/nginx/conf.d/staging.restaurantesmexicanos.conf` (port 80, server_name staging.restaurantesmexicanosfamosos.com)
- **Cloudflare:** DNS record "staging" → A 160.153.183.38, **proxy OFF** (DNS only). Si se activa el proxy, Cloudflare aplica Redirect Rules que redirigen a /admin.
- **Firewall:** Puerto 8090 también abierto (acceso alternativo por IP: http://160.153.183.38:8090)
- **.env:** Copia de producción con `APP_ENV=staging` y `APP_URL=https://staging.restaurantesmexicanosfamosos.com`

## Deploy Staging
### Opción 1: rsync desde local (RECOMENDADO — deploy key puede no tener acceso)
```bash
cd /Users/javier/WebsProjects/restaurantesmexicanos
# Subir archivos cambiados
rsync -avz --relative \
  [archivos-modificados] \
  mfgroup@160.153.183.38:/var/www/staging.restaurantesmexicanos.com/

# Build assets en el servidor
ssh mfgroup@160.153.183.38 'cd /var/www/staging.restaurantesmexicanos.com && npm run build'

# Clear cache
ssh mfgroup@160.153.183.38 'cd /var/www/staging.restaurantesmexicanos.com && sudo -u nginx php artisan view:clear && sudo -u nginx php artisan config:clear && sudo -u nginx php artisan cache:clear'
```

### Opción 2: git pull (si deploy key tiene acceso)
```bash
ssh mfgroup@160.153.183.38
cd /var/www/staging.restaurantesmexicanos.com
sudo -u nginx GIT_SSH_COMMAND="ssh -i /var/www/.ssh/id_ed25519 -o IdentitiesOnly=yes" git fetch origin [branch]
sudo -u nginx git checkout [branch]
npm run build
sudo -u nginx php artisan view:clear && sudo -u nginx php artisan config:clear
```

### Setup staging desde cero (si no existe el directorio)
```bash
# 1. Copiar producción
ssh mfgroup@160.153.183.38
sudo cp -a /var/www/restaurantesmexicanosfamosos.com.mx /var/www/staging.restaurantesmexicanos.com
sudo chown -R mfgroup:nginx /var/www/staging.restaurantesmexicanos.com

# 2. Ajustar .env
sudo sed -i "s/APP_ENV=production/APP_ENV=staging/" /var/www/staging.restaurantesmexicanos.com/.env
sudo sed -i "s|APP_URL=https://restaurantesmexicanosfamosos.com.mx|APP_URL=https://staging.restaurantesmexicanosfamosos.com|" /var/www/staging.restaurantesmexicanos.com/.env

# 3. Nginx config ya existe en /etc/nginx/conf.d/staging.restaurantesmexicanos.conf
# Si no existe, crear:
sudo tee /etc/nginx/conf.d/staging.restaurantesmexicanos.conf << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name staging.restaurantesmexicanosfamosos.com;
    root /var/www/staging.restaurantesmexicanos.com/public;
    index index.php;
    charset utf-8;
    location ^~ /livewire { try_files $uri $uri/ /index.php?$query_string; }
    location / { try_files $uri $uri/ /index.php?$query_string; }
    error_page 404 /index.php;
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~ /\.(?!well-known).* { deny all; }
}
EOF
sudo nginx -t && sudo systemctl reload nginx

# 4. Cloudflare: DNS record "staging" A 160.153.183.38 con proxy OFF
# 5. Firewall: sudo firewall-cmd --add-port=8090/tcp --permanent && sudo firewall-cmd --reload

# 6. Rsync los archivos nuevos y npm run build
```

## Diseño Actual (2026-03-31)
- **Paleta:** #0B0B0B (base), #1A1A1A (charcoal), #2A2A2A (gray), #D4AF37 (gold), #1F3D2B (green), #8B1E1E (red), #F5F5F5 (white)
- **Logo:** `/public/images/branding/famer55.png` (nuevo logo premium)
- **Fonts:** Playfair Display (headings) + Poppins (body)
- **Tailwind:** Colores en namespace `famer.*` (famer-black, famer-gold, etc.)
- **Estilo:** Dark premium, Apple/Airbnb/Stripe feel, gold accents sparingly

## Base de Datos — Notas Críticas

### email_logs — Tabla compartida con SDV (Resend webhooks)
- La tabla `email_logs` recibe webhooks de Resend para TODOS los dominios (FAMER + SDV)
- Los emails SDV llegan con `from_email = NULL` — NO tienen `from_email` ni `restaurant_id`
- **SIEMPRE filtrar** `whereNotNull('from_email')` en cualquier query de email_logs dentro de FAMER
- El `EmailLogResource` ya tiene `getEloquentQuery()` con este filtro — NO removerlo
- El `EmailStatsOverview` widget también aplica el mismo filtro vía closure `$famer = fn() => EmailLog::whereNotNull('from_email')`

## APIs Externas — Credenciales

### Foursquare Places API v2 (activa — 2026-04-19)
- **Proyecto:** Famer (`foursquare.com/developers`)
- **Client ID:** `IICLZWAHDPYBDREQL0BSVVBMK3TIANDI5GJ3VHSUUJ2YZZ4A`
- **Client Secret:** `4V43C5RSGYNYXW3TCIFPL4D1XSF4SDY1QHA0KLE2BH3O20JI`
- **Service API Key (v3 — famer1):** `MUNA3W4KVDHC2XGWITQCLXSOJNCYHEJZNQ3BPCMZHP2FZ31P`
- **API usada:** v2 (`https://api.foursquare.com/v2`) — v3 devuelve 401 (Service Key recién creada, puede activarse después)
- **Auth v2:** `client_id` + `client_secret` como query params (NO header)
- **Endpoints:** `/venues/search` (búsqueda) + `/venues/{id}` (detalle)
- **Config Laravel:** `services.foursquare.client_id` / `client_secret` / `service_key`
- **Límite mensual:** 5,000 calls → **160/día** (`FOURSQUARE_DAILY_LIMIT=160` en .env)
- **NOTA:** La key Legacy "FAMER" del dashboard es la antigua — ya NO está en .env

### TripAdvisor Content API (2026-04-19)
- **Dashboard:** tripadvisor.com/developers?screen=credentials
- **Plan:** Free — 5,000 calls/mes (límite para evitar facturación)
- **API Key activa:** `340C672E9BC54B998663447328186BAD` (generada 2026-04-19, esperar 15 min para activar)
- **API Key anterior:** `5217A51EFB234701BAC29C116F55F5DD` (da 403 — bloqueada/límite alcanzado)
- **IP Restriction:** 160.153.183.38/32 (solo VPS puede usar la key)
- **Endpoint:** `https://api.content.tripadvisor.com/api/v1/location/search?key=KEY&searchQuery=...&category=restaurants`
- **Límite diario Laravel:** 160 calls/día (configurable con `TRIPADVISOR_DAILY_LIMIT`)
- **Scheduler:** `tripadvisor:enrich-restaurants --limit=80` diario 7am ET

### Columnas agregadas manualmente (no en migraciones)

- `reviews.is_approved` — columna VIRTUAL GENERATED: `IF(status='approved',1,0)`
- `email_logs.delivered_at` — agregada vía ALTER TABLE
- `famer_email_1_sent_at`, `famer_email_2_sent_at`, `famer_email_3_sent_at` en `restaurants`

## Colaboración — Git Workflow (Javier + Claudio)

### Roles
- **Javier (Isaac):** push directo a `main` — sin cambios en su workflow
- **Claudio:** trabaja en branch `claudio/features`, abre PR o hace push directo a main cuando está listo

### Regla de Claudio — sync diario obligatorio
```bash
# Al INICIO de cada sesión de trabajo (SIEMPRE, antes de tocar código):
git checkout claudio/features
git merge main     # traer cambios de Javier
# ... trabajar ...
git push origin claudio/features

# Para subir a producción:
git merge main     # sync final antes de deployar
git push origin claudio/features:main
```

### Áreas de responsabilidad (evitar conflictos)
| Área | Responsable |
|------|-------------|
| `app/Filament/Resources/` — admin panel | Javier |
| `app/Filament/Pages/` — admin pages | Javier |
| `app/Console/Commands/` + `app/Jobs/` | Javier |
| Campañas email backend | Javier |
| `app/Filament/Owner/` — owner panel | Claudio |
| `app/Livewire/Owner/` | Claudio |
| `resources/views/livewire/owner/` | Claudio |
| Frontend público (`resources/views/livewire/`) | Claudio |

### Archivos de alto riesgo — avisar antes de tocar
- `routes/web.php` — el más conflictivo
- `bootstrap/app.php`
- `config/*.php`
- `tailwind.config.js`

## Reglas

- NUNCA editar archivos directamente en el VPS — todo por git o rsync desde local
- NUNCA usar `rsync --delete` (borra storage/ y vendor/)
- El tailwind.config.js usa `famer.*` colors — NO usar el override de `red` como gold (fue removido)
- Archivos blade que usan `red-600` de Tailwind ahora es el rojo real de Tailwind (ya no gold)
- npm run build necesario después de cambiar blade templates (Tailwind JIT)
- Filament 4: los NavigationGroup NO pueden tener íconos si sus items también los tienen (500 error)
- Todos los widgets Filament deben tener `protected static bool $isLazy = true;`
