<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class RealDataSeeder extends Seeder
{
    /**
     * Seeder con los datos reales del menú de Little Claire.
     * Crea categorías, productos, variantes y un menú activo.
     * No usa factories — los datos son fijos y provienen del PDF original.
     */
    public function run(): void
    {
        $menuData = $this->menuData();

        $menu = Menu::create([
            'name'        => 'Menú Little Claire',
            'description' => null,
            'is_active'   => true,
        ]);

        $categoryPos = 1;
        $productPos  = 1;

        foreach ($menuData as $categoryData) {
            $category = Category::create([
                'name'        => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
                'image_url'   => null,
                'is_visible'  => true,
                'position'    => $categoryPos,
            ]);

            $menu->categories()->attach($category->id, ['position' => $categoryPos]);
            $categoryPos++;

            foreach ($categoryData['products'] as $productData) {
                $product = Product::create([
                    'category_id' => $category->id,
                    'name'        => $productData['name'],
                    'description' => $productData['description'] ?? null,
                    'is_active'   => true,
                ]);

                foreach ($productData['variants'] as $i => $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'label'      => $variantData['label'],
                        'price'      => $variantData['price'],
                        'position'   => $i + 1,
                        'is_active'  => true,
                    ]);

                    if (!empty($variantData['image'])) {
                        ProductImage::create([
                            'product_variant_id' => $variant->id,
                            'image_url'          => '/product-images/' . $variantData['image'],
                            'position'           => 1,
                        ]);
                    }
                }

                $menu->products()->attach($product->id, ['position' => $productPos]);
                $productPos++;
            }
        }
    }

    private function menuData(): array
    {
        return [
            // ─────────────────────────────────────────────────
            // 1. TAZA PEQUEÑA 80 ml
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Taza Pequeña 80 ml',
                'products' => [
                    [
                        'name'        => 'Ristretto',
                        'description' => 'Mas intenso y concentrado, misma cantidad de cafe que un espresso, pero con menos agua.',
                        'variants'    => [['label' => null, 'price' => 3600, 'image' => 'Ristretto.png']],
                    ],
                    [
                        'name'        => 'Espresso',
                        'description' => 'Shot de cafe intenso, equilibrado y aroma concentrado.',
                        'variants'    => [['label' => null, 'price' => 3600, 'image' => 'Espresso.png']],
                    ],
                    [
                        'name'        => 'Lungo',
                        'description' => 'Espresso mas largo, suave y con notas mas ligeras.',
                        'variants'    => [['label' => null, 'price' => 3600, 'image' => 'Lungo.png']],
                    ],
                    [
                        'name'        => 'Macchiato',
                        'description' => 'Espresso con espuma de leche vaporizada, sutil y cremoso.',
                        'variants'    => [['label' => null, 'price' => 4500, 'image' => 'MACCHIATO.png']],
                    ],
                    [
                        'name'        => 'Doppio',
                        'description' => 'Doble espresso, mas fuerza y mas cuerpo.',
                        'variants'    => [['label' => null, 'price' => 4500, 'image' => 'Doppio.png']],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 2. CAFES — TAZA MEDIANA / TAZA GRANDE XXL
            // ─────────────────────────────────────────────────
            [
                'name'        => 'Taza Mediana 180 ml / Taza Grande XXL 500 ml',
                'description' => 'Mas del doble de ingredientes',
                'products'    => [
                    [
                        'name'        => 'Flat White / Cortado',
                        'description' => 'Doble shot de espresso con leche vaporizada, equilibrio perfecto.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 6300, 'image' => 'FLAT WHITE.png'],
                            ['label' => '500 ml', 'price' => 11700, 'image' => 'FLAT WHITE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Cafe Latte',
                        'description' => 'Shot de espresso con leche vaporizada, mas cremoso y delicado.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 5400, 'image' => 'Latte.png'],
                            ['label' => '500 ml', 'price' => 10800, 'image' => 'CAFÉ CON LECHE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Capuccino',
                        'description' => 'Shot de espresso con partes iguales de leche vaporizada y espuma. Pedilo con canela, cacao o solo.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 6300, 'image' => 'Capuccino.png'],
                            ['label' => '500 ml', 'price' => 11700, 'image' => 'CAPUCCINO XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Latte Macchiato / Lagrima',
                        'description' => 'Leche vaporizada con un toque de espresso, suave y cremoso.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 5400, 'image' => 'LATTE MACHIATTO.png'],
                            ['label' => '500 ml', 'price' => 10800, 'image' => 'LATTE MACHIATO XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Americano',
                        'description' => 'Espresso alargado con agua caliente, suave y liviano.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 5400, 'image' => 'Americano.png'],
                            ['label' => '500 ml', 'price' => 10800, 'image' => 'AMERICANO XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Cafe con Crema',
                        'description' => 'Doble espresso cubierto de crema batida, intenso y sedoso.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 6300, 'image' => 'CAFÉ CON CREMA.png'],
                            ['label' => '500 ml', 'price' => 11700],
                        ],
                    ],
                    [
                        'name'        => 'Caramel Latte',
                        'description' => 'Espresso con leche vaporizada y syrup de caramelo, dulce y aromatico.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 7200, 'image' => 'CARAMEL LATTE (Con o sin Café).png'],
                            ['label' => '500 ml', 'price' => 12600, 'image' => 'CARAMEL LATTE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Chocofelatte',
                        'description' => 'Espresso con leche vaporizada y syrup de chocolate.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 7200, 'image' => 'chocoffee latte.png'],
                            ['label' => '500 ml', 'price' => 12600, 'image' => 'CHOCOFFEE LATTE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Avellana Latte',
                        'description' => 'Espresso con leche vaporizada y syrup de avellanas, suave y ligeramente tostado.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 7200, 'image' => 'avellana latte.png'],
                            ['label' => '500 ml', 'price' => 12600, 'image' => 'AVELLANA LATTE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Blanco Latte',
                        'description' => 'Espresso con leche vaporizada y syrup de chocolate blanco, cremoso y delicado.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 7200, 'image' => 'CHOCO. BLANCO LATTE.png'],
                            ['label' => '500 ml', 'price' => 12600, 'image' => 'CHOCO. BLANCO LATTE XXL.png'],
                        ],
                    ],
                    [
                        'name'        => 'Pistacho Latte',
                        'description' => 'Espresso con leche vaporizada y syrup de pistacho, dulce, suave y unico.',
                        'variants'    => [
                            ['label' => '180 ml', 'price' => 7200],
                            ['label' => '500 ml', 'price' => 12600],
                        ],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 3. CAFES FRIOS
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Cafes Frios',
                'products' => [
                    ['name' => 'Cafe Frio',           'variants' => [['label' => null, 'price' => 5400, 'image' => 'CAFÉ FRÍO.png']]],
                    ['name' => 'Cafe Latte Frio',     'variants' => [['label' => null, 'price' => 6300]]],
                    ['name' => 'Blanco Latte Frio',   'variants' => [['label' => null, 'price' => 8100, 'image' => 'CHOCO. BLANCO LATTE FRIO.png']]],
                    ['name' => 'Avellana Latte Frio', 'variants' => [['label' => null, 'price' => 8100, 'image' => 'AVELLANA LATTE FRIO.png']]],
                    ['name' => 'Pistacho Latte Frio', 'variants' => [['label' => null, 'price' => 8100]]],
                    ['name' => 'Chocolate Latte Frio','variants' => [['label' => null, 'price' => 8100, 'image' => 'chocoffee latte frio.png']]],
                    ['name' => 'Caramel Latte Frio',  'variants' => [['label' => null, 'price' => 8100, 'image' => 'caramel latte frio.png']]],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 4. BEBIDAS
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Bebidas',
                'products' => [
                    ['name' => 'Coca Cola',         'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Coca Cola Zero',    'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Sprite',            'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Sprite Zero',       'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Fanta',             'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Agua Tonica',       'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Aquarius Manzana',  'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Aquarius Pomelo',   'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Aquarius Pera',     'variants' => [['label' => null, 'price' => 3600]]],
                    ['name' => 'Agua con o sin Gas','variants' => [['label' => null, 'price' => 3600]]],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 5. BEBIDAS CALIENTES
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Bebidas Calientes',
                'products' => [
                    ['name' => 'Mate para Compartir',  'variants' => [['label' => null, 'price' => 4500]]],
                    ['name' => 'Chocolatada',           'variants' => [['label' => null, 'price' => 6300]]],
                    ['name' => 'Chocolatada XXL 500ml', 'variants' => [['label' => null, 'price' => 11700]]],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 6. BEBIDAS HELADAS
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Bebidas Heladas',
                'products' => [
                    [
                        'name'        => 'Milkshake',
                        'description' => 'Vainilla, frutilla, chocolate, limon y menta, dulce de leche.',
                        'variants'    => [['label' => null, 'price' => 8100]],
                    ],
                    [
                        'name'        => 'Coffee Ice Cream',
                        'description' => 'Helado de vainilla, leche y 2 shots de espresso.',
                        'variants'    => [['label' => null, 'price' => 9000]],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 7. TE EN HEBRAS
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Te en Hebras',
                'products' => [
                    ['name' => 'Te Negro y Maracuya',                               'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Te Verde con Matcha, Jengibre y Cascaras de Limon', 'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Te Negro',                                           'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Te Verde con Manzanilla y Cascaras de Naranja',     'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Te Negro con Frutos Rojos, Ciruela y Chocolate',    'variants' => [['label' => null, 'price' => 5400]]],
                    [
                        'name'        => 'Blue Earl Grey',
                        'description' => 'Te negro de Ceylon con bergamota y petalos de aciano azul.',
                        'variants'    => [['label' => null, 'price' => 5400]],
                    ],
                    [
                        'name'        => 'Te Chai',
                        'description' => 'Con canela, pimienta negra, clavo, jengibre y cardamomo.',
                        'variants'    => [['label' => null, 'price' => 5400]],
                    ],
                    ['name' => 'Te Frio con Limon y Menta', 'variants' => [['label' => null, 'price' => 5400]]],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 8. JUGOS NATURALES (incluye Licuados como item especial)
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Jugos Naturales',
                'products' => [
                    ['name' => 'Exprimido de Naranja',      'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Limon, Menta y Jengibre',   'variants' => [['label' => null, 'price' => 5400]]],
                    [
                        'name'        => 'Jugo Especial',
                        'description' => 'Preguntanos que frutas tenemos hoy y te lo preparamos como mas te guste.',
                        'variants'    => [['label' => null, 'price' => 6300]],
                    ],
                    ['name' => 'Jugo de Manzana Prensada',    'variants' => [['label' => null, 'price' => 5400]]],
                    ['name' => 'Jugo de Zanahoria y Naranja', 'variants' => [['label' => null, 'price' => 6300]]],
                    [
                        'name'        => 'Limonada Little Claire',
                        'description' => 'Con helado de limon.',
                        'variants'    => [['label' => null, 'price' => 8100]],
                    ],
                    // Licuados: item especial extraido por el Blade como banner inline
                    [
                        'name'        => 'Licuados',
                        'description' => 'Hecho con frutas del dia. Consulta cuales tenemos hoy.',
                        'variants'    => [['label' => null, 'price' => 7200]],
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 9. PASTELERIA
            // ─────────────────────────────────────────────────
            [
                'name'        => 'Pasteleria',
                'description' => 'Acercate al mostrador para ver las opciones que ofrecemos hoy.',
                'products'    => [
                    ['name' => 'Budines',   'variants' => [['label' => null, 'price' => 7400]]],
                    ['name' => 'Tortas',    'variants' => [['label' => null, 'price' => 13200]]],
                    ['name' => 'Tartas',    'variants' => [['label' => null, 'price' => 10500]]],
                    ['name' => 'Croissant', 'variants' => [['label' => null, 'price' => 5100]]],
                    [
                        'name'        => 'Croissant Relleno',
                        'description' => 'De dulce de leche o frutos rojos.',
                        'variants'    => [['label' => null, 'price' => 6300]],
                    ],
                    ['name' => 'Medialuna', 'variants' => [['label' => null, 'price' => 3300]]],
                    [
                        'name'        => 'Medialuna Rellena',
                        'description' => 'De dulce de leche o frutos rojos.',
                        'variants'    => [['label' => null, 'price' => 5100]],
                    ],
                    [
                        'name'        => 'Alfajores Artesanales',
                        'description' => 'Pistacho, coco, almendra, nuez y mas.',
                        'variants'    => [['label' => null, 'price' => 7200]],
                    ],
                    [
                        'name'        => 'Muffins',
                        'description' => 'Chocolate, vainilla con chips.',
                        'variants'    => [['label' => null, 'price' => 4200]],
                    ],
                    ['name' => 'Scon Dulce', 'variants' => [['label' => null, 'price' => 6000]]],
                    [
                        'name'        => 'Tostadas con Mermelada Casera y Queso Crema',
                        'description' => 'Pan integral o blanco.',
                        'variants'    => [['label' => null, 'price' => 6000]],
                    ],
                    [
                        'name'        => 'Bagel con Mermelada Casera y Queso Crema',
                        'description' => 'Integral o blanco.',
                        'variants'    => [['label' => null, 'price' => 6000]],
                    ],
                    ['name' => 'Cookies Rellenas',                                   'variants' => [['label' => null, 'price' => 7400]]],
                    ['name' => 'Cookies',                                             'variants' => [['label' => null, 'price' => 6000]]],
                    ['name' => 'Roll de Canela, Chocolate o Manzana / Frutos Rojos', 'variants' => [['label' => null, 'price' => 6500]]],
                ],
            ],

            // ─────────────────────────────────────────────────
            // 10. SALADO
            // ─────────────────────────────────────────────────
            [
                'name'     => 'Salado',
                'products' => [
                    ['name' => 'Medialunas Rellenas de Jamon y Queso',                         'variants' => [['label' => null, 'price' => 7400]]],
                    ['name' => 'Medialuna Rellena de Lomito Ahumado y Queso',                  'variants' => [['label' => null, 'price' => 7800]]],
                    ['name' => 'Medialuna Rellena de Queso, Rucula y Tomates Secos',           'variants' => [['label' => null, 'price' => 7800]]],
                    ['name' => 'Croissant Relleno de Jamon y Queso',                           'variants' => [['label' => null, 'price' => 8700]]],
                    ['name' => 'Croissant Relleno de Lomito Ahumado y Queso',                  'variants' => [['label' => null, 'price' => 9000]]],
                    ['name' => 'Croissant Relleno de Queso, Rucula y Tomates Secos',           'variants' => [['label' => null, 'price' => 9000]]],
                    ['name' => 'Chipa de Queso Raclette',                                      'variants' => [['label' => null, 'price' => 7200]]],
                    ['name' => 'Avocado Toast',                                                'variants' => [['label' => null, 'price' => 13700]]],
                    ['name' => 'Omelette de Queso, Lomito Ahumado, Tomates Secos y Espinaca', 'variants' => [['label' => null, 'price' => 16800]]],
                    ['name' => 'Omelette de Queso, Rucula y Tomates Secos',                   'variants' => [['label' => null, 'price' => 15500]]],
                    ['name' => 'Omelette de Jamon y Queso',                                    'variants' => [['label' => null, 'price' => 15500]]],
                    ['name' => 'Bagel de Jamon y Queso',                                       'variants' => [['label' => null, 'price' => 8000]]],
                    ['name' => 'Bagel de Jamon y Queso Raclette Fundido',                      'variants' => [['label' => null, 'price' => 13500]]],
                    ['name' => 'Bagel de Jamon, Queso y Huevo',                                'variants' => [['label' => null, 'price' => 11700]]],
                    ['name' => 'Bagel de Lomito Ahumado y Queso',                              'variants' => [['label' => null, 'price' => 11400]]],
                    ['name' => 'Bagel de Lomito Ahumado y Queso Raclette Fundido',             'variants' => [['label' => null, 'price' => 14400]]],
                    ['name' => 'Bagel de Rucula, Tomates Secos y Queso Raclette Fundido',      'variants' => [['label' => null, 'price' => 14400]]],
                    ['name' => 'Tostado XXL de Jamon y Queso',                                 'variants' => [['label' => null, 'price' => 12300]]],
                    [
                        'name'        => 'Focaccia de Jamon y Queso',
                        'description' => 'Para compartir.',
                        'variants'    => [['label' => null, 'price' => 18200]],
                    ],
                    [
                        'name'        => 'Focaccia de Tomates Secos, Rucula y Queso',
                        'description' => 'Para compartir.',
                        'variants'    => [['label' => null, 'price' => 19100]],
                    ],
                    [
                        'name'        => 'Focaccia de Rucula, Jamon Crudo, Queso y Tomates Secos',
                        'description' => 'Para compartir.',
                        'variants'    => [['label' => null, 'price' => 20800]],
                    ],
                ],
            ],
        ];
    }
}
