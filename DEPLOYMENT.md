# Deployment Guide - Restaurantes Mexicanos
## Production VPS: 72.167.150.82

---

## Pre-Deployment Checklist

- [x] Database exported (`database_backup.sql` - 16KB)
- [x] Production `.env.production` created
- [ ] VPS access confirmed
- [ ] Production database credentials ready
- [ ] Google API keys for production ready
- [ ] Google Analytics tracking IDs ready
- [ ] Mail credentials configured

---

## Step 1: Connect to VPS

```bash
ssh isaacjv@72.167.150.82
```

Password: `LhWOY8q!QZWz@Fqu`

---

## Step 2: Prepare VPS Directory Structure

```bash
# Create project directory
sudo mkdir -p /var/www/restaurantesmexicanos.com
sudo chown isaacjv:isaacjv /var/www/restaurantesmexicanos.com

# Navigate to directory
cd /var/www/restaurantesmexicanos.com
```

---

## Step 3: Upload Files from Local to VPS

### Option A: Using rsync (Recommended - Faster)

From your **local machine**:

```bash
cd /Users/javier/WebsProjects/restaurantesmexicanos.com

# Sync files (excluding unnecessary files)
rsync -avz --progress \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='.git' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='.env' \
  --exclude='database_backup.sql' \
  ./ isaacjv@72.167.150.82:/var/www/restaurantesmexicanos.com/
```

### Option B: Using SCP (Alternative)

```bash
# Create tarball locally
tar -czf restaurantesmexicanos.tar.gz \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='.git' \
  --exclude='storage/logs/*' \
  --exclude='.env' \
  .

# Upload tarball
scp restaurantesmexicanos.tar.gz isaacjv@72.167.150.82:/var/www/restaurantesmexicanos.com/

# SSH into VPS and extract
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com
tar -xzf restaurantesmexicanos.tar.gz
rm restaurantesmexicanos.tar.gz
```

---

## Step 4: Upload Production .env File

From your **local machine**:

```bash
# Upload .env.production as .env
scp .env.production isaacjv@72.167.150.82:/var/www/restaurantesmexicanos.com/.env
```

**IMPORTANT**: Edit the production `.env` file on VPS:
1. Set database credentials
2. Add Google API keys
3. Add Google Analytics IDs
4. Configure mail settings
5. Generate APP_KEY (done in Step 6)

---

## Step 5: Upload Database Backup

From your **local machine**:

```bash
scp database_backup.sql isaacjv@72.167.150.82:/tmp/restaurantesmexicanos_backup.sql
```

---

## Step 6: Configure Production Environment on VPS

SSH into VPS:

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com
```

### Install Dependencies

```bash
# Install Composer dependencies (production only, no dev)
composer install --optimize-autoloader --no-dev

# Install NPM dependencies
npm install

# Build production assets
npm run build
```

### Configure Laravel

```bash
# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## Step 7: Configure Production Database

### Create Database and User

```bash
# Connect to MySQL
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE restaurantesmexicanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'restaurantesmexicanos_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

GRANT ALL PRIVILEGES ON restaurantesmexicanos.* TO 'restaurantesmexicanos_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### Import Database Backup

```bash
mysql -u restaurantesmexicanos_user -p restaurantesmexicanos < /tmp/restaurantesmexicanos_backup.sql

# Clean up
rm /tmp/restaurantesmexicanos_backup.sql
```

### Run Migrations (if any new ones)

```bash
php artisan migrate --force
```

### Optimize Laravel

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache icons (Filament)
php artisan icons:cache
```

---

## Step 8: Configure Web Server (Apache/Nginx)

### Option A: Apache Configuration

Create virtual host file:

```bash
sudo nano /etc/apache2/sites-available/restaurantesmexicanos.conf
```

Add configuration:

```apache
<VirtualHost *:80>
    ServerName restaurantesmexicanosfamosos.com
    ServerAlias famousmexicanrestaurants.com
    ServerAlias www.restaurantesmexicanosfamosos.com
    ServerAlias www.famousmexicanrestaurants.com

    DocumentRoot /var/www/restaurantesmexicanos.com/public

    <Directory /var/www/restaurantesmexicanos.com/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/restaurantesmexicanos_error.log
    CustomLog ${APACHE_LOG_DIR}/restaurantesmexicanos_access.log combined
</VirtualHost>
```

Enable site:

