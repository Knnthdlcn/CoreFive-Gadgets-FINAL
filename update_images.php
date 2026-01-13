<?php

$db = new PDO('mysql:host=127.0.0.1;dbname=eshop', 'root', '');

$updates = [
    'iPhone 15 Pro Max' => '/images/iPhone_15_Pro_Max_Blue_Titanium.jpg',
    'iPhone 15 Pro' => '/images/15 PR.jpg',
    'iPhone 15' => '/images/15.jpg',
    'Logitech G304' => '/images/LOGITECH-G304-LIGHTSPEED-WIRELES.jpg',
    'RK61 Keyboard' => '/images/RK61_-1.jpg',
    'Ergonomic Chair' => '/images/princess-chair-12.jpg',
    'Standing Desk' => '/images/product desk.jpg',
    'Desk Lamp' => '/images/Lamp.jpg',
];

foreach ($updates as $name => $img) {
    $stmt = $db->prepare('UPDATE products SET image_path = ? WHERE product_name = ?');
    $stmt->execute([$img, $name]);
    echo "Updated: $name\n";
}

echo "All images updated successfully!\n";
