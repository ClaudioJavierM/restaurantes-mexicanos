#!/bin/bash
echo "🚀 Starting City-Based Mexican Restaurant Scraper"
echo "Started at: $(date)"
echo ""

cd /var/www/restaurantesmexicanos.com

# California - Top cities
echo "📍 CALIFORNIA"
php artisan scrape:restaurants --city="Los Angeles" --state=CA --limit=200
sleep 2
php artisan scrape:restaurants --city="San Diego" --state=CA --limit=150
sleep 2
php artisan scrape:restaurants --city="San Francisco" --state=CA --limit=100
sleep 2
php artisan scrape:restaurants --city="San Jose" --state=CA --limit=100
sleep 2
php artisan scrape:restaurants --city="Fresno" --state=CA --limit=80
sleep 2
php artisan scrape:restaurants --city="Sacramento" --state=CA --limit=80
sleep 2

# Texas - Top cities
echo "📍 TEXAS"
php artisan scrape:restaurants --city="Houston" --state=TX --limit=200
sleep 2
php artisan scrape:restaurants --city="Dallas" --state=TX --limit=150
sleep 2
php artisan scrape:restaurants --city="San Antonio" --state=TX --limit=150
sleep 2
php artisan scrape:restaurants --city="Austin" --state=TX --limit=120
sleep 2
php artisan scrape:restaurants --city="El Paso" --state=TX --limit=100
sleep 2
php artisan scrape:restaurants --city="Fort Worth" --state=TX --limit=80
sleep 2

# Arizona
echo "📍 ARIZONA"
php artisan scrape:restaurants --city="Phoenix" --state=AZ --limit=150
sleep 2
php artisan scrape:restaurants --city="Tucson" --state=AZ --limit=100
sleep 2
php artisan scrape:restaurants --city="Mesa" --state=AZ --limit=80
sleep 2

# Illinois
echo "📍 ILLINOIS"
php artisan scrape:restaurants --city="Chicago" --state=IL --limit=200
sleep 2
php artisan scrape:restaurants --city="Aurora" --state=IL --limit=60
sleep 2

# Florida
echo "📍 FLORIDA"
php artisan scrape:restaurants --city="Miami" --state=FL --limit=150
sleep 2
php artisan scrape:restaurants --city="Orlando" --state=FL --limit=100
sleep 2
php artisan scrape:restaurants --city="Tampa" --state=FL --limit=80
sleep 2
php artisan scrape:restaurants --city="Jacksonville" --state=FL --limit=60
sleep 2

# Nevada
echo "📍 NEVADA"
php artisan scrape:restaurants --city="Las Vegas" --state=NV --limit=150
sleep 2
php artisan scrape:restaurants --city="Reno" --state=NV --limit=60
sleep 2

# Colorado
echo "📍 COLORADO"
php artisan scrape:restaurants --city="Denver" --state=CO --limit=120
sleep 2
php artisan scrape:restaurants --city="Colorado Springs" --state=CO --limit=60
sleep 2

# New York
echo "📍 NEW YORK"
php artisan scrape:restaurants --city="New York" --state=NY --limit=200
sleep 2
php artisan scrape:restaurants --city="Buffalo" --state=NY --limit=60
sleep 2

# New Mexico
echo "📍 NEW MEXICO"
php artisan scrape:restaurants --city="Albuquerque" --state=NM --limit=100
sleep 2
php artisan scrape:restaurants --city="Las Cruces" --state=NM --limit=50
sleep 2

# Washington
echo "📍 WASHINGTON"
php artisan scrape:restaurants --city="Seattle" --state=WA --limit=100
sleep 2
php artisan scrape:restaurants --city="Tacoma" --state=WA --limit=50
sleep 2

echo ""
echo "🎉 City-based scraping complete!"
echo "Finished at: $(date)"

# Final statistics
TOTAL=$(php artisan tinker --execute="echo \App\Models\Restaurant::count();")
echo ""
echo "📊 FINAL STATISTICS"
echo "Total restaurants in database: $TOTAL"
