# Siguiente Paso: Configurar Base de Datos en VPS

## Situación Actual

✅ **Completado:**
- Código subido a `/var/www/restaurantesmexicanos.com/`
- Composer instalado
- APP_KEY generado
- Backup de base de datos en `/tmp/restaurantesmexicanos_backup.sql`
- Nginx corriendo en el VPS

❌ **Necesario:**
- Crear base de datos MySQL
- Importar datos
- Configurar Nginx

---

## OPCIÓN 1: Contactar al Proveedor de Hosting (MÁS RÁPIDO)

**Recomendado si no tienes acceso root a MySQL**

1. Contacta a tu proveedor de hosting (GoDaddy/otro)
2. Pídeles que:
   - Te den acceso root de MySQL, O
   - Creen la base de datos `restaurantesmexicanos` para ti, O
   - Te den acceso a cPanel/Plesk para crear la base de datos

3. Una vez tengas acceso, sigue con la Opción 2 o 3

---

## OPCIÓN 2: Usar cPanel/Plesk (SI TIENES ACCESO)

Si tu VPS tiene cPanel o Plesk:

1. Accede al panel de control
2. Ve a "MySQL Databases" o "Bases de datos MySQL"
3. Crea una nueva base de datos: `restaurantesmexicanos`
4. Crea un usuario: `restaurantesmexicanos_user`
5. Asigna el usuario a la base de datos con todos los privilegios
6. Anota la contraseña que usaste
7. Ve a "phpMyAdmin"
8. Selecciona la base de datos `restaurantesmexicanos`
9. Ve a "Importar"
10. Sube el archivo `/tmp/restaurantesmexicanos_backup.sql`
11. Haz clic en "Continuar"

Luego continúa con el paso "Configurar .env" abajo.

---

## OPCIÓN 3: Usar MySQL Root (SI TIENES LA CONTRASEÑA)

### A. Buscar la contraseña de MySQL root

Intenta estos comandos en el VPS:

```bash
ssh isaacjv@72.167.150.82

# Buscar en archivos de configuración
sudo grep -r "password" /root/.my.cnf 2>/dev/null
sudo cat /root/.mysql_secret 2>/dev/null
sudo cat /var/log/mysqld.log | grep "temporary password"

# O revisar si hay acceso sin contraseña
sudo mysql -e "SELECT 1;"
```

### B. Si encuentras acceso, ejecuta:

```bash
# Conectar a MySQL
sudo mysql
# O si requiere contraseña:
mysql -u root -p
```

### C. En el prompt de MySQL, ejecuta:

```sql
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS restaurantesmexicanos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Crear usuario (cambia la contraseña)
CREATE USER 'restaurantesmexicanos_user'@'localhost'
  IDENTIFIED BY 'TuContraseñaSegura123!';

-- Dar permisos
GRANT ALL PRIVILEGES ON restaurantesmexicanos.*
  TO 'restaurantesmexicanos_user'@'localhost';

FLUSH PRIVILEGES;

-- Verificar
SHOW DATABASES LIKE 'restaurantesmexicanos';

EXIT;
```

### D. Importar el backup:

```bash
cd /var/www/restaurantesmexicanos.com

# Importar
mysql -u restaurantesmexicanos_user -p restaurantesmexicanos < /tmp/restaurantesmexicanos_backup.sql

# Verificar
mysql -u restaurantesmexicanos_user -p restaurantesmexicanos -e "SHOW TABLES; SELECT COUNT(*) FROM restaurants;"
```

---

## Después de Crear la Base de Datos

### 1. Actualizar el archivo .env

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com
nano .env
```

Actualiza estas líneas:

```env
DB_DATABASE=restaurantesmexicanos
DB_USERNAME=restaurantesmexicanos_user
DB_PASSWORD=LA_CONTRASEÑA_QUE_USASTE

# También actualiza estas (IMPORTANTE):
GOOGLE_PLACES_API_KEY=tu_api_key_de_produccion
GOOGLE_MAPS_API_KEY=tu_api_key_de_produccion
GOOGLE_ANALYTICS_ES=G-XXXXXXXXXX
GOOGLE_ANALYTICS_EN=G-YYYYYYYYYY

