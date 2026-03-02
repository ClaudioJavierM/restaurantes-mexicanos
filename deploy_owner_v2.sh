#!/bin/bash

echo "🚀 Deploying Owner Dashboard & Favorites System to Production..."

# Variables
SERVER="isaacjv@72.167.150.82"
PASSWORD="LhWOY8q!QZWz@Fqu"
REMOTE_PATH="/var/www/restaurantesmexicanos.com"
TMP_PATH="/tmp/owner_deploy"

# Step 1: Create temp directory on server
echo "📁 Creating temp directory..."
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "rm -rf $TMP_PATH && mkdir -p $TMP_PATH"

# Step 2: Upload all files to temp
echo "📤 Uploading files to temp directory..."

# Create directory structure in temp
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" << ENDSSH
mkdir -p $TMP_PATH/app/Providers/Filament
mkdir -p $TMP_PATH/app/Filament/Owner/Resources/MyRestaurantResource/Pages
mkdir -p $TMP_PATH/app/Filament/Owner/Resources/MyMenuResource/Pages
mkdir -p $TMP_PATH/app/Filament/Owner/Pages
mkdir -p $TMP_PATH/app/Filament/Owner/Widgets
mkdir -p $TMP_PATH/app/Policies
mkdir -p $TMP_PATH/app/Livewire
mkdir -p $TMP_PATH/app/Models
mkdir -p $TMP_PATH/database/migrations
mkdir -p $TMP_PATH/routes
mkdir -p $TMP_PATH/resources/views/layouts
mkdir -p $TMP_PATH/resources/views/livewire/layout
mkdir -p $TMP_PATH/resources/views/filament/owner/pages
mkdir -p $TMP_PATH/bootstrap
ENDSSH

# Upload Providers
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Providers/Filament/OwnerPanelProvider.php \
  "$SERVER:$TMP_PATH/app/Providers/Filament/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  bootstrap/providers.php \
  "$SERVER:$TMP_PATH/bootstrap/"

# Upload Owner Resources
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Resources/MyRestaurantResource.php \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Resources/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Resources/MyRestaurantResource/Pages/* \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Resources/MyRestaurantResource/Pages/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Resources/MyMenuResource.php \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Resources/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Resources/MyMenuResource/Pages/* \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Resources/MyMenuResource/Pages/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Pages/Dashboard.php \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Pages/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Filament/Owner/Widgets/* \
  "$SERVER:$TMP_PATH/app/Filament/Owner/Widgets/"

# Upload Policies
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Policies/RestaurantPolicy.php \
  "$SERVER:$TMP_PATH/app/Policies/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Policies/MenuItemPolicy.php \
  "$SERVER:$TMP_PATH/app/Policies/"

# Upload Favorites
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Livewire/FavoriteButton.php \
  "$SERVER:$TMP_PATH/app/Livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Livewire/MyFavorites.php \
  "$SERVER:$TMP_PATH/app/Livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Models/Favorite.php \
  "$SERVER:$TMP_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  resources/views/livewire/favorite-button.blade.php \
  "$SERVER:$TMP_PATH/resources/views/livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  resources/views/livewire/my-favorites.blade.php \
  "$SERVER:$TMP_PATH/resources/views/livewire/"

# Upload Updated Models
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Models/User.php \
  "$SERVER:$TMP_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Models/Restaurant.php \
  "$SERVER:$TMP_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  app/Models/Review.php \
  "$SERVER:$TMP_PATH/app/Models/"

# Upload Migrations
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  database/migrations/2025_11_28_173942_create_favorites_table.php \
  "$SERVER:$TMP_PATH/database/migrations/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  database/migrations/2025_11_28_171422_add_owner_response_to_reviews_table.php \
  "$SERVER:$TMP_PATH/database/migrations/"

# Upload Routes
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  routes/web.php \
  "$SERVER:$TMP_PATH/routes/"

# Upload Views
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  resources/views/layouts/app.blade.php \
  "$SERVER:$TMP_PATH/resources/views/layouts/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  resources/views/livewire/layout/navigation.blade.php \
  "$SERVER:$TMP_PATH/resources/views/livewire/layout/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  resources/views/filament/owner/pages/dashboard.blade.php \
  "$SERVER:$TMP_PATH/resources/views/filament/owner/pages/"

# Step 3: Move files with sudo and set permissions
echo "🔧 Moving files to production directory..."
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" << 'ENDSSH'
# Move files with sudo
sudo rsync -av /tmp/owner_deploy/ /var/www/restaurantesmexicanos.com/

# Set ownership and permissions
sudo chown -R isaacjv:isaacjv /var/www/restaurantesmexicanos.com
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/storage
sudo chmod -R 775 /var/www/restaurantesmexicanos.com/bootstrap/cache

# Run migrations
cd /var/www/restaurantesmexicanos.com
php artisan migrate --force

# Clear caches
php artisan optimize:clear

# Dump autoload
composer dump-autoload

# Restart PHP-FPM
sudo systemctl restart php-fpm

# Clean up
rm -rf /tmp/owner_deploy

echo "✅ All done!"
ENDSSH

echo ""
echo "✅ Deployment Complete!"
echo ""
echo "🎯 You can now access:"
echo "   - Owner Panel: https://www.restaurantesmexicanosfamosos.com/owner"
echo "   - My Favorites: https://www.restaurantesmexicanosfamosos.com/my-favorites"
echo ""
