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

        // Clear ALL existing items (including children) to avoid duplicates and orphans
        $header->allItems()->delete();

        $items = [
            [
                'title' => 'WHISKIES',
                'url' => '/san-pham?category=whisky',
                'children' => [
                    [
                        'title' => 'Ưu đãi mới nhất',
                        'url' => '/san-pham?category=whisky&sort=newest',
                        'children' => [
                            ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=whisky&sort=newest'],
                            ['title' => 'Khuyến mãi đặc biệt', 'url' => '/san-pham?category=whisky&on_sale=1'],
                            ['title' => 'Sản phẩm giới hạn', 'url' => '/san-pham?category=whisky&tag=limited'],
                            ['title' => 'Best Sellers', 'url' => '/san-pham?category=whisky&sort=best-seller'],
                        ]
                    ],
                    [
                        'title' => 'Single Malt',
                        'url' => '/san-pham?category=single-malt',
                        'children' => [
                            ['title' => 'Macallan', 'url' => '/san-pham?brand=macallan'],
                            ['title' => 'Glenfiddich', 'url' => '/san-pham?brand=glenfiddich'],
                            ['title' => 'The Balvenie', 'url' => '/san-pham?brand=balvenie'],
                            ['title' => 'Glenmorangie', 'url' => '/san-pham?brand=glenmorangie'],
                            ['title' => 'Highland Park', 'url' => '/san-pham?brand=highland-park'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=single-malt'],
                        ]
                    ],
                    [
                        'title' => 'Blended Scotch',
                        'url' => '/san-pham?category=blended',
                        'children' => [
                            ['title' => 'Johnnie Walker', 'url' => '/san-pham?brand=johnnie-walker'],
                            ['title' => 'Chivas Regal', 'url' => '/san-pham?brand=chivas'],
                            ['title' => 'Dewar\'s', 'url' => '/san-pham?brand=dewars'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=blended'],
                        ]
                    ],
                    [
                        'title' => 'Japanese Whisky',
                        'url' => '/san-pham?category=japanese-whisky',
                        'children' => [
                            ['title' => 'Yamazaki', 'url' => '/san-pham?brand=yamazaki'],
                            ['title' => 'Hibiki', 'url' => '/san-pham?brand=hibiki'],
                            ['title' => 'Nikka', 'url' => '/san-pham?brand=nikka'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=japanese-whisky'],
                        ]
                    ],
                    [
                        'title' => 'Bourbon & Rye',
                        'url' => '/san-pham?category=bourbon',
                        'children' => [
                            ['title' => 'Jack Daniel\'s', 'url' => '/san-pham?brand=jack-daniels'],
                            ['title' => 'Maker\'s Mark', 'url' => '/san-pham?brand=makers-mark'],
                            ['title' => 'Woodford Reserve', 'url' => '/san-pham?brand=woodford'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=bourbon'],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'COGNAC / RUM',
                'url' => '/san-pham?category=cognac',
                'children' => [
                    [
                        'title' => 'Ưu đãi mới nhất',
                        'url' => '/san-pham?category=cognac&sort=newest',
                        'children' => [
                            ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=cognac&sort=newest'],
                            ['title' => 'Khuyến mãi', 'url' => '/san-pham?category=cognac&on_sale=1'],
                            ['title' => 'Best Sellers', 'url' => '/san-pham?category=cognac&sort=best-seller'],
                        ]
                    ],
                    [
                        'title' => 'Cognac',
                        'url' => '/san-pham?category=cognac',
                        'children' => [
                            ['title' => 'Hennessy', 'url' => '/san-pham?brand=hennessy'],
                            ['title' => 'Martell', 'url' => '/san-pham?brand=martell'],
                            ['title' => 'Rémy Martin', 'url' => '/san-pham?brand=remy-martin'],
                            ['title' => 'Courvoisier', 'url' => '/san-pham?brand=courvoisier'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=cognac'],
                        ]
                    ],
                    [
                        'title' => 'Premium Rum',
                        'url' => '/san-pham?category=rum',
                        'children' => [
                            ['title' => 'Zacapa', 'url' => '/san-pham?brand=zacapa'],
                            ['title' => 'Diplomático', 'url' => '/san-pham?brand=diplomatico'],
                            ['title' => 'Rhum Clément', 'url' => '/san-pham?brand=clement'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=rum'],
                        ]
                    ],
                    [
                        'title' => 'Armagnac & Brandy',
                        'url' => '/san-pham?category=armagnac',
                        'children' => [
                            ['title' => 'Darroze', 'url' => '/san-pham?brand=darroze'],
                            ['title' => 'Delord', 'url' => '/san-pham?brand=delord'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=armagnac'],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'WINE & GIN',
                'url' => '/san-pham?category=wine',
                'children' => [
                    [
                        'title' => 'Ưu đãi mới nhất',
                        'url' => '/san-pham?category=wine&sort=newest',
                        'children' => [
                            ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=wine&sort=newest'],
                            ['title' => 'Khuyến mãi', 'url' => '/san-pham?category=wine&on_sale=1'],
                        ]
                    ],
                    [
                        'title' => 'Vang Đỏ',
                        'url' => '/san-pham?category=red-wine',
                        'children' => [
                            ['title' => 'Bordeaux', 'url' => '/san-pham?category=red-wine&region=bordeaux'],
                            ['title' => 'Bourgogne', 'url' => '/san-pham?category=red-wine&region=bourgogne'],
                            ['title' => 'Vang Ý', 'url' => '/san-pham?category=red-wine&country=italy'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=red-wine'],
                        ]
                    ],
                    [
                        'title' => 'Vang Trắng & Rosé',
                        'url' => '/san-pham?category=white-wine',
                        'children' => [
                            ['title' => 'Champagne', 'url' => '/san-pham?category=champagne'],
                            ['title' => 'Sauvignon Blanc', 'url' => '/san-pham?category=sauvignon-blanc'],
                            ['title' => 'Rosé', 'url' => '/san-pham?category=rose'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=white-wine'],
                        ]
                    ],
                    [
                        'title' => 'Gin',
                        'url' => '/san-pham?category=gin',
                        'children' => [
                            ['title' => 'London Dry Gin', 'url' => '/san-pham?category=london-dry-gin'],
                            ['title' => 'Hendrick\'s', 'url' => '/san-pham?brand=hendricks'],
                            ['title' => 'Monkey 47', 'url' => '/san-pham?brand=monkey-47'],
                            ['title' => 'Xem tất cả', 'url' => '/san-pham?category=gin'],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($items as $i => $itemData) {
            $parent = MenuItem::create([
                'menu_id' => $header->id,
                'title' => $itemData['title'],
                'url' => $itemData['url'],
                'item_type' => 'custom_link',
                'level' => 0,
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
                        'level' => 1,
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
                                'level' => 2,
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
