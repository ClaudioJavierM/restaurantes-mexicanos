#!/bin/bash

echo "🚀 Deploying Owner Dashboard & Favorites System to Production..."

# Variables
SERVER="isaacjv@72.167.150.82"
PASSWORD="LhWOY8q!QZWz@Fqu"
REMOTE_PATH="/var/www/restaurantesmexicanos.com"
LOCAL_PATH="/Users/javier/WebsProjects/restaurantesmexicanos.com"

# Step 1: Upload Provider
echo "📤 Uploading OwnerPanelProvider..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Providers/Filament/OwnerPanelProvider.php" \
  "$SERVER:$REMOTE_PATH/app/Providers/Filament/"

# Step 2: Upload bootstrap/providers.php
echo "📤 Uploading bootstrap/providers.php..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/bootstrap/providers.php" \
  "$SERVER:$REMOTE_PATH/bootstrap/"

# Step 3: Upload Owner Resources
echo "📤 Uploading Owner Resources..."
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/app/Filament/Owner/Resources/MyRestaurantResource/Pages"
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/app/Filament/Owner/Resources/MyMenuResource/Pages"
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/app/Filament/Owner/Pages"
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/app/Filament/Owner/Widgets"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Resources/MyRestaurantResource.php" \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Resources/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Resources/MyRestaurantResource/Pages/"* \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Resources/MyRestaurantResource/Pages/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Resources/MyMenuResource.php" \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Resources/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Resources/MyMenuResource/Pages/"* \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Resources/MyMenuResource/Pages/"

# Step 4: Upload Pages & Widgets
echo "📤 Uploading Pages & Widgets..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Pages/Dashboard.php" \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Pages/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Filament/Owner/Widgets/"* \
  "$SERVER:$REMOTE_PATH/app/Filament/Owner/Widgets/"

# Step 5: Upload Policies
echo "📤 Uploading Policies..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Policies/RestaurantPolicy.php" \
  "$SERVER:$REMOTE_PATH/app/Policies/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Policies/MenuItemPolicy.php" \
  "$SERVER:$REMOTE_PATH/app/Policies/"

# Step 6: Upload Favorites System
echo "📤 Uploading Favorites System..."
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/app/Livewire"
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/resources/views/livewire"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Livewire/FavoriteButton.php" \
  "$SERVER:$REMOTE_PATH/app/Livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Livewire/MyFavorites.php" \
  "$SERVER:$REMOTE_PATH/app/Livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Models/Favorite.php" \
  "$SERVER:$REMOTE_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/resources/views/livewire/favorite-button.blade.php" \
  "$SERVER:$REMOTE_PATH/resources/views/livewire/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/resources/views/livewire/my-favorites.blade.php" \
  "$SERVER:$REMOTE_PATH/resources/views/livewire/"

# Step 7: Upload Updated Models
echo "📤 Uploading Updated Models..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Models/User.php" \
  "$SERVER:$REMOTE_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Models/Restaurant.php" \
  "$SERVER:$REMOTE_PATH/app/Models/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/app/Models/Review.php" \
  "$SERVER:$REMOTE_PATH/app/Models/"

# Step 8: Upload Migrations
echo "📤 Uploading Migrations..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/database/migrations/2025_11_28_173942_create_favorites_table.php" \
  "$SERVER:$REMOTE_PATH/database/migrations/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/database/migrations/2025_11_28_171422_add_owner_response_to_reviews_table.php" \
  "$SERVER:$REMOTE_PATH/database/migrations/"

# Step 9: Upload Updated Routes
echo "📤 Uploading Routes..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/routes/web.php" \
  "$SERVER:$REMOTE_PATH/routes/"

# Step 10: Upload Updated Views
echo "📤 Uploading Updated Navigation Views..."
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/resources/views/layouts/app.blade.php" \
  "$SERVER:$REMOTE_PATH/resources/views/layouts/"

sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/resources/views/livewire/layout/navigation.blade.php" \
  "$SERVER:$REMOTE_PATH/resources/views/livewire/layout/"

sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "mkdir -p $REMOTE_PATH/resources/views/filament/owner/pages"
sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no \
  "$LOCAL_PATH/resources/views/filament/owner/pages/dashboard.blade.php" \
  "$SERVER:$REMOTE_PATH/resources/views/filament/owner/pages/"

# Step 11: Run migrations and clear cache on production
echo "🔧 Running migrations and clearing cache on production..."
sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" << 'ENDSSH'
cd /var/www/restaurantesmexicanos.com
php artisan migrate --force
php artisan optimize:clear
composer dump-autoload
sudo chown -R isaacjv:isaacjv .
sudo chmod -R 775 storage bootstrap/cache
sudo systemctl restart php-fpm
ENDSSH

echo "✅ Deployment Complete!"
echo ""
echo "🎯 You can now access:"
echo "   - Owner Panel: https://www.restaurantesmexicanosfamosos.com/owner"
echo "   - My Favorites: https://www.restaurantesmexicanosfamosos.com/my-favorites"
echo ""
echo "🧪 Test credentials (from seeder):"
echo "   - roberto.owner@example.com / password"
echo "   - laura.owner@example.com / password"