```bash
sudo a2ensite restaurantesmexicanos.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### Option B: Nginx Configuration

Create server block:

```bash
sudo nano /etc/nginx/sites-available/restaurantesmexicanos
```

Add configuration:

```nginx
server {
    listen 80;
    server_name restaurantesmexicanosfamosos.com famousmexicanrestaurants.com www.restaurantesmexicanosfamosos.com www.famousmexicanrestaurants.com;

    root /var/www/restaurantesmexicanos.com/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/restaurantesmexicanos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Step 9: Setup SSL Certificates (Certbot)

```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx

# Generate certificates for both domains
sudo certbot --apache -d restaurantesmexicanosfamosos.com -d www.restaurantesmexicanosfamosos.com -d famousmexicanrestaurants.com -d www.famousmexicanrestaurants.com

# OR for Nginx:
sudo certbot --nginx -d restaurantesmexicanosfamosos.com -d www.restaurantesmexicanosfamosos.com -d famousmexicanrestaurants.com -d www.famousmexicanrestaurants.com

# Test auto-renewal
sudo certbot renew --dry-run
```

---

## Step 10: Configure Cron Job for Nightly Scraping

```bash
# Edit crontab
crontab -e

# Add this line to run scraper every night at 2 AM
0 2 * * * cd /var/www/restaurantesmexicanos.com && php artisan scrape:restaurants --limit=50 >> /var/www/restaurantesmexicanos.com/storage/logs/scraper.log 2>&1
```

Alternative: Laravel Scheduler (if using)

```bash
# Add to crontab
* * * * * cd /var/www/restaurantesmexicanos.com && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 11: Update DNS Records

In your domain registrar (where domains are registered):

1. **restaurantesmexicanosfamosos.com**:
   - A Record: `@` → `72.167.150.82`
   - A Record: `www` → `72.167.150.82`

2. **famousmexicanrestaurants.com**:
   - A Record: `@` → `72.167.150.82`
   - A Record: `www` → `72.167.150.82`

**Note**: DNS propagation takes 1-48 hours (usually 1-4 hours)

---

## Step 12: Post-Deployment Verification

### Test Application

```bash
# Check Laravel is working
curl -I https://restaurantesmexicanosfamosos.com

# Check database connection
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com
php artisan tinker
# In tinker:
\App\Models\Restaurant::count()
exit
```

### Monitor Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Apache logs
tail -f /var/log/apache2/restaurantesmexicanos_error.log

# Nginx logs
tail -f /var/log/nginx/error.log

# Scraper logs
tail -f storage/logs/scraper.log
```

### Test Key Features

1. Visit homepage: https://restaurantesmexicanosfamosos.com
2. Test restaurant list: https://restaurantesmexicanosfamosos.com/restaurantes
3. Test restaurant detail page
4. Test suggestion form: https://restaurantesmexicanosfamosos.com/sugerir
5. Test admin panel: https://restaurantesmexicanosfamosos.com/admin
6. Test English version: https://famousmexicanrestaurants.com
7. Check sitemap: https://restaurantesmexicanosfamosos.com/sitemap.xml

---

## Step 13: Run Initial Scraping (Manual)

```bash
ssh isaacjv@72.167.150.82
cd /var/www/restaurantesmexicanos.com

# Test with dry-run first
php artisan scrape:restaurants --state=CA --limit=10 --dry-run

# Run actual scraping (start with one state)
php artisan scrape:restaurants --state=CA --limit=50

# Monitor progress in another terminal
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/restaurantesmexicanos.com
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/storage
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/bootstrap/cache
```

### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Connection Issues

Check `.env` file has correct credentials:
```bash
cat .env | grep DB_
```

Test connection:
```bash
php artisan tinker
DB::connection()->getPdo();
```

### 500 Server Error

Check Laravel logs:
```bash
tail -50 storage/logs/laravel.log
```

Enable debug temporarily:
```bash
# Edit .env
APP_DEBUG=true
# Visit site to see error
# Don't forget to set back to false!
```

---

## Maintenance Commands

### Update Application (Future Updates)

```bash
cd /var/www/restaurantesmexicanos.com

# Put in maintenance mode
php artisan down

# Pull latest code (if using Git)
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Bring back online
php artisan up
```

### Backup Database (Regular Backups)

```bash
# Create backup directory
mkdir -p /var/www/restaurantesmexicanos.com/backups

# Create backup
mysqldump -u restaurantesmexicanos_user -p restaurantesmexicanos > /var/www/restaurantesmexicanos.com/backups/backup_$(date +%Y%m%d_%H%M%S).sql

# Optionally download to local
scp isaacjv@72.167.150.82:/var/www/restaurantesmexicanos.com/backups/backup_*.sql ~/Desktop/
```

---

## Security Checklist

- [ ] `.env` file is NOT accessible via web (Laravel handles this)
- [ ] APP_DEBUG=false in production
- [ ] Strong database password set
- [ ] SSL certificates installed and working
- [ ] File permissions correctly set (775 storage, 644 files, 755 dirs)
- [ ] Firewall configured (UFW or iptables)
- [ ] Regular backups scheduled
- [ ] Monitor logs regularly
- [ ] Keep dependencies updated

---

## Performance Optimization (Optional)

### Enable OPcache

Edit PHP configuration:
```bash
sudo nano /etc/php/8.2/apache2/php.ini
# OR
sudo nano /etc/php/8.2/fpm/php.ini
```

Set:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.revalidate_freq=60
```

### Queue Workers (For future queue jobs)

```bash
# Install supervisor
sudo apt install supervisor

# Create worker config
sudo nano /etc/supervisor/conf.d/restaurantesmexicanos-worker.conf
```

---

## Contact Information

- **VPS IP**: 72.167.150.82
- **SSH User**: isaacjv
- **Project Path**: /var/www/restaurantesmexicanos.com
- **Domains**:
  - restaurantesmexicanosfamosos.com (Spanish)
  - famousmexicanrestaurants.com (English)

---

**Last Updated**: 2025-11-03
