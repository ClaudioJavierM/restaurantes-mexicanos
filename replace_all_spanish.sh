#!/bin/bash
# Script to replace all hardcoded Spanish text with translation functions

cd resources/views/livewire

# home.blade.php
sed -i.bak \
  -e 's/>Explora por Categoría</>{{ __('\''app.explore_by_category'\'') }}</g' \
  -e 's/>¿Qué se te antoja hoy?</>{{ __('\''app.what_are_you_craving'\'') }}</g' \
  -e 's/ lugares/ {{ __('\''app.places'\'') }}/g' \
  -e 's/>Ver Todas las Categorías</>{{ __('\''app.view_all_categories'\'') }}</g' \
  -e 's/>⭐ DESTACADOS</>{{ __('\''app.featured_badge'\'') }}</g' \
  -e 's/>Restaurantes Populares</>{{ __('\''app.popular_restaurants'\'') }}</g' \
  -e 's/>Los favoritos de nuestra comunidad</>{{ __('\''app.community_favorites'\'') }}</g' \
  -e 's/>⭐ Destacado</>{{ __('\''app.featured'\'') }}</g' \
  -e 's/>No hay restaurantes destacados todavía</>{{ __('\''app.no_featured_yet'\'') }}</g' \
  -e 's/>¿Conoces un Gran Restaurante Mexicano?</>{{ __('\''app.cta_title'\'') }}</g' \
  -e 's/>Ayúdanos a crear el directorio más completo. ¡Comparte tus lugares favoritos con la comunidad!</>{{ __('\''app.cta_description'\'') }}</g' \
  -e 's/>Sugerir Restaurante</>{{ __('\''app.suggest_restaurant'\'') }}</g' \
  home.blade.php

echo "✓ home.blade.php updated"

