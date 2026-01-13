<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update products with actual local images
$updates = [
    ['id' => 1, 'name' => 'iPhone 15 Pro Max', 'image' => 'images/iPhone_15_Pro_Max_Blue_Titanium.jpg'],
    ['id' => 2, 'name' => 'iPhone 15 Pro', 'image' => 'images/15 PR.jpg'],
    ['id' => 3, 'name' => 'iPhone 15', 'image' => 'images/15.jpg'],
    ['id' => 4, 'name' => 'Logitech G304', 'image' => 'images/LOGITECH-G304-LIGHTSPEED-WIRELES.jpg'],
    ['id' => 5, 'name' => 'RK61 Keyboard', 'image' => 'images/RK61_-1.jpg'],
    ['id' => 6, 'name' => 'Ergonomic Chair', 'image' => 'images/princess-chair-12.jpg'],
];

echo "Updating product images...\n";
echo "==========================\n";

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

echo "\nDone!\n";
