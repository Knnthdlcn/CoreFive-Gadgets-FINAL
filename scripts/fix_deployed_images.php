<?php

// Copy product images from storage/app/public/products -> public/images
// and update DB `products.image_path` to `images/<filename>` for deployed sites

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "Starting image migration: storage/app/public/products -> public/images\n";

$storageDir = storage_path('app/public/products');
$publicDir = public_path('images');

if (!File::exists($publicDir)) {
    File::makeDirectory($publicDir, 0755, true);
    echo "Created directory: $publicDir\n";
}

$products = DB::table('products')
    ->whereNotNull('image_path')
    ->where(function ($q) {
        $q->where('image_path', 'like', 'storage/products/%')
          ->orWhere('image_path', 'like', 'storage/%');
    })
    ->get();

if ($products->isEmpty()) {
    echo "No products found with storage-based image_path. Nothing to do.\n";
    exit(0);
}

$copied = 0;
$missing = 0;

foreach ($products as $p) {
    $imgPath = $p->image_path;
    // Normalize path and get filename
    $imgPath = str_replace('\\', '/', $imgPath);
    $filename = basename($imgPath);
    $src = $storageDir . '/' . $filename;
    $dest = $publicDir . '/' . $filename;

    if (File::exists($src)) {
        if (!File::exists($dest)) {
            File::copy($src, $dest);
            echo "Copied: $src -> images/$filename\n";
        } else {
            echo "Already exists in public: images/$filename\n";
        }

        DB::table('products')
            ->where('product_id', $p->product_id)
            ->update(['image_path' => 'images/' . $filename]);

        $copied++;
    } else {
        echo "Missing source file: $src (product_id={$p->product_id})\n";
        $missing++;
    }
}

echo "Done. Copied: $copied, Missing: $missing\n";

if ($missing > 0) {
    echo "Note: some files were missing from storage/app/public/products.\n";
    echo "If you have local images, upload them to the server or configure persistent storage (S3) to avoid data loss on deploys.\n";
}

exit(0);
