<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = App\Models\Product::all(['product_id', 'product_name', 'image_path']);

echo "All Products:\n";
echo "=============\n";
foreach ($products as $product) {
    echo "ID: " . $product->product_id . " | Name: " . $product->product_name . " | Image: " . ($product->image_path ?? 'NULL') . "\n";
}
