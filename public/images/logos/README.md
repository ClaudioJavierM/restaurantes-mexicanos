# Logos para Promoción Cruzada

## Especificaciones de Logos

Todos los logos deben cumplir con las siguientes especificaciones:

### Formato y Dimensiones
- **Tamaño recomendado**: 300x100px (proporción 3:1)
- **Formato**: PNG con fondo transparente o SVG
- **Peso máximo**: 50KB por logo
- **Resolución**: @2x para pantallas retina (600x200px)

### Logos Necesarios

1. **mexartcraft-logo.png**
   - Sitio: Mexican Arts and Crafts
   - URL: https://www.mexartcraft.com/
   - Color de marca: #D97706 (Amber)

2. **muebleyarte-logo.png**
   - Sitio: Mueble y Arte de Tonalá
   - URL: https://www.muebleyarte.com/
   - Color de marca: #92400E (Brown)

3. **refrimex-logo.png**
   - Sitio: Refrimex Paletería
   - URL: https://refrimexpaleteria.com/tromexpro
   - Color de marca: #1E40AF (Blue)

4. **tormex-logo.png**
   - Sitio: TorMex Pro
   - URL: https://tormexpro.com/
   - Color de marca: #EA580C (Orange)

5. **mftrailers-logo.png**
   - Sitio: MF Trailers
   - URL: https://mftrailers.com/
   - Color de marca: #DC2626 (Red)

6. **decorarmex-logo.png**
   - Sitio: DecorarMex
   - URL: https://decorarmex.com/
   - Color de marca: #C026D3 (Fuchsia)

7. **mueblesmexicanos-logo.png**
   - Sitio: Muebles Mexicanos
   - URL: https://mueblesmexicanos.com
   - Color de marca: #78350F (Brown)
   - Estado: En construcción

8. **mf-imports-logo.png**
   - Sitio: MF Imports
   - URL: https://mf-imports.com
   - Color de marca: #8B1538 (Vino)

9. **rmf-logo.png**
   - Sitio: Restaurantes Mexicanos Famosos (este sitio)
   - URL: https://restaurantesmexicanosfamosos.com
   - Color de marca: #059669 (Emerald)

## Optimización

Para optimizar los logos:

```bash
# Usando ImageMagick
convert logo.png -resize 300x100 -strip -quality 85 logo-optimized.png

# Usando pngquant
pngquant --quality=65-80 logo.png
```

## Fallback

Si un logo no está disponible, el componente mostrará automáticamente un fallback con:
- Las iniciales del sitio
- El color de marca como fondo
- Texto blanco

## Estado de Logos

- [ ] mexartcraft-logo.png
- [ ] muebleyarte-logo.png
- [ ] refrimex-logo.png
- [ ] tormex-logo.png
- [ ] mftrailers-logo.png
- [ ] decorarmex-logo.png
- [ ] mueblesmexicanos-logo.png
- [ ] mf-imports-logo.png
- [ ] rmf-logo.png
