# Little Claire Bakery — Estado actual del proyecto

_Última actualización: 2026-03-19_

---

## Visión general

Proyecto web para una cafetería.
- Panel de administración + landing pública.
- El menú se genera como PDF configurable desde el panel admin.
- Sin usuarios regulares: autenticación solo para admins.

## Stack

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12 (API-only) |
| Frontend | React SPA (proyecto separado) |
| Base de datos | MySQL |
| Auth | Laravel Sanctum (tabla `admins`) |
| PDF | Blade → HTML → PDF via Browsershot |
| Backups | Laravel Scheduler (semanal, sin tablas en DB) |

## Arquitectura

```
React SPA  <-->  Laravel 12 REST API  <-->  MySQL
```

- Laravel **no** sirve assets del frontend.
- Blade se usa **únicamente** para renderizar PDFs (y la ruta de preview en desarrollo).
- Frontend y backend separados (monorepo o 2 repos).

---

## Modelo de base de datos

### `admins`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| email | string | unique |
| password | string | |
| timestamps | | |

### `categories`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| name | string | |
| description | string | nullable |
| image_url | string | nullable |
| is_visible | bool | |
| position | int | orden global |
| timestamps | | |

### `products`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| category_id | FK → categories | nullable, nullOnDelete |
| name | string | |
| description | string | nullable |
| is_active | bool | default true |
| timestamps | | |

> **Nota:** `products` no tiene campo `price`. El precio vive en `product_variants`. Un producto puede tener una o más variantes.

### `product_variants`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| product_id | FK → products | cascadeOnDelete |
| label | string | nullable — e.g. "180 ml", "500 ml". `null` = variante única sin nombre |
| price | decimal(10,2) | |
| position | smallint | orden dentro del producto |
| is_active | bool | default true |
| timestamps | | |

### `product_images`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| product_id | FK → products | |
| image_url | string | |
| position | int | |

### `menus`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| name | string | |
| description | string | nullable |
| is_active | bool | |
| timestamps | | |

> Representa un PDF de menú, no una página.

### `menu_categories`
| Campo | Tipo | Notas |
|-------|------|-------|
| menu_id | FK → menus | |
| category_id | FK → categories | |
| position | smallint | |
| — | unique(menu_id, category_id) | |

### `menu_products`
| Campo | Tipo | Notas |
|-------|------|-------|
| menu_id | FK → menus | |
| product_id | FK → products | |
| position | smallint | |
| — | unique(menu_id, product_id) | |

> `custom_price` fue eliminado. Los precios son propiedad de `product_variants`. Los menús seleccionan productos; todas las variantes activas de un producto aparecen en el PDF.

### `promotions`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| title | string | |
| description | string | nullable |
| discount_type | enum | `percentage` \| `fixed` |
| discount_value | decimal | |
| starts_at | datetime | nullable |
| ends_at | datetime | nullable |
| is_active | bool | |
| timestamps | | |

### `promotion_products`
| Campo | Tipo | Notas |
|-------|------|-------|
| promotion_id | FK → promotions | |
| product_id | FK → products | |
| — | unique(promotion_id, product_id) | |

### `contact_requests`
| Campo | Tipo | Notas |
|-------|------|-------|
| id | PK | |
| name | string | |
| email | string | |
| phone | string | nullable |
| message | text | |
| type | enum | `general` \| `catering` |
| is_read | bool | |
| created_at | timestamp | sin updated_at |

---

