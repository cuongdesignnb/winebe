<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            'https://images.unsplash.com/photo-1527281483448-0382f347eb1d?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1594212699903-ec5a3eaa5033?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1516535794938-6063878f08cc?q=80&w=600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1544145945-f904253db0ad?q=80&w=600&auto=format&fit=crop',
        ];

        $assets = [];
        foreach ($images as $i => $url) {
            $assets[] = MediaAsset::create([
                'original_name' => 'sample_' . ($i + 1) . '.jpg',
                'original_path' => $url,
                'mime_type' => 'image/jpeg',
                'size_bytes' => 1024,
                'disk' => 'public',
                'title' => 'Sample Image ' . ($i + 1),
            ]);
        }

        $products = Product::all();
        foreach ($products as $product) {
            // Attach 1-3 random images
            $randomAssets = collect($assets)->random(rand(1, 3))->pluck('id')->toArray();
            $product->attachMedia($randomAssets, 'images');
        }
    }
}
