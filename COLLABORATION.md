# Workflow de Colaboracion — FAMER

## Quienes somos

| Persona | GitHub | Rol |
|---------|--------|-----|
| Javier (Isaac) | `isaacjv79` | Backend, admin panel, campaigns, SEO, data pipeline |
| Claudio | `ClaudioJavierM` | Owner panel, frontend publico, UX, features de restaurante |

## Repos

| Remote | Repo | Proposito |
|--------|------|-----------|
| `production` | `isaacjv79/restaurantes-mexicanos` | Repo principal, conectado al servidor |
| `origin` | `ClaudioJavierM/restaurantes-mexicanos` | Fork de Claudio |

## Reglas de trabajo

### Javier/Isaac
- Push directo a `main` en `isaacjv79/restaurantes-mexicanos`
- Sin cambios en su workflow actual

### Claudio
- Trabaja en branch `claudio/features`
- Al inicio de cada sesion: sync con main

```bash
git checkout claudio/features
git fetch production main
git merge production/main
```

- Cuando quiere subir a produccion:

```bash
git fetch production main
git merge production/main          # resolver conflictos si hay
git push production claudio/features:main   # push a main de produccion
git push origin claudio/features            # backup en fork
```

## Areas de responsabilidad

Esto NO es un bloqueo — ambos pueden tocar cualquier archivo. Es una guia para minimizar conflictos.

### Javier/Isaac — Area principal
- `app/Filament/Resources/` — admin panel resources
- `app/Filament/Pages/` — admin pages (Dashboard, SeoAnalytics, OperationsCenter)
- `app/Filament/Widgets/` — admin widgets
- `app/Console/Commands/` — artisan commands
- `app/Jobs/` — queue jobs
- `app/Mail/` — mailables y templates de campanas
- `database/migrations/` — migraciones
- `resources/views/emails/` — templates de email

### Claudio — Area principal
- `app/Filament/Owner/` — owner panel completo (pages, resources, widgets)
- `app/Livewire/Owner/` — componentes owner Livewire
- `resources/views/livewire/owner/` — vistas owner Livewire
- `resources/views/filament/owner/` — vistas Filament owner
- `resources/views/livewire/` — componentes publicos (restaurant-detail, restaurant-list, etc.)
- `resources/views/layouts/` — layouts (app, guest, owners-public)
- `app/Livewire/` — componentes publicos (RestaurantDetail, RestaurantList, etc.)

### Archivos de ALTO RIESGO — Avisar antes de editar
Estos archivos los tocan ambos frecuentemente. Si vas a hacer cambios grandes, avisa al otro:

- `routes/web.php`
- `routes/api.php`
- `config/*.php` (especialmente services.php, stripe.php)
- `app/Models/Restaurant.php`
- `app/Models/User.php`
- `bootstrap/app.php`

## Regla del servidor

**Nunca editar archivos directamente en el servidor de produccion.**
Todos los cambios van por git. Si se necesita un hotfix urgente, hacer commit en el servidor y push inmediato.

## Deploy

El servidor (`/var/www/restaurantesmexicanosfamosos.com.mx`) tiene archivos owned by `nginx` (PHP-FPM). Para deployar:

1. Push a `isaacjv79/restaurantes-mexicanos` main
2. Usar el script PHP de deploy (porque `mfgroup` no puede escribir archivos de `nginx`):

```php
// public/deploy-update.php — se crea, ejecuta via curl, y se borra
<?php
chdir(dirname(__DIR__));
exec('git fetch origin main 2>&1');
$files = ['archivo1.php', 'archivo2.php'];
foreach ($files as $f) {
    $c = shell_exec("git show FETCH_HEAD:$f");
    if ($c) file_put_contents($f, $c);
}
array_map('unlink', glob('storage/framework/views/*.php'));
if (function_exists('opcache_reset')) opcache_reset();
```

3. Limpiar caches: `php artisan config:cache && php artisan route:cache`
