<?php

namespace App\Helpers;

class UrlHelper
{
    /**
     * Get the URL for switching to a different language
     * Maintains the current path but changes the domain
     */
    public static function switchLanguageUrl(string $locale): string
    {
        $currentPath = request()->path();
        $currentPath = $currentPath === '/' ? '' : '/' . $currentPath;

        if ($locale === 'en') {
            return 'https://famousmexicanrestaurants.com' . $currentPath;
        }

        return 'https://restaurantesmexicanosfamosos.com' . $currentPath;
    }

    /**
     * Get the domain for the current locale
     */
    public static function getCurrentDomain(): string
    {
        return app()->getLocale() === 'en'
            ? 'famousmexicanrestaurants.com'
            : 'restaurantesmexicanosfamosos.com';
    }

    /**
     * Get the alternate domain (for the other language)
     */
    public static function getAlternateDomain(): string
    {
        return app()->getLocale() === 'en'
            ? 'restaurantesmexicanosfamosos.com'
            : 'famousmexicanrestaurants.com';
    }

    /**
     * Get the alternate locale
     */
    public static function getAlternateLocale(): string
    {
        return app()->getLocale() === 'en' ? 'es' : 'en';
    }
}
