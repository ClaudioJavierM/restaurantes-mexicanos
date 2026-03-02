#!/usr/bin/env python3
"""
Script to replace all hardcoded Spanish text with translation functions
"""

import os
import re

# Map of Spanish text -> translation key
TRANSLATIONS = {
    # Home page
    'Explora por Categoría': 'app.explore_by_category',
    '¿Qué se te antoja hoy?': 'app.what_are_you_craving',
    'lugares': 'app.places',
    'Ver Todas las Categorías': 'app.view_all_categories',
    'Restaurantes Populares': 'app.popular_restaurants',
    'Los favoritos de nuestra comunidad': 'app.community_favorites',
    '⭐ Destacado': 'app.featured_badge',
    'Destacado': 'app.featured',
    '¿Conoces un Gran Restaurante Mexicano?': 'app.cta_title',
    'Ayúdanos a crear el directorio más completo. ¡Comparte tus lugares favoritos con la comunidad!': 'app.cta_description',
    'Sugerir Restaurante': 'app.suggest_restaurant',

    # Restaurant list/detail
    'resultados': 'app.results',
    'resultado': 'app.result',
    'Filtros Avanzados': 'app.advanced_filters',
    'Limpiar Filtros': 'app.clear_filters',
    'Aplicar Filtros': 'app.apply_filters',
    'Sin resultados': 'app.no_results',
    'No encontramos restaurantes': 'app.no_restaurants_found',
    'Intenta con otros filtros': 'app.try_different_filters',

    # Restaurant detail
    'Acerca de': 'app.about',
    'Horario': 'app.hours',
    'Menú': 'app.menu',
    'Reseñas': 'app.reviews',
    'Ubicación': 'app.location',
    'Información de Contacto': 'app.contact_info',
    'Teléfono': 'app.phone',
    'Sitio Web': 'app.website',
    'Dirección': 'app.address',
    'Compartir': 'app.share',

    # Footer
    'El directorio más completo de restaurantes mexicanos auténticos en Estados Unidos. Encuentra los mejores sabores de México cerca de ti. ¡Buen provecho! 🌮': 'app.footer_about',
    'Enlaces Rápidos': 'app.footer_quick_links',
    'Nuestros Negocios': 'app.footer_our_businesses',
    'Todos los Restaurantes': 'app.all_restaurants',
    'Admin': 'app.admin',
    'Hecho con ❤️ para la comunidad mexicana en USA.': 'app.footer_copyright',

    # Filters
    'Precio': 'app.price',
    'Cualquiera': 'app.any',
    'Económico': 'app.budget',
    'Moderado': 'app.moderate',
    'Caro': 'app.expensive',
    'Tipo de Comida': 'app.food_type',
    'Características': 'app.features',
    'Para llevar': 'app.takeout',
    'Entrega': 'app.delivery',
    'Reservaciones': 'app.reservations',
    'Estacionamiento': 'app.parking',
    'WiFi': 'app.wifi',
    'Bar completo': 'app.full_bar',
    'Terraza': 'app.outdoor_seating',
    'Música en vivo': 'app.live_music',
    'Apto para niños': 'app.kid_friendly',
    'Accesible': 'app.wheelchair_accessible',

    # Menu
    'Ver Menú Completo': 'app.view_full_menu',
    'Cerrar': 'app.close',
    'Descripción': 'app.description',
    'Alergias': 'app.allergies',
    'Picante': 'app.spicy',
    'Vegetariano': 'app.vegetarian',
    'Vegano': 'app.vegan',
    'Sin Gluten': 'app.gluten_free',

    # Reviews
    'Escribe tu reseña': 'app.write_your_review',
    'Tu opinión': 'app.your_opinion',
    'Enviar Reseña': 'app.submit_review',
    'personas encontraron esto útil': 'app.people_found_helpful',
    'persona encontró esto útil': 'app.person_found_helpful',
    '¿Te resultó útil?': 'app.was_this_helpful',
    'Sí': 'app.yes',
    'No': 'app.no',

    # Common
    'Ver más': 'app.see_more',
    'Ver menos': 'app.see_less',
    'Cargar más': 'app.load_more',
    'Buscar': 'app.search',
    'Buscar restaurantes': 'app.search_restaurants',
    'Inicio': 'app.home',
}

def replace_in_file(filepath):
    """Replace hardcoded Spanish text with translation functions"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        original_content = content
        modified = False

        for spanish, key in TRANSLATIONS.items():
            # Escape special regex characters
            escaped_spanish = re.escape(spanish)

            # Replace patterns like: >Spanish text</
            pattern1 = f'>{escaped_spanish}<'
            replacement1 = f">{{{{ __('{key}') }}}}<"
            if re.search(pattern1, content):
                content = re.sub(pattern1, replacement1, content)
                modified = True

            # Replace patterns like: "Spanish text"
            pattern2 = f'"{escaped_spanish}"'
            replacement2 = f'"{{{{ __(\\'{key}\\') }}}}"'
            if re.search(pattern2, content):
                content = re.sub(pattern2, replacement2, content)
                modified = True

            # Replace patterns like: 'Spanish text'
            pattern3 = f"'{escaped_spanish}'"
            replacement3 = f"'{{{{ __(\\'{key}\\') }}}}'"
            if re.search(pattern3, content):
                content = re.sub(pattern3, replacement3, content)
                modified = True

        if modified and content != original_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"✓ Updated: {filepath}")
            return True
        else:
            print(f"  No changes: {filepath}")
            return False

    except Exception as e:
        print(f"✗ Error in {filepath}: {e}")
        return False

def main():
    """Process all Livewire view files"""
    base_path = 'resources/views'

    files_to_process = [
        'livewire/home.blade.php',
        'livewire/restaurant-list.blade.php',
        'livewire/restaurant-detail.blade.php',
        'livewire/suggestion-form.blade.php',
        'livewire/review-list.blade.php',
        'livewire/review-form.blade.php',
        'livewire/partials/advanced-filters.blade.php',
        'livewire/partials/restaurant-menu.blade.php',
        'livewire/partials/menu-item-modal.blade.php',
        'livewire/partials/restaurant-advanced-badges.blade.php',
        'layouts/app.blade.php',
    ]

    total_updated = 0
    for file_path in files_to_process:
        full_path = os.path.join(base_path, file_path)
        if os.path.exists(full_path):
            if replace_in_file(full_path):
                total_updated += 1
        else:
            print(f"  File not found: {full_path}")

    print(f"\n{'='*60}")
    print(f"Total files updated: {total_updated}")
    print(f"{'='*60}")

if __name__ == '__main__':
    main()
