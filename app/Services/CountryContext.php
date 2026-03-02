<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CountryContext
{
    protected static ?string $country = null;
    protected static ?string $domain = null;

    /**
     * Domain to country mapping
     */
    public const DOMAIN_MAP = [
        // Mexico
        'restaurantesmexicanosfamosos.com.mx' => 'MX',
        'www.restaurantesmexicanosfamosos.com.mx' => 'MX',
        // USA (default)
        'restaurantesmexicanosfamosos.com' => 'US',
        'www.restaurantesmexicanosfamosos.com' => 'US',
        'famousmexicanrestaurants.com' => 'US',
        'www.famousmexicanrestaurants.com' => 'US',
        // Local development
        'localhost' => 'US',
        '127.0.0.1' => 'US',
    ];

    /**
     * Country configuration
     */
    public const COUNTRY_CONFIG = [
        'US' => [
            'name' => 'United States',
            'name_es' => 'Estados Unidos',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'phone_prefix' => '+1',
            'phone_length' => 10,
            'locales' => ['en', 'es'],
            'default_locale' => 'es',
            'timezone' => 'America/Chicago',
            'flag' => '🇺🇸',
        ],
        'MX' => [
            'name' => 'Mexico',
            'name_es' => 'México',
            'currency' => 'MXN',
            'currency_symbol' => '$',
            'phone_prefix' => '+52',
            'phone_length' => 10,
            'locales' => ['es'],
            'default_locale' => 'es',
            'timezone' => 'America/Mexico_City',
            'flag' => '🇲🇽',
        ],
    ];

    /**
     * Initialize country context from request
     */
        /**
     * Initialize country context from request
     */
    public static function initFromRequest($request): void
    {
        $host = $request->getHost();
        self::$domain = $host;
        self::$country = self::DOMAIN_MAP[$host] ?? "US";

        // Store in config for easy access
        Config::set("app.country", self::$country);
        Config::set("app.country_config", self::COUNTRY_CONFIG[self::$country]);

        // Set locale based on DOMAIN first
        $config = self::COUNTRY_CONFIG[self::$country];
        
        // English domain = English by default
        if (str_contains($host, "famousmexicanrestaurants")) {
            $locale = "en";
        }
        // Spanish domain = Spanish by default
        elseif (str_contains($host, "restaurantesmexicanos")) {
            $locale = "es";
        }
        // Fallback to session or default
        else {
            $locale = session("locale", $config["default_locale"]);
        }

        // Force Spanish for Mexico
        if (self::$country === "MX") {
            $locale = "es";
        }

        // Validate locale is allowed for this country
        if (!in_array($locale, $config["locales"])) {
            $locale = $config["default_locale"];
        }

        App::setLocale($locale);
        session(["locale" => $locale]);
    }

    /**
     * Get current country code
     */
    public static function getCountry(): string
    {
        return self::$country ?? Config::get('app.country', 'US');
    }

    /**
     * Get current domain
     */
    public static function getDomain(): ?string
    {
        return self::$domain;
    }

    /**
     * Check if current context is USA
     */
    public static function isUSA(): bool
    {
        return self::getCountry() === 'US';
    }

    /**
     * Check if current context is Mexico
     */
    public static function isMexico(): bool
    {
        return self::getCountry() === 'MX';
    }

    /**
     * Get country configuration
     */
    public static function getConfig(?string $key = null)
    {
        $config = self::COUNTRY_CONFIG[self::getCountry()] ?? self::COUNTRY_CONFIG['US'];
        return $key ? ($config[$key] ?? null) : $config;
    }

    /**
     * Check if language switching is allowed
     */
    public static function canSwitchLanguage(): bool
    {
        $config = self::getConfig();
        return count($config['locales']) > 1;
    }

    /**
     * Get available locales for current country
     */
    public static function getAvailableLocales(): array
    {
        return self::getConfig('locales') ?? ['es'];
    }

    /**
     * Format phone number for current country
     */
    public static function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/\D/', '', $phone);
        $prefix = self::getConfig('phone_prefix');
        $length = self::getConfig('phone_length');

        if (strlen($cleaned) === $length) {
            return $prefix . $cleaned;
        }

        return $phone;
    }

    /**
     * Get the base URL for current country
     */
    public static function getBaseUrl(): string
    {
        return self::isMexico() 
            ? 'https://restaurantesmexicanosfamosos.com.mx'
            : 'https://restaurantesmexicanosfamosos.com';
    }
}
