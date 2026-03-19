# Refactor: Product Variants

**Fecha:** 2026-03-18
**Objetivo:** Soportar variantes de producto (e.g. tamaños, volúmenes) con precio propio por variante.

---

## Motivación

La tabla `products` tenía un único campo `price`, lo que impedía representar un mismo producto con múltiples opciones de precio. Por ejemplo: un café con tamaño Chico / Mediano / Grande, cada uno con su propio precio.

---

## Decisiones de diseño

| Decisión | Justificación |
|----------|--------------|
| `products.price` eliminado | El precio pasa a ser propiedad de las variantes |
| `product_variants.label` es nullable | Un producto con `label = null` y una sola variante se comporta igual que el producto de precio único anterior — compatible con el modelo mental existente |
| Los menús siguen seleccionando productos (no variantes) | Mantiene la UI simple: se agrega un producto al menú y todas sus variantes activas aparecen en el PDF automáticamente |
| `menu_products.custom_price` eliminado | Ya no tiene sentido sobreescribir el precio a nivel de menú si las variantes son la fuente de verdad del precio. Tener ambos generaría ambigüedad |
| El endpoint `PUT /menus/{menu}/products/{product}` se mantiene pero como no-op | Evita romper el frontend; en el futuro puede reutilizarse para otros campos |

---

## Archivos modificados

### Migraciones

#### `2026_02_02_111546_create_products_table.php`
- **Eliminado:** columna `price decimal(10,2)`
- Agregado comentario explicativo

#### `2026_02_02_111547_create_product_variants_table.php` *(nuevo)*
- Tabla `product_variants` con: `id`, `product_id` (FK cascadeOnDelete), `label` (nullable), `price`, `position`, `is_active`, `timestamps`

#### `2026_02_02_111551_create_menu_products_table.php`
- **Eliminado:** columna `custom_price decimal(10,2) nullable`

---

### Modelos

#### `app/Models/Product.php`
- **Eliminado:** `price` de `$fillable` y `$casts`
- **Agregado:** relación `variants(): HasMany` (ordenada por `position`)
- **Modificado:** `menus()` — eliminado `custom_price` del `withPivot`

#### `app/Models/ProductVariant.php` *(nuevo)*
- `$fillable`: `product_id`, `label`, `price`, `position`, `is_active`
- `$casts`: `price => decimal:2`, `is_active => boolean`
- Relación `product(): BelongsTo`

#### `app/Models/Menu.php`
- **Modificado:** `products()` — eliminado `custom_price` del `withPivot`

#### `app/Models/MenuProduct.php`
- **Eliminado:** `custom_price` de `$fillable` y `$casts`

---

### Factories

#### `database/factories/ProductFactory.php`
- **Eliminado:** campo `price`

#### `database/factories/ProductVariantFactory.php` *(nuevo)*
- Genera una variante sin label, precio aleatorio entre $2 y $15, `position = 1`

---

### Seeders

#### `database/seeders/ProductSeeder.php`
- **Modificado:** por cada producto creado, genera variantes de forma aleatoria:
  - `null` → una variante sin label (precio único simple)
  - Sets predefinidos → 2 o 3 variantes con label (`Chico/Mediano/Grande`, `250ml/350ml/500ml`, `Chico/Grande`)

#### `database/seeders/MenuProductSeeder.php`
- **Eliminado:** `custom_price` del `attach()`

---

### Controllers

#### `app/Http/Controllers/ProductController.php`
- **Eliminado:** `price` de las listas de campos en `index()` y `show()`
- **Agregado:** eager load de `variants` en `index()` y `show()`

#### `app/Http/Controllers/ProductVariantController.php` *(nuevo)*
- CRUD completo: `index`, `store`, `show`, `update`, `destroy`
- Anidado bajo `Product` (`/products/{product}/variants`)

#### `app/Http/Controllers/MenuProductController.php`
- **Modificado `index()`:** carga `products.variants` antes de devolver el resource
- **Modificado `store()`:** eliminado `custom_price` del `MenuProduct::create()`
- **Modificado `update()`:** eliminada la lógica de `custom_price`; retorna 204 (no-op)

---

### Requests

#### `app/Http/Requests/Product/StoreProductRequest.php`
- **Eliminada** regla `price`

#### `app/Http/Requests/Product/UpdateProductRequest.php`
- **Eliminada** regla `price`

#### `app/Http/Requests/ProductVariant/StoreProductVariantRequest.php` *(nuevo)*
- Reglas: `label` (nullable|string), `price` (required|numeric), `position` (required|integer), `is_active` (boolean)

#### `app/Http/Requests/ProductVariant/UpdateProductVariantRequest.php` *(nuevo)*
- Reglas: todas con `sometimes`, mismas que Store

#### `app/Http/Requests/MenuProduct/StoreMenuProductRequest.php`
- **Eliminada** regla `custom_price`

#### `app/Http/Requests/MenuProduct/UpdateMenuProductRequest.php`
- **Eliminadas** todas las reglas (era solo `custom_price`)

---

### Resources

#### `app/Http/Resources/MenuProductResource.php`
- **Eliminados:** campos `price` y `custom_price`
- **Agregado:** campo `variants` (array con `id`, `label`, `price`, `position`, `is_active`)

---

### Servicios

#### `app/Services/GenerateMenuPdfService.php`
- **Modificado `handle()`:**
  - Eager load cambiado de `categories.products.images` a `categories` + `products.variants` + `products.images` por separado
  - Por cada producto, se mapean sus variantes activas como `[label, price]`
  - **Eliminados:** campos `price`, `custom_price`, `final_price`
  - **Agregado:** campo `variants[]` en la estructura de datos del producto

---

### Vistas

#### `resources/views/pdf/menu.blade.php`
- **Modificado bloque de precio:**
  - Si el producto tiene una sola variante sin label → muestra el precio inline (comportamiento anterior)
  - Si el producto tiene múltiples variantes → muestra cada una como fila `label | precio`
- **Corregido:** imagen del producto ahora usa `pdf_src` (base64) con fallback a `url`
- **Agregados estilos:** `.variant-row` y `.variant-label`

---

### Rutas

#### `routes/api.php`
- **Agregada:** `Route::apiResource('products/{product}/variants', ProductVariantController::class)`

---

### Documentación

#### `Config.txt`
- Documentado que `products` ya no tiene `price`
- Agregada sección `product_variants` con todos sus campos
- Actualizada sección `menu_products` indicando la eliminación de `custom_price`
- Agregados dos puntos nuevos en `KEY CONCEPTS` sobre variantes

---

## Flujo end-to-end resultante

```
Admin crea producto
    └── Admin crea variantes del producto (label + price)

Admin configura menú
    └── Admin agrega productos al menú (sin precio override)

Admin genera PDF
    └── GenerateMenuPdfService carga products.variants
        └── Para cada producto en el menú:
            ├── 1 variante sin label  →  muestra precio único
            └── N variantes con label →  muestra lista label/precio
```

## Cómo resetear la base de datos

```bash
php artisan migrate:fresh --seed
```
