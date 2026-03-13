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
                    ['title' => 'Single Malt', 'url' => '/san-pham?category=single-malt'],
                    ['title' => 'Blended', 'url' => '/san-pham?category=blended'],
                    ['title' => 'Scotch Whisky', 'url' => '/san-pham?category=scotch-whisky'],
                ]
            ],
            [
                'title' => 'COGNAC',
                'url' => '/san-pham?category=cognac',
                'children' => [
                    ['title' => 'VS', 'url' => '/san-pham?category=vs'],
                    ['title' => 'VSOP', 'url' => '/san-pham?category=vsop'],
                    ['title' => 'XO', 'url' => '/san-pham?category=xo'],
                ]
            ],
            [
                'title' => 'WINE',
                'url' => '/san-pham?category=wine',
                'children' => [
                    ['title' => 'Red Wine', 'url' => '/san-pham?category=red-wine'],
                    ['title' => 'White Wine', 'url' => '/san-pham?category=white-wine'],
                ]
            ],
        ];

        foreach ($items as $i => $itemData) {
            $item = MenuItem::create([
                'menu_id' => $header->id,
                'title' => $itemData['title'],
                'url' => $itemData['url'],
                'item_type' => 'custom_link',
                'sort_order' => $i,
                'is_visible' => true,
            ]);

            if (isset($itemData['children'])) {
                foreach ($itemData['children'] as $j => $childData) {
                    MenuItem::create([
                        'menu_id' => $header->id,
                        'parent_id' => $item->id,
                        'title' => $childData['title'],
                        'url' => $childData['url'],
                        'item_type' => 'custom_link',
                        'sort_order' => $j,
                        'is_visible' => true,
                    ]);
                }
            }
        }
    }
}
