@if($canSwitchLanguage ?? true)
<div class="flex items-center">
    @if(app()->getLocale() === 'en')
        <a href="{{ \App\Helpers\UrlHelper::switchLanguageUrl('es') }}"
           hreflang="es"
           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-all text-sm font-medium"
           title="Cambiar a Español">
            <span class="text-base">🇲🇽</span>
            <span>ES</span>
        </a>
    @else
        <a href="{{ \App\Helpers\UrlHelper::switchLanguageUrl('en') }}"
           hreflang="en"
           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition-all text-sm font-medium"
           title="Switch to English">
            <span class="text-base">🇺🇸</span>
            <span>EN</span>
        </a>
    @endif
</div>
@endif
