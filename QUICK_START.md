# Quick Start Guide - Deploy to Production

## Current Status

✅ **Code uploaded** to VPS: `/var/www/restaurantesmexicanos.com/`
✅ **Composer dependencies installed**
✅ **APP_KEY generated**
✅ **Storage link created**
✅ **Database backup uploaded** to `/tmp/restaurantesmexicanos_backup.sql`

---

## What You Need to Do Now

### Option 1: Automated Setup (Recommended)

SSH into the VPS and run the automated setup script:

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com
./vps_setup.sh
```

The script will guide you through:
1. Database creation
2. Database import
3. Permissions setup
4. Laravel optimization
5. Web server configuration instructions

---

### Option 2: Manual Setup

If you prefer to do it manually, follow these steps:

#### 1. SSH into VPS

```bash
ssh isaacjv@72.167.150.82
```

#### 2. Create Production Database

You need MySQL root password. If you don't have it, check with your hosting provider.

```bash
# Option A: Use the SQL script
mysql -u root -p < /tmp/create_database.sql

# Option B: Manual commands
mysql -u root -p
```

Then execute:
```sql
CREATE DATABASE IF NOT EXISTS restaurantesmexicanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'restaurantesmexicanos_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON restaurantesmexicanos.* TO 'restaurantesmexicanos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**IMPORTANT**: Replace `STRONG_PASSWORD_HERE` with a secure password!

#### 3. Update .env File

```bash
cd /var/www/restaurantesmexicanos.com
nano .env
```

Update these lines:
```env
DB_DATABASE=restaurantesmexicanos
DB_USERNAME=restaurantesmexicanos_user
DB_PASSWORD=YOUR_PASSWORD_FROM_STEP_2

# Also update:
GOOGLE_PLACES_API_KEY=your_production_key
GOOGLE_MAPS_API_KEY=your_production_key
GOOGLE_ANALYTICS_ES=G-XXXXXXXXXX
GOOGLE_ANALYTICS_EN=G-YYYYYYYYYY
```

#### 4. Import Database

```bash
cd /var/www/restaurantesmexicanos.com

# Get credentials from .env
DB_USER=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)

# Import backup
mysql -u $DB_USER -p $DB_NAME < /tmp/restaurantesmexicanos_backup.sql

# Clean up
rm /tmp/restaurantesmexicanos_backup.sql
```

#### 5. Run Migrations

```bash
cd /var/www/restaurantesmexicanos.com
php artisan migrate --force
```

#### 6. Set Permissions

```bash
cd /var/www/restaurantesmexicanos.com
sudo chown -R nginx:nginx .
sudo chmod -R 775 storage bootstrap/cache
```

#### 7. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
```

---

## Web Server Configuration

### Check Current Setup

First, check what web server is running:

```bash
# Check Nginx
sudo nginx -v
sudo systemctl status nginx

