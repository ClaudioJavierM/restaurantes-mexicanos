#!/bin/bash
echo "🚀 Starting Extended City Scraping - Target: 5,000+ restaurants"
echo "Started at: $(date)"
echo "Current count: $(cd /var/www/restaurantesmexicanos.com && php artisan tinker --execute='echo \App\Models\Restaurant::count();')"
echo ""

cd /var/www/restaurantesmexicanos.com

# California - More cities
echo "📍 CALIFORNIA - Additional Cities"
php artisan scrape:restaurants --city="Oakland" --state=CA --limit=100
sleep 2
php artisan scrape:restaurants --city="Long Beach" --state=CA --limit=100
sleep 2
php artisan scrape:restaurants --city="Bakersfield" --state=CA --limit=80
sleep 2
php artisan scrape:restaurants --city="Riverside" --state=CA --limit=80
sleep 2
php artisan scrape:restaurants --city="Santa Ana" --state=CA --limit=80
sleep 2
php artisan scrape:restaurants --city="Anaheim" --state=CA --limit=80
sleep 2
php artisan scrape:restaurants --city="Stockton" --state=CA --limit=60
sleep 2
php artisan scrape:restaurants --city="Modesto" --state=CA --limit=60
sleep 2

# Texas - More cities
echo "📍 TEXAS - Additional Cities"
php artisan scrape:restaurants --city="Laredo" --state=TX --limit=100
sleep 2
php artisan scrape:restaurants --city="Brownsville" --state=TX --limit=80
sleep 2
php artisan scrape:restaurants --city="McAllen" --state=TX --limit=80
sleep 2
php artisan scrape:restaurants --city="Corpus Christi" --state=TX --limit=80
sleep 2
php artisan scrape:restaurants --city="Arlington" --state=TX --limit=60
sleep 2
php artisan scrape:restaurants --city="Plano" --state=TX --limit=60
sleep 2

# Arizona - More cities
echo "📍 ARIZONA - Additional Cities"
php artisan scrape:restaurants --city="Scottsdale" --state=AZ --limit=60
sleep 2
php artisan scrape:restaurants --city="Chandler" --state=AZ --limit=60
sleep 2
php artisan scrape:restaurants --city="Glendale" --state=AZ --limit=60
sleep 2

# Nevada - More cities
echo "📍 NEVADA - Additional Cities"
php artisan scrape:restaurants --city="Henderson" --state=NV --limit=60
sleep 2
php artisan scrape:restaurants --city="North Las Vegas" --state=NV --limit=50
sleep 2

# New Mexico - More cities
echo "📍 NEW MEXICO - Additional Cities"
php artisan scrape:restaurants --city="Santa Fe" --state=NM --limit=60
sleep 2
php artisan scrape:restaurants --city="Rio Rancho" --state=NM --limit=40
sleep 2

# Georgia
echo "📍 GEORGIA"
php artisan scrape:restaurants --city="Atlanta" --state=GA --limit=100
sleep 2

# North Carolina
echo "📍 NORTH CAROLINA"
php artisan scrape:restaurants --city="Charlotte" --state=NC --limit=80
sleep 2
php artisan scrape:restaurants --city="Raleigh" --state=NC --limit=60
sleep 2

# Tennessee
echo "📍 TENNESSEE"
php artisan scrape:restaurants --city="Nashville" --state=TN --limit=80
sleep 2
php artisan scrape:restaurants --city="Memphis" --state=TN --limit=60
sleep 2

# Oregon
echo "📍 OREGON"
php artisan scrape:restaurants --city="Portland" --state=OR --limit=80
sleep 2

# Utah
echo "📍 UTAH"
php artisan scrape:restaurants --city="Salt Lake City" --state=UT --limit=80
sleep 2

# Oklahoma
echo "📍 OKLAHOMA"
php artisan scrape:restaurants --city="Oklahoma City" --state=OK --limit=60
sleep 2

# Kansas
echo "📍 KANSAS"
php artisan scrape:restaurants --city="Wichita" --state=KS --limit=50
sleep 2

# Virginia
echo "📍 VIRGINIA"
php artisan scrape:restaurants --city="Virginia Beach" --state=VA --limit=60
sleep 2

# Massachusetts
echo "📍 MASSACHUSETTS"
php artisan scrape:restaurants --city="Boston" --state=MA --limit=60
sleep 2

echo ""
echo "🎉 Extended city scraping complete!"
echo "Finished at: $(date)"

# Final count
TOTAL=$(php artisan tinker --execute="echo \App\Models\Restaurant::count();")
echo ""
echo "📊 FINAL STATISTICS"
echo "Total restaurants in database: $TOTAL"
