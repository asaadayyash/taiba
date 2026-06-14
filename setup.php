<?php
// Run once: http://localhost/taiba-store/setup.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_NAME', 'taiba_store');

if (session_status() === PHP_SESSION_NONE) session_start();

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    // Users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('USER','ADMIN') DEFAULT 'USER',
        phone VARCHAR(30),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        compare_price DECIMAL(10,2),
        stock INT DEFAULT 0,
        images JSON,
        weight DECIMAL(8,2),
        material VARCHAR(100),
        featured TINYINT(1) DEFAULT 0,
        active TINYINT(1) DEFAULT 1,
        category_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    // Orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        user_id INT NOT NULL,
        status ENUM('PENDING','CONFIRMED','PROCESSING','SHIPPED','DELIVERED','CANCELLED') DEFAULT 'PENDING',
        subtotal DECIMAL(10,2) NOT NULL,
        tax DECIMAL(10,2) DEFAULT 0,
        shipping DECIMAL(10,2) DEFAULT 0,
        total DECIMAL(10,2) NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(30) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Order items
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    )");

    // Cart
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY user_product (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    echo "<p style='color:green'>✔ Tables created successfully</p>";

    // Seed categories
    $cbase = 'https://cdn.files.salla.network/homepage/2019323384/';
    $cats = [
        ['Rings',     'rings',     'Gold rings for every occasion',    $cbase.'fabd25a7-0b84-47ca-8455-8a2703d42cd7.webp'],
        ['Bracelets', 'bracelets', 'Elegant gold bracelets',           $cbase.'be7d470d-ff02-4e49-aadc-e4bf8f6d5f24.webp'],
        ['Necklaces', 'necklaces', 'Beautiful gold necklaces',         $cbase.'b6109662-b4e9-4066-a305-fec657970f2e.webp'],
        ['Earrings',  'earrings',  'Stunning gold earrings',           $cbase.'098e3859-26c6-47fa-b95b-0572b4feef52.webp'],
        ['Sets',      'sets',      'Complete jewelry sets',            $cbase.'94992020-08ab-4a21-b2d4-a798b33dae23.webp'],
        ['Ingots',    'ingots',    'Gold ingots and bullion',          $cbase.'75175604-d748-4512-9b0b-f67a3a44d3e5.webp'],
    ];
    $catIds = [];
    foreach ($cats as [$name, $slug, $desc, $img]) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, description, image) VALUES (?,?,?,?)");
        $stmt->execute([$name, $slug, $desc, $img]);
        $row = $pdo->query("SELECT id FROM categories WHERE slug='$slug'")->fetch();
        $catIds[$slug] = $row['id'];
        echo "<p style='color:green'>✔ Category: $name</p>";
    }

    // Seed products
    $base = 'https://cdn.files.salla.network/homepage/2019323384/';
    $products = [
        ['Classic 21K Gold Ring',     'A timeless ring crafted from 21K gold, perfect for everyday elegance.',        850,  1000, 15, '21K Gold', 4.5,  1, $catIds['rings'],     json_encode([$base.'fabd25a7-0b84-47ca-8455-8a2703d42cd7.webp', $base.'0f75b8a6-ed07-456b-8f8d-ce28522ff4a0.webp'])],
        ['Twisted Gold Bracelet',     'Elegant twisted design in 18K gold, a statement piece for any occasion.',      1200, null, 8,  '18K Gold', 8.2,  1, $catIds['bracelets'], json_encode([$base.'be7d470d-ff02-4e49-aadc-e4bf8f6d5f24.webp', $base.'1686c2bd-1c85-40cd-915b-0c15195308b7.webp'])],
        ['Diamond-cut Gold Necklace', 'Stunning diamond-cut 21K gold necklace that catches every light.',             2500, 3000, 5,  '21K Gold', 12.0, 1, $catIds['necklaces'], json_encode([$base.'b6109662-b4e9-4066-a305-fec657970f2e.webp', $base.'f91129a9-b132-49b0-8db7-2bea1026a665.webp'])],
        ['Pearl Drop Earrings',       '18K gold with freshwater pearls for a touch of classic sophistication.',       650,  null, 20, '18K Gold', 3.1,  0, $catIds['earrings'],  json_encode([$base.'098e3859-26c6-47fa-b95b-0572b4feef52.webp', $base.'c5760a63-660c-4466-8614-d5a947a2490c.webp'])],
        ['Bridal Jewelry Set',        'Complete 21K gold bridal set including necklace, bracelet, ring and earrings.',8500, 10000,3,  '21K Gold', 45.0, 1, $catIds['sets'],      json_encode([$base.'94992020-08ab-4a21-b2d4-a798b33dae23.webp', $base.'3557981d-63d2-42bf-b888-9a183db2030b.webp'])],
        ['Simple Band Ring',          'Minimalist polished 18K gold band, timeless and versatile.',                   420,  null, 30, '18K Gold', 2.8,  0, $catIds['rings'],     json_encode([$base.'0f75b8a6-ed07-456b-8f8d-ce28522ff4a0.webp', $base.'87833948-4244-4d27-aeb3-c18684589f5d.webp'])],
        ['Gold Ingot 10g',            'Certified 24K gold ingot, 10 grams. A solid investment in pure gold.',        3200, null, 10, '24K Gold', 10.0, 0, $catIds['ingots'],    json_encode([$base.'75175604-d748-4512-9b0b-f67a3a44d3e5.webp', $base.'c72ecce7-3d1c-4188-886c-2eeac50751de.webp'])],
        ['Chain Necklace 45cm',       'Cuban link chain in 21K gold, 45cm length. Bold and luxurious.',              1800, null, 12, '21K Gold', 9.5,  1, $catIds['necklaces'], json_encode([$base.'c72ecce7-3d1c-4188-886c-2eeac50751de.webp', $base.'3daa6fe4-fb7a-4524-b257-312bb38bb921.webp'])],
    ];
    foreach ($products as [$n,$d,$p,$cp,$st,$mat,$w,$feat,$cid,$imgs]) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO products (name,description,price,compare_price,stock,material,weight,featured,category_id,images) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$n,$d,$p,$cp,$st,$mat,$w,$feat,$cid,$imgs]);
        echo "<p style='color:green'>✔ Product: $n</p>";
    }

    // Seed admin user
    $adminPw = password_hash('admin123', PASSWORD_BCRYPT, ['cost'=>12]);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name,email,password,role) VALUES (?,?,?,?)");
    $stmt->execute(['Admin','admin@taibagold.com',$adminPw,'ADMIN']);
    echo "<p style='color:green'>✔ Admin user created</p>";

    // Demo user
    $userPw = password_hash('user1234', PASSWORD_BCRYPT, ['cost'=>12]);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name,email,password,phone) VALUES (?,?,?,?)");
    $stmt->execute(['Demo Customer','demo@taibagold.com',$userPw,'+966551234567']);
    echo "<p style='color:green'>✔ Demo user created</p>";

    echo "<hr><h2 style='color:#C9A840'>Setup Complete!</h2>";
    echo "<p><strong>Admin:</strong> admin@taibagold.com / admin123</p>";
    echo "<p><strong>User:</strong> demo@taibagold.com / user1234</p>";
    echo "<p><a href='/taiba-store/' style='color:#C9A840'>Go to Store →</a></p>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
