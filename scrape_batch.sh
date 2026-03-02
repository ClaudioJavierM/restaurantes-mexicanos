#!/bin/bash

# Scraping batch script for top 10 states
# This will run sequentially to avoid overwhelming the server

echo "🚀 Starting batch scraping for top 10 states..."
echo "Started at: $(date)"

cd /var/www/restaurantesmexicanos.com

# 1. California (biggest state)
echo ""
echo "📍 Scraping California (500)..."
php artisan scrape:restaurants --state=CA --limit=500
echo "✅ California complete"

# 2. Texas
echo ""
echo "📍 Scraping Texas (500)..."
php artisan scrape:restaurants --state=TX --limit=500
echo "✅ Texas complete"

# 3. Arizona
echo ""
echo "📍 Scraping Arizona (300)..."
php artisan scrape:restaurants --state=AZ --limit=300
echo "✅ Arizona complete"

# 4. Illinois
echo ""
echo "📍 Scraping Illinois (300)..."
php artisan scrape:restaurants --state=IL --limit=300
echo "✅ Illinois complete"

# 5. Florida
echo ""
echo "📍 Scraping Florida (300)..."
php artisan scrape:restaurants --state=FL --limit=300
echo "✅ Florida complete"

# 6. New York
echo ""
echo "📍 Scraping New York (200)..."
php artisan scrape:restaurants --state=NY --limit=200
echo "✅ New York complete"

# 7. Nevada
echo ""
echo "📍 Scraping Nevada (200)..."
php artisan scrape:restaurants --state=NV --limit=200
echo "✅ Nevada complete"

# 8. Colorado
echo ""
echo "📍 Scraping Colorado (200)..."
php artisan scrape:restaurants --state=CO --limit=200
echo "✅ Colorado complete"

# 9. New Mexico
echo ""
echo "📍 Scraping New Mexico (200)..."
php artisan scrape:restaurants --state=NM --limit=200
echo "✅ New Mexico complete"

# 10. Washington
echo ""
echo "📍 Scraping Washington (200)..."
php artisan scrape:restaurants --state=WA --limit=200
echo "✅ Washington complete"

echo ""
echo "🎉 Batch scraping complete!"
echo "Finished at: $(date)"

# Show final count
TOTAL=$(php artisan tinker --execute="echo \App\Models\Restaurant::count();")
WITH_PHOTOS=$(php artisan tinker --execute="echo \App\Models\Restaurant::whereNotNull('image')->count();")
WITHOUT_PHOTOS=$(php artisan tinker --execute="echo \App\Models\Restaurant::whereNull('image')->count();")

echo ""
echo "📊 Final Statistics:"
echo "Total restaurants: $TOTAL"
echo "With photos: $WITH_PHOTOS"
echo "Without photos: $WITHOUT_PHOTOS"
