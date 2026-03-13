<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Country;
use App\Models\Region;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductNote;
use App\Models\Taxon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $brands = Brand::all();
        $countries = Country::all();
        $regions = Region::all();

        if ($brands->count() === 0 || $countries->count() === 0) {
            $this->command->warn('Pleash run MasterDataSeeder first.');
            return;
        }

        $taxons = Taxon::all();

        $volumes = [700, 750, 1000, 50, 200];
        $saleModes = ['online_checkout', 'online_checkout', 'online_checkout', 'inquiry_only'];
        $productTypes = ['whisky', 'cognac', 'wine'];

        for ($i = 1; $i <= 100; $i++) {
            $brand = $brands->random();
            $country = $countries->random();
            $region = clone $regions->random();

            // Nếu Region không khớp Country, bỏ qua gán Region
            $regionId = ($region->country_id === $country->id) ? $region->id : null;

            $productType = $faker->randomElement($productTypes);
            $name = $brand->name . ' ' . $faker->words(rand(1, 3), true) . ' ' . $faker->randomElement(['Years Old', 'Reserve', 'Cask', 'Vintage']);

            $product = Product::create([
                'brand_id' => $brand->id,
                'country_id' => $country->id,
                'distillery_id' => null, // Giản lược
                'name' => ucwords($name),
                'slug' => Str::slug($name) . '-' . uniqid(),
                'product_type' => $productType,
                'sale_mode' => $faker->randomElement($saleModes),
                'is_published' => true,
                'is_featured' => $faker->boolean(20), // 20% cơ hội là Featured
                'short_desc' => $faker->sentence(10),
                'long_desc_html' => '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>',
            ]);

            // Gắn vào 1-2 Taxon ngẫu nhiên phù hợp với productType
            $filteredTaxons = $taxons->filter(function ($t) use ($productType) {
                return Str::contains(strtolower($t->slug), $productType);
            });

            if ($filteredTaxons->count() > 0) {
                $product->taxons()->attach($filteredTaxons->random(min(2, $filteredTaxons->count()))->pluck('id')->toArray());
            }

            // Tạo 1-2 Variants cho mỗi Product
            $variantCount = rand(1, 2);
            for ($v = 0; $v < $variantCount; $v++) {
                $basePrice = $faker->numberBetween(100, 5000) * 10000; // Giá từ 1 triệu tới 50 triệu
                ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(Str::random(8)),
                    'abv_percent' => $faker->randomFloat(2, 40, 60),
                    'volume_ml' => $faker->randomElement($volumes),
                    'price' => $basePrice,
                    'compare_at_price' => $faker->boolean(30) ? $basePrice * 1.2 : null,
                    'can_checkout' => true,
                ]);
            }

            // Tạo Tasting Notes
            $notes = ['aroma', 'palate', 'finish'];
            foreach ($notes as $key => $type) {
                ProductNote::create([
                    'product_id' => $product->id,
                    'note_type' => $type,
                    'content' => $faker->sentence(8),
                    'sort_order' => $key
                ]);
            }
        }
    }
}
