#!/bin/bash

# VPS Setup Script for restaurantesmexicanos.com
# Run this script on the VPS as isaacjv user

set -e  # Exit on any error

echo "================================================="
echo "VPS Setup for restaurantesmexicanos.com"
echo "================================================="
echo ""

PROJECT_DIR="/var/www/restaurantesmexicanos.com"
cd $PROJECT_DIR

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Step 1: Check if composer dependencies are installed
echo -e "${YELLOW}Step 1: Checking Composer dependencies...${NC}"
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --optimize-autoloader --no-dev
else
    echo -e "${GREEN}✓ Composer dependencies already installed${NC}"
fi
echo ""

# Step 2: Check APP_KEY
echo -e "${YELLOW}Step 2: Checking APP_KEY...${NC}"
if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
else
    echo -e "${GREEN}✓ APP_KEY already set${NC}"
fi
echo ""

# Step 3: Database Configuration
echo -e "${YELLOW}Step 3: Database Configuration${NC}"
echo -e "${RED}IMPORTANT: You need to configure the database manually!${NC}"
echo ""
echo "Current .env database settings:"
grep "^DB_" .env
echo ""
echo "To set up the database:"
echo "1. Get MySQL root password"
echo "2. Run: mysql -u root -p"
echo "3. Execute these SQL commands:"
echo ""
echo "   CREATE DATABASE IF NOT EXISTS restaurantesmexicanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "   CREATE USER 'restaurantesmexicanos_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';"
echo "   GRANT ALL PRIVILEGES ON restaurantesmexicanos.* TO 'restaurantesmexicanos_user'@'localhost';"
echo "   FLUSH PRIVILEGES;"
echo "   EXIT;"
echo ""
echo "4. Update .env file with the correct DB_PASSWORD"
echo ""
read -p "Press ENTER once database is created and .env is updated..."
echo ""

# Step 4: Test database connection
echo -e "${YELLOW}Step 4: Testing database connection...${NC}"
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection successful!';" 2>&1
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Database connection failed. Please check your .env settings.${NC}"
    exit 1
fi
echo ""

# Step 5: Import database backup
echo -e "${YELLOW}Step 5: Importing database backup...${NC}"
if [ -f "/tmp/restaurantesmexicanos_backup.sql" ]; then
    echo "Importing database from backup..."
    DB_USER=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
    DB_NAME=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
    DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)

    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < /tmp/restaurantesmexicanos_backup.sql

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database imported successfully${NC}"
        rm /tmp/restaurantesmexicanos_backup.sql
        echo "✓ Backup file removed"
    else
        echo -e "${RED}✗ Database import failed${NC}"
        exit 1
    fi
else
    echo -e "${RED}✗ Backup file not found at /tmp/restaurantesmexicanos_backup.sql${NC}"
    exit 1
fi
echo ""

# Step 6: Run migrations
echo -e "${YELLOW}Step 6: Running migrations...${NC}"
php artisan migrate --force
echo ""

# Step 7: Set proper permissions
echo -e "${YELLOW}Step 7: Setting file permissions...${NC}"
sudo chown -R nginx:nginx $PROJECT_DIR
sudo chmod -R 775 $PROJECT_DIR/storage
sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

# Step 8: Cache optimization
echo -e "${YELLOW}Step 8: Optimizing Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
echo -e "${GREEN}✓ Caches built${NC}"
echo ""

# Step 9: Check web server configuration
echo -e "${YELLOW}Step 9: Web Server Configuration${NC}"
echo ""
echo "You need to configure the web server (Nginx/Apache) to serve this application."
echo ""
echo "For Nginx, create: /etc/nginx/sites-available/restaurantesmexicanos.conf"
echo "For Apache, create: /etc/apache2/sites-available/restaurantesmexicanos.conf"
echo ""
echo "See DEPLOYMENT.md for detailed web server configuration instructions."
echo ""

# Step 10: SSL Certificates
echo -e "${YELLOW}Step 10: SSL Certificates${NC}"
echo ""
echo "After DNS propagation, run Certbot to get SSL certificates:"
echo "  sudo certbot --nginx -d restaurantesmexicanosfamosos.com -d www.restaurantesmexicanosfamosos.com -d famousmexicanrestaurants.com -d www.famousmexicanrestaurants.com"
echo ""

# Summary
echo "================================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "================================================="
echo ""
echo "Next steps:"
echo "1. Configure web server (Nginx/Apache) - See DEPLOYMENT.md"
echo "2. Test the application"
echo "3. Update DNS records to point to this VPS"
echo "4. Install SSL certificates with Certbot"
echo "5. Setup cron job for nightly scraping"
echo ""
echo "To test the application now, run:"
echo "  php artisan serve --host=0.0.0.0 --port=8000"
echo ""