# Mail (si quieres enviar emails)
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
```

Guarda con `Ctrl+O`, `Enter`, `Ctrl+X`

### 2. Probar la conexión

```bash
cd /var/www/restaurantesmexicanos.com
php artisan tinker
```

En tinker, ejecuta:
```php
DB::connection()->getPdo();
\App\Models\Restaurant::count();
exit
```

Si ves un número (15), ¡funciona! ✅

### 3. Ejecutar migraciones (si hay nuevas)

```bash
php artisan migrate --force
```

### 4. Optimizar Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

---

## Configurar Nginx

### 1. Verificar configuración actual de Nginx

```bash
sudo cat /etc/nginx/nginx.conf | grep -A 10 "server {"
ls -la /etc/nginx/conf.d/
ls -la /etc/nginx/sites-enabled/
```

### 2. Crear configuración para el sitio

```bash
sudo nano /etc/nginx/conf.d/restaurantesmexicanos.conf
```

Pega esto:

```nginx
server {
    listen 80;
    server_name restaurantesmexicanosfamosos.com www.restaurantesmexicanosfamosos.com famousmexicanrestaurants.com www.famousmexicanrestaurants.com;

    root /var/www/restaurantesmexicanos.com/public;
    index index.php index.html;

    # Logging
    access_log /var/log/nginx/restaurantesmexicanos_access.log;
    error_log /var/log/nginx/restaurantesmexicanos_error.log;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Guarda con `Ctrl+O`, `Enter`, `Ctrl+X`

### 3. Ajustar permisos

```bash
sudo chown -R nginx:nginx /var/www/restaurantesmexicanos.com
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/storage
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/bootstrap/cache
```

### 4. Probar y recargar Nginx

```bash
# Probar configuración
sudo nginx -t

# Si dice "syntax is ok", recargar
sudo systemctl reload nginx
```

---

## Probar la Aplicación

### Opción A: Servidor de desarrollo (temporal)

```bash
cd /var/www/restaurantesmexicanos.com
php artisan serve --host=0.0.0.0 --port=8000
```

Visita en tu navegador: `http://72.167.150.82:8000`

### Opción B: Editar /etc/hosts (para probar Nginx antes de DNS)

En tu computadora local, edita `/etc/hosts`:

```bash
sudo nano /etc/hosts
```

Agrega:
```
72.167.150.82 restaurantesmexicanosfamosos.com
72.167.150.82 famousmexicanrestaurants.com
```

Luego visita: `http://restaurantesmexicanosfamosos.com`

---

## Actualizar DNS

**Solo cuando todo funcione**, actualiza los DNS en tu registrador de dominios:

**Para ambos dominios:**
- restaurantesmexicanosfamosos.com
- famousmexicanrestaurants.com

**Registros A:**
```
Tipo: A
Nombre: @
Valor: 72.167.150.82
TTL: 3600

Tipo: A
Nombre: www
Valor: 72.167.150.82
TTL: 3600
```

Espera 1-4 horas para propagación DNS.

---

## Instalar SSL (Después de DNS)

```bash
# Instalar Certbot
sudo yum install certbot python3-certbot-nginx -y

# Obtener certificados
sudo certbot --nginx \
  -d restaurantesmexicanosfamosos.com \
  -d www.restaurantesmexicanosfamosos.com \
  -d famousmexicanrestaurants.com \
  -d www.famousmexicanrestaurants.com

# Probar auto-renovación
sudo certbot renew --dry-run
```

---

## Configurar Cron para Scraper

```bash
crontab -e
```

Agrega:
```cron
# Scraper cada noche a las 2 AM
0 2 * * * cd /var/www/restaurantesmexicanos.com && php artisan scrape:restaurants --limit=50 >> /var/www/restaurantesmexicanos.com/storage/logs/scraper.log 2>&1
```

---

## Resumen de Comandos Rápidos

```bash
# 1. Conectar al VPS
ssh isaacjv@72.167.150.82

# 2. Ir al proyecto
cd /var/www/restaurantesmexicanos.com

# 3. Ver logs
tail -f storage/logs/laravel.log

# 4. Limpiar caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# 5. Ver errores de Nginx
sudo tail -f /var/log/nginx/error.log

# 6. Reiniciar servicios
sudo systemctl reload nginx
sudo systemctl restart php-fpm
```

---

## ¿Qué Opción Elegir?

- **¿Tienes cPanel/Plesk?** → Usa **Opción 2** (más fácil)
- **¿Sabes la contraseña root de MySQL?** → Usa **Opción 3**
- **¿No tienes ninguno?** → Usa **Opción 1** (contacta soporte)

---

## Información de Acceso

- **VPS IP:** 72.167.150.82
- **SSH User:** isaacjv
- **SSH Pass:** LhWOY8q!QZWz@Fqu
- **Proyecto:** /var/www/restaurantesmexicanos.com
- **Backup:** /tmp/restaurantesmexicanos_backup.sql

---

**¡Todo está listo! Solo falta crear la base de datos y configurar Nginx!** 🚀
