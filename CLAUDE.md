# Little Claire Bakery — Backend API

## Propósito

API REST para una cafetería. El frontend (React SPA, proyecto separado) consume esta API y muestra el menú como PDF al usuario final. Los administradores acceden a un panel de administración que les permite editar el menú (productos, variantes, categorías, orden), y el sistema genera el PDF actualizado automáticamente.

Laravel **no** sirve el frontend. Blade se usa únicamente para renderizar el PDF.

## Stack

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12 (API-only) |
| Frontend | React SPA (repo separado) |
| Base de datos | MySQL |
| Auth | Laravel Sanctum — cookies/sesión |
| PDF | Blade → HTML → PDF via Browsershot |

## Comandos

```bash
php artisan test                    # correr tests
php artisan migrate:fresh --seed    # resetear DB con datos reales
php artisan db:seed                 # solo seeders
php artisan optimize:clear          # limpiar caché (necesario tras cambios en config/auth)
```

## Decisiones de diseño no-obvias

- **Auth guard:** el guard `web` apunta al provider `admins` (modelo `Admin`), no a `users`. Tocar `config/auth.php` sin tenerlo en cuenta rompe la autenticación.
- **Precio en variantes:** `products` no tiene campo `price`. El precio vive en `product_variants`. Un producto con una sola variante sin `label` se comporta como precio único clásico.
- **Menús seleccionan productos, no variantes:** al agregar un producto a un menú, todas sus variantes activas aparecen en el PDF automáticamente.
- **`PUT /menus/{menu}/products/{product}` es un no-op intencional:** se mantiene para no romper el frontend; retorna 204 sin hacer nada.
- **Assets del PDF en base64:** logo, QR y SVG divisor se guardan en `public/pdf-assets/` y se convierten a base64 antes de pasarse al Blade, porque Browsershot renderiza desde un string HTML sin base URL.

## Estructura clave

```
app/Http/Controllers/   — un controller por recurso
app/Http/Requests/      — Form Requests para validación (subdirectorio por recurso)
app/Http/Resources/     — Resources para todas las respuestas de API
app/Models/             — Eloquent models
app/Services/           — GenerateMenuPdfService (buildData + handle)
public/pdf-assets/      — assets estáticos del PDF
resources/views/pdf/    — menu.blade.php (diseño del PDF, 3 páginas A4)
database/seeders/       — RealDataSeeder crea los datos reales del menú
```

## Endpoints principales

| Grupo | Rutas |
|-------|-------|
| Auth | `POST /api/admin/login`, `GET /api/admin/me`, `POST /api/admin/logout` |
| Catálogo | `/api/categories`, `/api/products`, `/api/products/{product}/variants` |
| Menús | `/api/menus`, `/api/menus/{menu}/categories`, `/api/menus/{menu}/products` |
| Otros | `/api/promotions`, `/api/contact-requests` |
| Dev only | `GET /preview-menu/{menu}`, `GET /test-menu-pdf/{menu}` |

## Fuera de alcance

- SSR / Blade UI / Inertia
- Cuentas de usuario regulares
- Órdenes y pagos
