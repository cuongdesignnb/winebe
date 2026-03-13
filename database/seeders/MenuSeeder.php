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
                            [
                                'title' => 'Lựa chọn nổi bật',
                                'url' => '/san-pham?category=whisky&featured=1',
                                'children' => [
                                    ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=whisky&sort=newest'],
                                    ['title' => 'Khuyến mãi đặc biệt', 'url' => '/san-pham?category=whisky&on_sale=1'],
                                    ['title' => 'Sản phẩm giới hạn', 'url' => '/san-pham?category=whisky&tag=limited'],
                                    ['title' => 'Best Sellers', 'url' => '/san-pham?category=whisky&sort=best-seller'],
                                    ['title' => 'Xem tất cả Whisky', 'url' => '/san-pham?category=whisky'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Single Malt',
                        'url' => '/san-pham?category=single-malt',
                        'children' => [
                            [
                                'title' => 'Lựa chọn nổi bật',
                                'url' => '/san-pham?category=single-malt&featured=1',
                                'children' => [
                                    ['title' => 'Single Malt cao cấp', 'url' => '/san-pham?category=single-malt&tag=premium'],
                                    ['title' => 'Dưới 2 triệu', 'url' => '/san-pham?category=single-malt&price_max=2000000'],
                                    ['title' => 'Phiên bản đặc biệt', 'url' => '/san-pham?category=single-malt&tag=limited'],
                                    ['title' => 'Xem tất cả Single Malt', 'url' => '/san-pham?category=single-malt'],
                                ]
                            ],
                            [
                                'title' => 'Vùng Scotland',
                                'url' => '/san-pham?category=single-malt&region=scotland',
                                'children' => [
                                    ['title' => 'Speyside', 'url' => '/san-pham?category=single-malt&region=speyside'],
                                    ['title' => 'Highland', 'url' => '/san-pham?category=single-malt&region=highland'],
                                    ['title' => 'Islay', 'url' => '/san-pham?category=single-malt&region=islay'],
                                    ['title' => 'Lowland', 'url' => '/san-pham?category=single-malt&region=lowland'],
                                ]
                            ],
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=single-malt',
                                'children' => [
                                    ['title' => 'Macallan', 'url' => '/san-pham?brand=macallan'],
                                    ['title' => 'Glenfiddich', 'url' => '/san-pham?brand=glenfiddich'],
                                    ['title' => 'The Balvenie', 'url' => '/san-pham?brand=balvenie'],
                                    ['title' => 'Glenmorangie', 'url' => '/san-pham?brand=glenmorangie'],
                                    ['title' => 'Highland Park', 'url' => '/san-pham?brand=highland-park'],
                                    ['title' => 'Laphroaig', 'url' => '/san-pham?brand=laphroaig'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Blended Scotch',
                        'url' => '/san-pham?category=blended',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=blended',
                                'children' => [
                                    ['title' => 'Johnnie Walker', 'url' => '/san-pham?brand=johnnie-walker'],
                                    ['title' => 'Chivas Regal', 'url' => '/san-pham?brand=chivas'],
                                    ['title' => 'Dewar\'s', 'url' => '/san-pham?brand=dewars'],
                                    ['title' => 'Royal Salute', 'url' => '/san-pham?brand=royal-salute'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=blended'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Japanese Whisky',
                        'url' => '/san-pham?category=japanese-whisky',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=japanese-whisky',
                                'children' => [
                                    ['title' => 'Yamazaki', 'url' => '/san-pham?brand=yamazaki'],
                                    ['title' => 'Hibiki', 'url' => '/san-pham?brand=hibiki'],
                                    ['title' => 'Hakushu', 'url' => '/san-pham?brand=hakushu'],
                                    ['title' => 'Nikka', 'url' => '/san-pham?brand=nikka'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=japanese-whisky'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Bourbon & Rye',
                        'url' => '/san-pham?category=bourbon',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=bourbon',
                                'children' => [
                                    ['title' => 'Jack Daniel\'s', 'url' => '/san-pham?brand=jack-daniels'],
                                    ['title' => 'Maker\'s Mark', 'url' => '/san-pham?brand=makers-mark'],
                                    ['title' => 'Woodford Reserve', 'url' => '/san-pham?brand=woodford'],
                                    ['title' => 'Buffalo Trace', 'url' => '/san-pham?brand=buffalo-trace'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=bourbon'],
                                ]
                            ],
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
                            [
                                'title' => 'Lựa chọn nổi bật',
                                'url' => '/san-pham?category=cognac&featured=1',
                                'children' => [
                                    ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=cognac&sort=newest'],
                                    ['title' => 'Khuyến mãi', 'url' => '/san-pham?category=cognac&on_sale=1'],
                                    ['title' => 'Best Sellers', 'url' => '/san-pham?category=cognac&sort=best-seller'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Cognac',
                        'url' => '/san-pham?category=cognac',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=cognac',
                                'children' => [
                                    ['title' => 'Hennessy', 'url' => '/san-pham?brand=hennessy'],
                                    ['title' => 'Martell', 'url' => '/san-pham?brand=martell'],
                                    ['title' => 'Rémy Martin', 'url' => '/san-pham?brand=remy-martin'],
                                    ['title' => 'Courvoisier', 'url' => '/san-pham?brand=courvoisier'],
                                ]
                            ],
                            [
                                'title' => 'Phân loại',
                                'url' => '/san-pham?category=cognac',
                                'children' => [
                                    ['title' => 'VS', 'url' => '/san-pham?category=cognac&grade=vs'],
                                    ['title' => 'VSOP', 'url' => '/san-pham?category=cognac&grade=vsop'],
                                    ['title' => 'XO', 'url' => '/san-pham?category=cognac&grade=xo'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=cognac'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Premium Rum',
                        'url' => '/san-pham?category=rum',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=rum',
                                'children' => [
                                    ['title' => 'Zacapa', 'url' => '/san-pham?brand=zacapa'],
                                    ['title' => 'Diplomático', 'url' => '/san-pham?brand=diplomatico'],
                                    ['title' => 'Rhum Clément', 'url' => '/san-pham?brand=clement'],
                                ]
                            ],
                            [
                                'title' => 'Xuất xứ',
                                'url' => '/san-pham?category=rum',
                                'children' => [
                                    ['title' => 'Jamaica', 'url' => '/san-pham?category=rum&country=jamaica'],
                                    ['title' => 'Cuba', 'url' => '/san-pham?category=rum&country=cuba'],
                                    ['title' => 'Barbados', 'url' => '/san-pham?category=rum&country=barbados'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=rum'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Armagnac & Brandy',
                        'url' => '/san-pham?category=armagnac',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=armagnac',
                                'children' => [
                                    ['title' => 'Darroze', 'url' => '/san-pham?brand=darroze'],
                                    ['title' => 'Delord', 'url' => '/san-pham?brand=delord'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=armagnac'],
                                ]
                            ],
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
                            [
                                'title' => 'Lựa chọn nổi bật',
                                'url' => '/san-pham?category=wine&featured=1',
                                'children' => [
                                    ['title' => 'Sản phẩm mới', 'url' => '/san-pham?category=wine&sort=newest'],
                                    ['title' => 'Khuyến mãi', 'url' => '/san-pham?category=wine&on_sale=1'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Vang Đỏ',
                        'url' => '/san-pham?category=red-wine',
                        'children' => [
                            [
                                'title' => 'Vùng sản xuất',
                                'url' => '/san-pham?category=red-wine',
                                'children' => [
                                    ['title' => 'Bordeaux', 'url' => '/san-pham?category=red-wine&region=bordeaux'],
                                    ['title' => 'Bourgogne', 'url' => '/san-pham?category=red-wine&region=bourgogne'],
                                    ['title' => 'Tuscany', 'url' => '/san-pham?category=red-wine&region=tuscany'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=red-wine'],
                                ]
                            ],
                            [
                                'title' => 'Giống nho',
                                'url' => '/san-pham?category=red-wine',
                                'children' => [
                                    ['title' => 'Cabernet Sauvignon', 'url' => '/san-pham?category=red-wine&grape=cabernet'],
                                    ['title' => 'Merlot', 'url' => '/san-pham?category=red-wine&grape=merlot'],
                                    ['title' => 'Pinot Noir', 'url' => '/san-pham?category=red-wine&grape=pinot-noir'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Vang Trắng & Rosé',
                        'url' => '/san-pham?category=white-wine',
                        'children' => [
                            [
                                'title' => 'Loại rượu',
                                'url' => '/san-pham?category=white-wine',
                                'children' => [
                                    ['title' => 'Champagne', 'url' => '/san-pham?category=champagne'],
                                    ['title' => 'Sauvignon Blanc', 'url' => '/san-pham?category=sauvignon-blanc'],
                                    ['title' => 'Chardonnay', 'url' => '/san-pham?category=chardonnay'],
                                    ['title' => 'Rosé', 'url' => '/san-pham?category=rose'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=white-wine'],
                                ]
                            ],
                        ]
                    ],
                    [
                        'title' => 'Gin',
                        'url' => '/san-pham?category=gin',
                        'children' => [
                            [
                                'title' => 'Thương hiệu',
                                'url' => '/san-pham?category=gin',
                                'children' => [
                                    ['title' => 'Hendrick\'s', 'url' => '/san-pham?brand=hendricks'],
                                    ['title' => 'Monkey 47', 'url' => '/san-pham?brand=monkey-47'],
                                    ['title' => 'Tanqueray', 'url' => '/san-pham?brand=tanqueray'],
                                    ['title' => 'Xem tất cả', 'url' => '/san-pham?category=gin'],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
        ];

        $this->seedRecursive($header->id, $items, null, 0);
    }

    private function seedRecursive(int $menuId, array $items, ?int $parentId, int $level): void
    {
        foreach ($items as $i => $itemData) {
            $item = MenuItem::create([
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'title' => $itemData['title'],
                'url' => $itemData['url'],
                'item_type' => 'custom_link',
                'level' => $level,
                'sort_order' => $i,
                'is_visible' => true,
            ]);

            if (isset($itemData['children'])) {
                $this->seedRecursive($menuId, $itemData['children'], $item->id, $level + 1);
            }
        }
    }
}
