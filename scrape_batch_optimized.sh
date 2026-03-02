#!/bin/bash
echo "🚀 Starting optimized batch scraping..."
echo "Started at: $(date)"

cd /var/www/restaurantesmexicanos.com

# Top 5 states with highest Mexican population
echo "📍 Scraping California (500)..."
php artisan scrape:restaurants --state=CA --limit=500
echo "✅ California complete"
sleep 3

echo "📍 Scraping Texas (500)..."
php artisan scrape:restaurants --state=TX --limit=500
echo "✅ Texas complete"
sleep 3

echo "📍 Scraping Arizona (300)..."
php artisan scrape:restaurants --state=AZ --limit=300
echo "✅ Arizona complete"
sleep 3

echo "📍 Scraping Illinois (300)..."
php artisan scrape:restaurants --state=IL --limit=300
echo "✅ Illinois complete"
sleep 3

echo "📍 Scraping Florida (300)..."
php artisan scrape:restaurants --state=FL --limit=300
echo "✅ Florida complete"
sleep 3

echo "📍 Scraping New York (200)..."
php artisan scrape:restaurants --state=NY --limit=200
echo "✅ New York complete"
sleep 3

echo "📍 Scraping Nevada (200)..."
php artisan scrape:restaurants --state=NV --limit=200
echo "✅ Nevada complete"
sleep 3

echo "📍 Scraping Colorado (200)..."
php artisan scrape:restaurants --state=CO --limit=200
echo "✅ Colorado complete"

echo ""
echo "🎉 Batch scraping complete!"
echo "Finished at: $(date)"

# Final count
TOTAL=$(php artisan tinker --execute="echo \App\Models\Restaurant::count();")
echo "📊 Total restaurants in database: $TOTAL"
