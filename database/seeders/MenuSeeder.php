<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $header = Menu::firstOrCreate([
            'code' => 'header',
        ], [
            'name' => 'Main Header Menu',
            'is_active' => true,
        ]);

        $items = [
            [
                'title' => 'WHISKIES',
                'url' => '/san-pham?category=whisky',
                'children' => [
                    [
                        'title' => 'Single Malt', 
                        'url' => '/san-pham?category=single-malt',
                        'children' => [
                            ['title' => 'Macallan', 'url' => '/san-pham?search=Macallan'],
                            ['title' => 'Glenfiddich', 'url' => '/san-pham?search=Glenfiddich'],
                            ['title' => 'The Balvenie', 'url' => '/san-pham?search=Balvenie'],
                        ]
                    ],
                    [
                        'title' => 'Blended Scotch', 
                        'url' => '/san-pham?category=blended',
                        'children' => [
                            ['title' => 'Johnnie Walker', 'url' => '/san-pham?search=Walker'],
                            ['title' => 'Chivas Regal', 'url' => '/san-pham?search=Chivas'],
                        ]
                    ],
                    ['title' => 'Japanese Whisky', 'url' => '/san-pham?category=japanese-whisky'],
                ]
            ],
            [
                'title' => 'COGNAC / RUM',
                'url' => '/san-pham?category=cognac',
                'children' => [
                    [
                        'title' => 'Cognac', 
                        'url' => '/san-pham?category=cognac',
                        'children' => [
                            ['title' => 'Hennessy', 'url' => '/san-pham?search=Hennessy'],
                            ['title' => 'Martell', 'url' => '/san-pham?search=Martell'],
                        ]
                    ],
                    [
                        'title' => 'Premium Rum', 
                        'url' => '/san-pham?category=rum',
                        'children' => [
                            ['title' => 'Zacapa', 'url' => '/san-pham?search=Zacapa'],
                            ['title' => 'Diplomatico', 'url' => '/san-pham?search=Diplomatico'],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'WINE & GIN',
                'url' => '/san-pham?category=wine',
                'children' => [
                    ['title' => 'Vang Đỏ (Red Wine)', 'url' => '/san-pham?category=red-wine'],
                    ['title' => 'Vang Trắng (White Wine)', 'url' => '/san-pham?category=white-wine'],
                    ['title' => 'London Dry Gin', 'url' => '/san-pham?category=gin'],
                ]
            ],
        ];

        foreach ($items as $i => $itemData) {
            $parent = MenuItem::create([
                'menu_id' => $header->id,
                'title' => $itemData['title'],
                'url' => $itemData['url'],
                'item_type' => 'custom_link',
                'sort_order' => $i,
                'is_visible' => true,
            ]);

            if (isset($itemData['children'])) {
                foreach ($itemData['children'] as $j => $childData) {
                    $child = MenuItem::create([
                        'menu_id' => $header->id,
                        'parent_id' => $parent->id,
                        'title' => $childData['title'],
                        'url' => $childData['url'],
                        'item_type' => 'custom_link',
                        'sort_order' => $j,
                        'is_visible' => true,
                    ]);

                    if (isset($childData['children'])) {
                        foreach ($childData['children'] as $k => $subChildData) {
                            MenuItem::create([
                                'menu_id' => $header->id,
                                'parent_id' => $child->id,
                                'title' => $subChildData['title'],
                                'url' => $subChildData['url'],
                                'item_type' => 'custom_link',
                                'sort_order' => $k,
                                'is_visible' => true,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
