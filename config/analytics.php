<?php
return [
    "ga4_property_id" => env("GA4_PROPERTY_ID", "511346323"),
    "ga4_service_account" => env("GA4_SERVICE_ACCOUNT_PATH", storage_path("app/gsc-service-account.json")),
    "gsc_site_url" => env("GSC_SITE_URL", "sc-domain:restaurantesmexicanosfamosos.com"),
    "cache_ttl" => env("ANALYTICS_CACHE_TTL", 1800),
];
