<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Country;
use App\Models\Region;
use App\Models\Taxon;
use App\Models\Distillery;
use App\Models\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Countries
        $countries = [
            ['name' => 'Scotland', 'iso_code' => 'GB'],
            ['name' => 'France', 'iso_code' => 'FR'],
            ['name' => 'Japan', 'iso_code' => 'JP'],
            ['name' => 'United States', 'iso_code' => 'US'],
            ['name' => 'Ireland', 'iso_code' => 'IE'],
        ];

        foreach ($countries as $c) {
            Country::firstOrCreate(
                ['slug' => Str::slug($c['name'])],
                ['name' => $c['name'], 'iso_code' => $c['iso_code']]
            );
        }

        // 2. Regions (Scotland focused)
        $regions = [
            ['name' => 'Speyside', 'country_slug' => 'scotland'],
            ['name' => 'Islay', 'country_slug' => 'scotland'],
            ['name' => 'Highlands', 'country_slug' => 'scotland'],
            ['name' => 'Cognac', 'country_slug' => 'france'],
        ];

        foreach ($regions as $r) {
            $country = Country::where('slug', $r['country_slug'])->first();
            if ($country) {
                Region::firstOrCreate(
                    ['slug' => Str::slug($r['name'])],
                    ['name' => $r['name'], 'country_id' => $country->id]
                );
            }
        }

        // 3. Brands & Distilleries
        $brands = [
            'The Macallan',
            'Hennessy',
            'Yamazaki',
            'Glenfiddich',
            'Lagavulin',
            'Martell',
            'Hibiki'
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($b)],
                ['name' => $b, 'status' => 'active']
            );
            Distillery::firstOrCreate(
                ['slug' => Str::slug($b)],
                ['name' => $b . ' Distillery']
            );
        }

        // 4. Categories (Taxa)
        $categoriesTree = [
            'Whisky' => ['Single Malt', 'Blended', 'Bourbon', 'Scotch Whisky'],
            'Cognac' => ['VS', 'VSOP', 'XO'],
            'Wine' => ['Red Wine', 'White Wine', 'Champagne'],
        ];

        foreach ($categoriesTree as $parent => $children) {
            $data = ['name' => $parent, 'taxon_type' => 'category'];
            if ($parent === 'Whisky') {
                $data['description'] = "To make a Scotch whisky, two simple ingredients are all that's needed: barley (and/or other cereals), water and yeast! But creating the perfect spirit is an art form. The cereals are malted then fermented before being transformed into alcohol in a still, then matured in oak casks for a minimum of 3 years.";
                $data['content'] = '<h3>SCOTCH WHISKY</h3><p>To make a Scotch whisky, two simple ingredients are all that\'s needed: barley (and/or other cereals), water and yeast! But creating the perfect spirit is an art form.</p><h4>THE ART OF DISTILLATION</h4><p>The cereals are malted then fermented before being transformed into alcohol in a still, then matured in oak casks for a minimum of 3 years. This intricate process combines science and traditional craftsmanship.</p><h4>BLENDED OR SINGLE MALT?</h4><p>Blended scotch is made from mixing single malts with grain whiskies. Single malt comes from only one distillery, offering a pure expression of its origin and character.</p>';
            }

            $parentNode = Taxon::firstOrCreate(
                ['slug' => Str::slug($parent)],
                $data
            );

            foreach ($children as $child) {
                Taxon::firstOrCreate(
                    ['slug' => Str::slug($parent . '-' . $child)],
                    ['name' => $child, 'parent_id' => $parentNode->id, 'taxon_type' => 'category']
                );
            }
        }

        // 5. Collections
        $collections = ['Limited Edition', 'Vintage 1990s', 'Peated Mastery'];
        foreach ($collections as $col) {
            Collection::firstOrCreate(
                ['slug' => Str::slug($col)],
                ['name' => $col]
            );
        }
    }
}
