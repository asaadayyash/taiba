<?php
// Run once: http://localhost/taiba-store/update_images.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'taiba_store');

if (session_status() === PHP_SESSION_NONE) session_start();

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $base = 'https://cdn.files.salla.network/homepage/2019323384/';

    // --- Category images ---
    $catImages = [
        'rings'     => $base . 'fabd25a7-0b84-47ca-8455-8a2703d42cd7.webp',
        'bracelets' => $base . 'be7d470d-ff02-4e49-aadc-e4bf8f6d5f24.webp',
        'necklaces' => $base . 'b6109662-b4e9-4066-a305-fec657970f2e.webp',
        'earrings'  => $base . '098e3859-26c6-47fa-b95b-0572b4feef52.webp',
        'sets'      => $base . '94992020-08ab-4a21-b2d4-a798b33dae23.webp',
        'ingots'    => $base . '75175604-d748-4512-9b0b-f67a3a44d3e5.webp',
    ];

    foreach ($catImages as $slug => $img) {
        $stmt = $pdo->prepare("UPDATE categories SET image=? WHERE slug=?");
        $stmt->execute([$img, $slug]);
        echo "<p style='color:green'>✔ Category image: $slug</p>";
    }

    // --- Product images ---
    $productImages = [
        'Classic 21K Gold Ring' => json_encode([
            $base . 'fabd25a7-0b84-47ca-8455-8a2703d42cd7.webp',
            $base . '0f75b8a6-ed07-456b-8f8d-ce28522ff4a0.webp',
        ]),
        'Twisted Gold Bracelet' => json_encode([
            $base . 'be7d470d-ff02-4e49-aadc-e4bf8f6d5f24.webp',
            $base . '1686c2bd-1c85-40cd-915b-0c15195308b7.webp',
        ]),
        'Diamond-cut Gold Necklace' => json_encode([
            $base . 'b6109662-b4e9-4066-a305-fec657970f2e.webp',
            $base . 'f91129a9-b132-49b0-8db7-2bea1026a665.webp',
        ]),
        'Pearl Drop Earrings' => json_encode([
            $base . '098e3859-26c6-47fa-b95b-0572b4feef52.webp',
            $base . 'c5760a63-660c-4466-8614-d5a947a2490c.webp',
        ]),
        'Bridal Jewelry Set' => json_encode([
            $base . '94992020-08ab-4a21-b2d4-a798b33dae23.webp',
            $base . '3557981d-63d2-42bf-b888-9a183db2030b.webp',
        ]),
        'Simple Band Ring' => json_encode([
            $base . '0f75b8a6-ed07-456b-8f8d-ce28522ff4a0.webp',
            $base . '87833948-4244-4d27-aeb3-c18684589f5d.webp',
        ]),
        'Gold Ingot 10g' => json_encode([
            $base . '75175604-d748-4512-9b0b-f67a3a44d3e5.webp',
            $base . 'c72ecce7-3d1c-4188-886c-2eeac50751de.webp',
        ]),
        'Chain Necklace 45cm' => json_encode([
            $base . 'c72ecce7-3d1c-4188-886c-2eeac50751de.webp',
            $base . '3daa6fe4-fb7a-4524-b257-312bb38bb921.webp',
        ]),
    ];

    foreach ($productImages as $name => $imgs) {
        $stmt = $pdo->prepare("UPDATE products SET images=? WHERE name=?");
        $stmt->execute([$imgs, $name]);
        echo "<p style='color:green'>✔ Product images: $name</p>";
    }

    echo "<hr><h2 style='color:#C9A840'>Images Updated!</h2>";
    echo "<p><a href='/taiba-store/' style='color:#C9A840'>← Go to Store</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