# OR check Apache
sudo httpd -v
sudo systemctl status httpd
```

### For Nginx

Create configuration file:

```bash
sudo nano /etc/nginx/conf.d/restaurantesmexicanos.conf
```

Add this configuration:

```nginx
server {
    listen 80;
    server_name restaurantesmexicanosfamosos.com www.restaurantesmexicanosfamosos.com famousmexicanrestaurants.com www.famousmexicanrestaurants.com;

    root /var/www/restaurantesmexicanos.com/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/www.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Test and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### For Apache

Create configuration file:

```bash
sudo nano /etc/httpd/conf.d/restaurantesmexicanos.conf
```

Add this configuration:

```apache
<VirtualHost *:80>
    ServerName restaurantesmexicanosfamosos.com
    ServerAlias www.restaurantesmexicanosfamosos.com famousmexicanrestaurants.com www.famousmexicanrestaurants.com

    DocumentRoot /var/www/restaurantesmexicanos.com/public

    <Directory /var/www/restaurantesmexicanos.com/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog /var/log/httpd/restaurantesmexicanos_error.log
    CustomLog /var/log/httpd/restaurantesmexicanos_access.log combined
</VirtualHost>
```

Enable and restart:

```bash
sudo systemctl reload httpd
```

---

## DNS Configuration

Update your DNS records at your domain registrar to point to the VPS:

**For both domains:**
- restaurantesmexicanosfamosos.com
- famousmexicanrestaurants.com

Add these A records:
```
Type: A
Name: @
Value: 72.167.150.82
TTL: 3600

Type: A
Name: www
Value: 72.167.150.82
TTL: 3600
```

**Note**: DNS propagation takes 1-48 hours (usually 1-4 hours)

---

## SSL Certificates (After DNS Propagation)

Once DNS is pointing to the VPS, install SSL certificates:

```bash
# Install Certbot
sudo yum install certbot python3-certbot-nginx -y
# OR for Apache:
sudo yum install certbot python3-certbot-apache -y

# Get certificates
sudo certbot --nginx -d restaurantesmexicanosfamosos.com -d www.restaurantesmexicanosfamosos.com -d famousmexicanrestaurants.com -d www.famousmexicanrestaurants.com

# OR for Apache:
sudo certbot --apache -d restaurantesmexicanosfamosos.com -d www.restaurantesmexicanosfamosos.com -d famousmexicanrestaurants.com -d www.famousmexicanrestaurants.com

# Test auto-renewal
sudo certbot renew --dry-run
```

---

## Setup Cron Job for Scraper

```bash
crontab -e
```

Add this line:
```cron
# Run scraper every night at 2 AM
0 2 * * * cd /var/www/restaurantesmexicanos.com && php artisan scrape:restaurants --limit=50 >> /var/www/restaurantesmexicanos.com/storage/logs/scraper.log 2>&1
```

---

## Test Your Application

### Quick Test (Development Server)

```bash
cd /var/www/restaurantesmexicanos.com
php artisan serve --host=0.0.0.0 --port=8000
```

Visit: `http://72.167.150.82:8000`

### Production Test

Once web server is configured and DNS is updated:

1. **Homepage (Spanish)**: https://restaurantesmexicanosfamosos.com
2. **Homepage (English)**: https://famousmexicanrestaurants.com
3. **Restaurants List**: https://restaurantesmexicanosfamosos.com/restaurantes
4. **Admin Panel**: https://restaurantesmexicanosfamosos.com/admin
5. **Sitemap**: https://restaurantesmexicanosfamosos.com/sitemap.xml

---

## Troubleshooting

### Check Laravel Logs

```bash
tail -f /var/www/restaurantesmexicanos.com/storage/logs/laravel.log
```

### Check Web Server Logs

```bash
# Nginx
sudo tail -f /var/log/nginx/error.log

# Apache
sudo tail -f /var/log/httpd/restaurantesmexicanos_error.log
```

### 500 Server Error

```bash
cd /var/www/restaurantesmexicanos.com

# Check permissions
sudo chown -R nginx:nginx .
sudo chmod -R 775 storage bootstrap/cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Connection Issues

```bash
cd /var/www/restaurantesmexicanos.com

# Test connection
php artisan tinker
# In tinker:
DB::connection()->getPdo();
\App\Models\Restaurant::count();
exit
```

---

## Important Files on VPS

- **Project**: `/var/www/restaurantesmexicanos.com/`
- **Environment**: `/var/www/restaurantesmexicanos.com/.env`
- **Setup Script**: `/var/www/restaurantesmexicanos.com/vps_setup.sh`
- **Full Guide**: `/var/www/restaurantesmexicanos.com/DEPLOYMENT.md`
- **Database SQL**: `/tmp/create_database.sql`
- **Backup**: `/tmp/restaurantesmexicanos_backup.sql`

---

## Post-Deployment Checklist

- [ ] Database created and backup imported
- [ ] .env file configured with production credentials
- [ ] Web server configured (Nginx/Apache)
- [ ] DNS records updated
- [ ] SSL certificates installed
- [ ] Application accessible via domains
- [ ] Admin panel working
- [ ] Google Maps API working
- [ ] Cron job configured for scraper
- [ ] Test scraper: `php artisan scrape:restaurants --state=CA --limit=10`

---

## Need Help?

Read the full deployment guide:
```bash
cat /var/www/restaurantesmexicanos.com/DEPLOYMENT.md
```

Or check the logs for specific errors.

---

**Good luck with the deployment! 🚀**