## Estructura del backend (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminAuthController.php
│   │   ├── CategoryController.php
│   │   ├── ContactRequestController.php
│   │   ├── MenuCategoryController.php
│   │   ├── MenuController.php
│   │   ├── MenuProductController.php
│   │   ├── ProductController.php
│   │   ├── ProductVariantController.php
│   │   └── PromotionController.php
│   ├── Requests/
│   │   ├── Admin/
│   │   ├── Category/
│   │   ├── ContactRequest/
│   │   ├── Menu/
│   │   ├── MenuCategory/
│   │   ├── MenuProduct/
│   │   ├── Product/
│   │   ├── ProductVariant/
│   │   └── Promotion/
│   └── Resources/
│       ├── MenuCategoryResource.php
│       └── MenuProductResource.php
├── Models/
│   ├── Admin.php
│   ├── Category.php
│   ├── ContactRequest.php
│   ├── Menu.php
│   ├── MenuCategory.php
│   ├── MenuProduct.php
│   ├── Product.php
│   ├── ProductImage.php
│   ├── ProductVariant.php
│   ├── Promotion.php
│   └── PromotionProduct.php
└── Services/
    └── GenerateMenuPdfService.php
        ├── buildData(Menu): array   ← separa construcción de datos
        └── handle(Menu): array      ← genera el PDF llamando a buildData()

public/
└── pdf-assets/                      ← assets estáticos del PDF
    ├── logo-little-claire.png
    ├── qr-code.png
    └── divider-line.svg

database/
├── seeders/
│   ├── AdminSeeder.php              ← datos de prueba
│   ├── CategorySeeder.php
│   ├── ProductSeeder.php
│   ├── ...
│   └── RealDataSeeder.php           ← datos reales del menú Little Claire
└── factories/
    ├── ProductFactory.php
    └── ProductVariantFactory.php

resources/views/pdf/
└── menu.blade.php                   ← diseño del PDF (3 páginas A4)
```

---

## API Endpoints

### Auth
| Método | Ruta | Acción |
|--------|------|--------|
| POST | `/api/admin/login` | Login |
| GET | `/api/admin/me` | Admin actual (auth) |
| POST | `/api/admin/logout` | Logout (auth) |

### Catálogo
| Método | Ruta | Acción |
|--------|------|--------|
| GET/POST | `/api/categories` | Listar / Crear |
| GET/PUT/DELETE | `/api/categories/{id}` | Ver / Editar / Eliminar |
| GET/POST | `/api/products` | Listar / Crear |
| GET/PUT/DELETE | `/api/products/{id}` | Ver / Editar / Eliminar |
| GET/POST | `/api/products/{product}/variants` | Listar / Crear variantes |
| GET/PUT/DELETE | `/api/products/{product}/variants/{variant}` | Ver / Editar / Eliminar variante |

### Menús
| Método | Ruta | Acción |
|--------|------|--------|
| GET/POST | `/api/menus` | Listar / Crear |
| GET/PUT/DELETE | `/api/menus/{id}` | Ver / Editar / Eliminar |
| GET/POST | `/api/menus/{menu}/categories` | Categorías del menú |
| PUT | `/api/menus/{menu}/categories/order` | Reordenar categorías |
| DELETE | `/api/menus/{menu}/categories/{category}` | Quitar categoría |
| GET/POST | `/api/menus/{menu}/products` | Productos del menú |
| PUT | `/api/menus/{menu}/products/order` | Reordenar productos |
| DELETE | `/api/menus/{menu}/products/{product}` | Quitar producto |

### Otros
| Método | Ruta | Acción |
|--------|------|--------|
| GET/POST | `/api/promotions` | Listar / Crear |
| GET/PUT/DELETE | `/api/promotions/{id}` | Ver / Editar / Eliminar |
| POST | `/api/contact-requests` | Crear solicitud (pública) |
| GET/PUT/DELETE | `/api/contact-requests/{id}` | Ver / Editar / Eliminar (admin) |

### Utilidades (web, solo desarrollo)
| Método | Ruta | Acción |
|--------|------|--------|
| GET | `/preview-menu/{menu}` | Preview HTML del Blade del menú |
| GET | `/test-menu-pdf/{menu}` | Genera el PDF y retorna la URL |

---

## Sistema de generación de PDF

### Flujo
```
Admin dispara generación
  → GenerateMenuPdfService::handle(Menu)
      → buildData(): carga categorías + productos + variantes + assets base64
      → View::make('pdf.menu') → HTML string
      → Browsershot::html($html)->format('A4')->savePdf(...)
  → Retorna { relative_path, absolute_path, public_url, exists, size }
