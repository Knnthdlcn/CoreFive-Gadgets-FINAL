<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = App\Models\Product::take(5)->get(['product_id', 'product_name', 'image_path']);

echo "Product Data:\n";
echo "=============\n";
foreach ($products as $product) {
    echo "ID: " . $product->product_id . "\n";
    echo "Name: " . $product->product_name . "\n";
    echo "Image Path: " . ($product->image_path ?? 'NULL') . "\n";
    echo "Image URL: " . $product->image_url . "\n";
    echo "---\n";
}
