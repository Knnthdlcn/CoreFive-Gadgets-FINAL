<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update remaining products with actual local images
$updates = [
    ['id' => 7, 'name' => 'Standing Desk', 'image' => 'images/product desk.jpg'],
    ['id' => 8, 'name' => 'Desk Lamp', 'image' => 'images/Lamp.jpg'],
];

echo "Updating remaining product images...\n";
echo "====================================\n";

foreach ($updates as $update) {
    $product = App\Models\Product::find($update['id']);
    if ($product) {
        $product->image_path = $update['image'];
        $product->save();
        echo "✓ Updated: {$update['name']} -> {$update['image']}\n";
    } else {
        echo "✗ Not found: ID {$update['id']}\n";
    }
}

echo "\nVerifying all products now:\n";
echo "===========================\n";
$allProducts = App\Models\Product::all(['product_id', 'product_name', 'image_path']);
foreach ($allProducts as $p) {
    echo "ID {$p->product_id}: {$p->product_name} -> {$p->image_path}\n";
}

echo "\nDone!\n";