```

### Assets estáticos del PDF
Los assets (logo, QR, SVG divisor) se almacenan en `public/pdf-assets/` y son convertidos a base64 por `resolvePublicAsset()` antes de pasarse al Blade, dado que Browsershot renderiza desde un string HTML sin base URL.

### Blade — detección automática de layout por categoría
El Blade detecta el tipo de sección a renderizar analizando los datos de cada categoría:

| Condición detectada | Layout aplicado |
|---------------------|----------------|
| Productos con 2+ variantes etiquetadas (ej. "180 ml" / "500 ml") | `size-prices` — precios por tamaño en columna derecha |
| Todos mismos precio, sin descripción, 5+ productos | Price-box — nombre en grid + recuadro de precio único al costado |
| Todos mismos precio, con descripción en algunos | Price-box con lista espaciosa (Té en Hebras) |
| Categoría "Bebidas Calientes" | 3 columnas horizontales iguales |
| Categoría "Bebidas Heladas" | 2 columnas con descripción, sin ícono |
| Categoría "Jugos Naturales" | 2 columnas + banner inline para Licuados |
| Categoría "Pastelería" | 3 columnas con header especial (`section-flex-header`) |
| Default con ícono (página 1) | Items con cuadro ícono + nombre + descripción |
| Default sin ícono (páginas 2-3) | Items solo nombre + precio |

### Paginación del PDF
Las 10 categorías del menú real se distribuyen en 3 páginas A4:
- **Página 1** (índices 0-2): Taza Pequeña, Cafés Mediana/Grande, Cafés Fríos
- **Página 2** (índices 3-7): Bebidas, Bebidas Calientes, Bebidas Heladas, Té en Hebras, Jugos Naturales
- **Página 3** (índices 8-9): Pastelería, Salado

> La agrupación es hardcoded por índice (`array_slice`). Si se agregan categorías nuevas, revisar la distribución en el Blade.

---

## Seeders

### Datos de prueba (fake)
Los seeders originales (`CategorySeeder`, `ProductSeeder`, etc.) están **comentados** en `DatabaseSeeder` y no corren por defecto. Se pueden rehabilitar descomentando las líneas en `DatabaseSeeder::run()`.

### Datos reales — `RealDataSeeder`
Crea las 10 categorías y todos los productos del menú real de Little Claire, más el menú activo "Menú Little Claire" con todas las categorías y productos adjuntos.

**Categorías creadas (en orden de posición):**
1. Taza Pequeña 80 ml — 5 productos (precio único)
2. Taza Mediana 180 ml / Taza Grande XXL 500 ml — 11 productos con variantes de tamaño
3. Cafés Fríos — 7 productos
4. Bebidas — 10 productos ($3.600 c/u)
5. Bebidas Calientes — 3 productos (Mate, Chocolatada, Chocolatada XXL)
6. Bebidas Heladas — 2 productos
7. Té en Hebras — 8 productos ($5.400 c/u)
8. Jugos Naturales — 7 productos (incluye Licuados)
9. Pastelería — 15 productos
10. Salado — 21 productos

**Reset de datos:**
```bash
php artisan migrate:fresh --seed
```

---

## Conceptos clave

- **Catálogo ≠ Menú**: el menú es una selección explícita de categorías y productos.
- El orden en el menú se controla via tablas pivot (`position`).
- Un producto con una sola variante sin label se comporta como un producto de precio único clásico.
- **Licuados** es un producto dentro de la categoría "Jugos Naturales" pero el Blade lo extrae y renderiza como sección propia (inline banner) por su nombre.
- El PDF siempre refleja la configuración del admin.
- Solo puede haber un menú activo a la vez (lógica de aplicación).
- Los assets estáticos del PDF (logo, QR, SVG) se guardan en `public/pdf-assets/` y se pasan al Blade como base64.

## Fuera de alcance

- SSR / Next.js / Inertia
- Blade UI
- Cuentas de usuario
- Órdenes / pagos
