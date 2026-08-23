<?php
/**
 * Database Seeder for 'cloths' database
 * Run via browser or CLI: C:\xampp\php\php.exe database/seeder.php
 */

require_once dirname(__DIR__) . '/config/database.php';

if (!$mysqli) {
  die("Error: Could not connect to MySQL database 'cloths'. Please ensure MySQL is running.\n");
}

echo "Starting Seeder for 'cloths' database...\n";

// Ensure tables exist by running schema if needed
$checkTable = $mysqli->query("SHOW TABLES LIKE 'products'");
if ($checkTable && $checkTable->num_rows === 0) {
  $schemaSql = file_get_contents(BASE_PATH . '/database/schema.sql');
  $mysqli->multi_query($schemaSql);
  while ($mysqli->more_results() && $mysqli->next_result()) {;}
}

// Clear existing product records to reseed clean demo catalog
$mysqli->query("DELETE FROM order_items");
$mysqli->query("DELETE FROM cart_items");
$mysqli->query("DELETE FROM reviews");
$mysqli->query("DELETE FROM product_images");
$mysqli->query("DELETE FROM product_colors");
$mysqli->query("DELETE FROM product_sizes");
$mysqli->query("DELETE FROM products");

$sampleProducts = [
  // 1. Women: Designer Anarkali Suit Set (With Salwar & Dupatta)
  [
    'name' => 'Embroidered Georgette Anarkali Suit Set with Dupatta & Salwar',
    'slug' => 'embroidered-georgette-anarkali-suit-set',
    'sku' => 'AUR-WOM-ST-001',
    'brand' => 'AURA & CO.',
    'gender' => 'women',
    'description' => 'A breathtaking festive unstitched & readymade Anarkali suit set in premium flowy georgette with zari and thread work on the yoke and hemline. Accompanied by a matching santoon salwar bottom and a lightweight embroidered organza dupatta with scalloped borders.',
    'features' => [
      'Fabric: Heavy Faux Georgette with micro-cotton lining',
      'Bottom: Pure Santoon Salwar / Churidar Fabric (2.5M)',
      'Dupatta: Organza Silk with Zari embroidery & scalloped borders (2.25M)',
      'Work: Handwork Sequins, Zari & Thread floral motifs',
      'Occasion: Weddings, Festive & Evening wear'
    ],
    'material' => 'Pure Georgette with Santoon Bottom & Organza Dupatta',
    'care_instructions' => 'Dry clean only. Steam iron on reverse side.',
    'price' => 3499.00,
    'original_price' => 5999.00,
    'discount_percent' => 42,
    'category_id' => 1, // Women
    'subcategory_id' => 10, // Suits & Salwars
    'image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&h=900&fit=crop',
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&h=900&fit=crop', 'label' => 'Main Front View', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&h=900&fit=crop', 'label' => 'Salwar / Bottom View', 'primary' => 0],
      ['url' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=700&h=900&fit=crop', 'label' => 'Dupatta & Scarf View', 'primary' => 0],
      ['url' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=700&h=900&fit=crop', 'label' => 'Back View & Flare', 'primary' => 0],
      ['url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=700&h=900&fit=crop', 'label' => 'Fabric & Embroidery Detail', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Pure White', 'code' => '#FFFFFF'],
      ['name' => 'Baby Pink', 'code' => '#FFB6C1'],
      ['name' => 'Deep Purple', 'code' => '#800080'],
      ['name' => 'Midnight Black', 'code' => '#1A1A1A'],
      ['name' => 'Maroon', 'code' => '#800000'],
    ],
    'sizes' => [
      ['size' => 'Unstitched / Free Size', 'stock' => 15, 'sku' => 'AUR-ST-001-FS'],
      ['size' => 'S', 'stock' => 10, 'sku' => 'AUR-ST-001-S'],
      ['size' => 'M', 'stock' => 20, 'sku' => 'AUR-ST-001-M'],
      ['size' => 'L', 'stock' => 18, 'sku' => 'AUR-ST-001-L'],
      ['size' => 'XL', 'stock' => 12, 'sku' => 'AUR-ST-001-XL'],
      ['size' => 'XXL', 'stock' => 8, 'sku' => 'AUR-ST-001-XXL'],
    ]
  ],

  // 2. Women: Designer Banarasi Silk Saree
  [
    'name' => 'Handwoven Royal Banarasi Silk Zari Saree',
    'slug' => 'handwoven-royal-banarasi-silk-saree',
    'sku' => 'AUR-WOM-SAR-002',
    'brand' => 'AURA & CO.',
    'gender' => 'women',
    'description' => 'A timeless Banarasi Katan silk saree woven by master artisans in Varanasi. Rich golden zari work adorned across floral jaal body with heavy pallu and matching unstitched blouse piece.',
    'features' => [
      'Length: 5.5 Metres Saree + 0.8 Metre Blouse piece',
      'Weave: Traditional Kadhwa technique',
      'Border: 4-inch Intricate Temple Zari Border',
      'Silk Mark Certified Pure Silk'
    ],
    'material' => '100% Pure Katan Silk with Golden Zari',
    'care_instructions' => 'Strictly dry clean. Store in muslin cloth.',
    'price' => 6999.00,
    'original_price' => 9999.00,
    'discount_percent' => 30,
    'category_id' => 1,
    'subcategory_id' => 12, // Sarees
    'image' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=700&h=900&fit=crop',
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?w=700&h=900&fit=crop', 'label' => 'Main Saree View', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&h=900&fit=crop', 'label' => 'Pallu & Zari Detail', 'primary' => 0],
      ['url' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=700&h=900&fit=crop', 'label' => 'Blouse Fabric Piece', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Royal Maroon', 'code' => '#800000'],
      ['name' => 'Emerald Green', 'code' => '#047857'],
      ['name' => 'Deep Navy', 'code' => '#000080'],
      ['name' => 'Magenta Pink', 'code' => '#BE185D'],
    ],
    'sizes' => [
      ['size' => 'Free Size (6.3M)', 'stock' => 25, 'sku' => 'AUR-SAR-002-FS'],
    ]
  ],

  // 3. Men: Raw Silk Kurta Pyjama & Nehru Jacket Set
  [
    'name' => 'Royal Silk Kurta & Pyjama Set with Embroidered Nehru Jacket',
    'slug' => 'royal-silk-kurta-pyjama-nehru-jacket-set',
    'sku' => 'AUR-MEN-KRT-003',
    'brand' => 'AURA & CO.',
    'gender' => 'men',
    'description' => 'A classic 3-piece celebratory set comprising a mandarin collar silk kurta, churidar pyjama, and a tailored floral jacquard waistcoat. Designed for groom-wear, festivals, and grand occasions.',
    'features' => [
      '3-Piece Set: Kurta + Churidar + Nehru Waistcoat',
      'Mandarin Collar with metallic buttons',
      'Dual side pockets on kurta & waistcoat',
      'Elasticated comfortable waistband on churidar'
    ],
    'material' => 'Art Silk & Jacquard Blend',
    'care_instructions' => 'Dry clean recommended. Iron on low heat.',
    'price' => 3999.00,
    'original_price' => 6499.00,
    'discount_percent' => 38,
    'category_id' => 2, // Men
    'subcategory_id' => 20, // Kurta Sets & Sherwanis
    'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=700&h=900&fit=crop',
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=700&h=900&fit=crop', 'label' => 'Full Set View', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=700&h=900&fit=crop', 'label' => 'Nehru Jacket Detail', 'primary' => 0],
      ['url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=700&h=900&fit=crop', 'label' => 'Kurta & Pyjama Back', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Ivory White', 'code' => '#FFFFF0'],
      ['name' => 'Royal Blue', 'code' => '#4169E1'],
      ['name' => 'Wine Maroon', 'code' => '#581845'],
      ['name' => 'Jet Black', 'code' => '#111827'],
    ],
    'sizes' => [
      ['size' => '38 (S)', 'stock' => 10, 'sku' => 'AUR-MEN-38'],
      ['size' => '40 (M)', 'stock' => 18, 'sku' => 'AUR-MEN-40'],
      ['size' => '42 (L)', 'stock' => 16, 'sku' => 'AUR-MEN-42'],
      ['size' => '44 (XL)', 'stock' => 10, 'sku' => 'AUR-MEN-44'],
      ['size' => '46 (XXL)', 'stock' => 5, 'sku' => 'AUR-MEN-46'],
    ]
  ],

  // 4. Men: Premium Casual Linen Shirt
  [
    'name' => 'Pure Linen Breathable Relaxed Casual Shirt',
    'slug' => 'pure-linen-breathable-relaxed-casual-shirt',
    'sku' => 'AUR-MEN-SHT-004',
    'brand' => 'AURA & CO.',
    'gender' => 'men',
    'description' => 'Tailored from 100% French flax linen, this relaxed-fit long-sleeve shirt offers effortless luxury and all-day cooling comfort.',
    'features' => ['100% European Flax Linen', 'Button-down spread collar', 'Mother-of-pearl buttons', 'Pre-washed for soft feel'],
    'material' => '100% Pure Linen',
    'care_instructions' => 'Machine wash gentle. Hang dry in shade.',
    'price' => 2199.00,
    'original_price' => 2999.00,
    'discount_percent' => 26,
    'category_id' => 2,
    'subcategory_id' => 21, // Shirts
    'image' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=700&h=900&fit=crop',
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=700&h=900&fit=crop', 'label' => 'Front Shirt View', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=700&h=900&fit=crop', 'label' => 'Collar & Fabric', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Sky Blue', 'code' => '#87CEEB'],
      ['name' => 'White', 'code' => '#FFFFFF'],
      ['name' => 'Sage Olive', 'code' => '#84A98C'],
      ['name' => 'Dusty Pink', 'code' => '#DDA15E'],
    ],
    'sizes' => [
      ['size' => 'S', 'stock' => 8, 'sku' => 'AUR-LIN-S'],
      ['size' => 'M', 'stock' => 15, 'sku' => 'AUR-LIN-M'],
      ['size' => 'L', 'stock' => 14, 'sku' => 'AUR-LIN-L'],
      ['size' => 'XL', 'stock' => 9, 'sku' => 'AUR-LIN-XL'],
    ]
  ],

  // 5. Kids (Girls): Embroidered Festive Lehenga Choli Set
  [
    'name' => 'Girls Festive Sequin Lehenga Choli with Net Dupatta',
    'slug' => 'girls-festive-sequin-lehenga-choli-set',
    'sku' => 'AUR-KID-GIRL-005',
    'brand' => 'AURA & CO. Kids',
    'gender' => 'kids',
    'description' => 'An adorable and lightweight 3-piece ethnic lehenga choli set for young girls with cotton lining for itch-free festive celebrations.',
    'features' => [
      '100% Soft Cotton Inner Lining',
      'Lightweight Net Dupatta with Tassel Trims',
      'Adjustable drawstring waistband',
      'Comfort-fit stitched blouse with back zipper'
    ],
    'material' => 'Georgette & Net with 100% Cotton Lining',
    'care_instructions' => 'Gentle dry clean or delicate hand wash.',
    'price' => 1899.00,
    'original_price' => 2999.00,
    'discount_percent' => 36,
    'category_id' => 3, // Kids
    'subcategory_id' => 31, // Girls Ethnic & Frocks
    'image' => 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=700&h=900&fit=crop',
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=700&h=900&fit=crop', 'label' => 'Main Lehenga View', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=700&h=900&fit=crop', 'label' => 'Dupatta & Choli', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Baby Pink', 'code' => '#FFB6C1'],
      ['name' => 'Lavender Purple', 'code' => '#E6E6FA'],
      ['name' => 'Sunshine Yellow', 'code' => '#FBBF24'],
      ['name' => 'Mint Green', 'code' => '#A7F3D0'],
    ],
    'sizes' => [
      ['size' => '2-3 Years', 'stock' => 8, 'sku' => 'AUR-KID-23Y'],
      ['size' => '4-5 Years', 'stock' => 12, 'sku' => 'AUR-KID-45Y'],
      ['size' => '6-7 Years', 'stock' => 10, 'sku' => 'AUR-KID-67Y'],
      ['size' => '8-9 Years', 'stock' => 6, 'sku' => 'AUR-KID-89Y'],
      ['size' => '10-11 Years', 'stock' => 5, 'sku' => 'AUR-KID-1011Y'],
    ]
  ],

  // 6. Kids (Boys): Traditional Silk Kurta Dhoti Set
  [
    'name' => 'Boys Silk Blend Kurta & Ready Dhoti Set with Dupatta',
    'slug' => 'boys-silk-blend-kurta-ready-dhoti-set',
    'sku' => 'AUR-KID-BOY-006',
    'brand' => 'AURA & CO. Kids',
    'gender' => 'kids',
    'description' => 'Elegant traditional boys ethnic wear set featuring a jacquard woven kurta and pre-stitched ready-to-wear pant style dhoti with border accents.',
    'features' => ['Pre-stitched ready-to-wear Dhoti Pants', 'Soft cotton lining inside Kurta', 'Front button closure', 'Skin-friendly non-itch fabric'],
    'material' => 'Silk Blend with Cotton Lining',
    'care_instructions' => 'Gentle hand wash in cold water.',
    'price' => 1499.00,
    'original_price' => 2299.00,
    'discount_percent' => 34,
    'category_id' => 3, // Kids
    'subcategory_id' => 30, // Boys Ethnic Wear
    'image' => 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=700&h=900&fit=crop',
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      ['url' => 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=700&h=900&fit=crop', 'label' => 'Kurta & Dhoti Front', 'primary' => 1],
      ['url' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=700&h=900&fit=crop', 'label' => 'Embroidery Detail', 'primary' => 0],
    ],
    'colors' => [
      ['name' => 'Mustard Yellow', 'code' => '#D97706'],
      ['name' => 'Royal Blue', 'code' => '#1D4ED8'],
      ['name' => 'Maroon Red', 'code' => '#991B1B'],
      ['name' => 'Pure White', 'code' => '#FFFFFF'],
    ],
    'sizes' => [
      ['size' => '2-3 Years', 'stock' => 10, 'sku' => 'AUR-BOY-23Y'],
      ['size' => '4-5 Years', 'stock' => 12, 'sku' => 'AUR-BOY-45Y'],
      ['size' => '6-7 Years', 'stock' => 10, 'sku' => 'AUR-BOY-67Y'],
      ['size' => '8-9 Years', 'stock' => 7, 'sku' => 'AUR-BOY-89Y'],
    ]
  ]
];

foreach ($sampleProducts as $p) {
  $featuresJson = json_encode($p['features']);
  
  $stmt = $mysqli->prepare("INSERT INTO products 
    (name, slug, sku, brand, gender, description, features, material, care_instructions, price, original_price, discount_percent, category_id, subcategory_id, image, is_featured, is_active) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param('sssssssssddiiisii', 
    $p['name'], $p['slug'], $p['sku'], $p['brand'], $p['gender'], $p['description'], $featuresJson, 
    $p['material'], $p['care_instructions'], $p['price'], $p['original_price'], $p['discount_percent'], 
    $p['category_id'], $p['subcategory_id'], $p['image'], $p['is_featured'], $p['is_active']);

  if ($stmt->execute()) {
    $prodId = $mysqli->insert_id;
    echo "✔ Added Product: {$p['name']} (ID: $prodId)\n";

    // Insert Images with labels
    $imgStmt = $mysqli->prepare("INSERT INTO product_images (product_id, image_url, image_label, sort_order, is_primary) VALUES (?, ?, ?, ?, ?)");
    foreach ($p['images'] as $idx => $img) {
      $imgStmt->bind_param('issii', $prodId, $img['url'], $img['label'], $idx, $img['primary']);
      $imgStmt->execute();
    }

    // Insert Colors
    $clrStmt = $mysqli->prepare("INSERT INTO product_colors (product_id, color_code, color_name, sort_order) VALUES (?, ?, ?, ?)");
    foreach ($p['colors'] as $cIdx => $clr) {
      $clrStmt->bind_param('issi', $prodId, $clr['code'], $clr['name'], $cIdx);
      $clrStmt->execute();
    }

    // Insert Sizes
    $szStmt = $mysqli->prepare("INSERT INTO product_sizes (product_id, size, stock, sku) VALUES (?, ?, ?, ?)");
    foreach ($p['sizes'] as $sz) {
      $szStmt->bind_param('isis', $prodId, $sz['size'], $sz['stock'], $sz['sku']);
      $szStmt->execute();
    }
  } else {
    echo "✖ Failed to insert product {$p['name']}: " . $mysqli->error . "\n";
  }
}

// Add sample demo customer and order if empty
$checkCust = $mysqli->query("SELECT id FROM customers LIMIT 1");
if ($checkCust && $checkCust->num_rows === 0) {
  $hash = password_hash('customer123', PASSWORD_DEFAULT);
  $mysqli->query("INSERT INTO customers (first_name, last_name, email, phone, password, gender) VALUES
    ('Priya', 'Sharma', 'priya@example.com', '+91 98765 12345', '$hash', 'female'),
    ('Rahul', 'Verma', 'rahul@example.com', '+91 98765 54321', '$hash', 'male')");
  echo "✔ Added sample customers\n";
}

$checkOrders = $mysqli->query("SELECT id FROM orders LIMIT 1");
if ($checkOrders && $checkOrders->num_rows === 0) {
  $cust = $mysqli->query("SELECT id, first_name, last_name, email, phone FROM customers LIMIT 1")->fetch_assoc();
  if ($cust) {
    $shipping = json_encode([
      'full_name' => $cust['first_name'] . ' ' . $cust['last_name'],
      'phone' => $cust['phone'],
      'address_line1' => 'Flat 402, Royal Palms Heights',
      'address_line2' => 'MG Road, Bandra West',
      'city' => 'Mumbai',
      'state' => 'Maharashtra',
      'postal_code' => '400050',
      'country' => 'India'
    ]);
    $billing = $shipping;
    $ordNum = generateOrderNumber();

    $mysqli->query("INSERT INTO orders 
      (order_number, customer_id, customer_name, customer_email, customer_phone, billing_address, shipping_address, subtotal, discount_amount, grand_total, payment_method, payment_status, order_status, tracking_number, notes) 
      VALUES 
      ('$ordNum', {$cust['id']}, '{$cust['first_name']} {$cust['last_name']}', '{$cust['email']}', '{$cust['phone']}', '$billing', '$shipping', 3499.00, 0.00, 3499.00, 'cod', 'pending', 'pending', '', 'First time order')");

    $ordId = $mysqli->insert_id;
    $firstProd = $mysqli->query("SELECT id, name, sku, price FROM products LIMIT 1")->fetch_assoc();
    if ($firstProd) {
      $mysqli->query("INSERT INTO order_items (order_id, product_id, product_name, product_sku, size, color_name, color_code, quantity, unit_price, total_price) VALUES
        ($ordId, {$firstProd['id']}, '{$firstProd['name']}', '{$firstProd['sku']}', 'M', 'Pure White', '#FFFFFF', 1, {$firstProd['price']}, {$firstProd['price']})");
    }

    $mysqli->query("INSERT INTO order_status_history (order_id, status, note) VALUES ($ordId, 'pending', 'Order placed online by customer')");
    echo "✔ Added sample demo order $ordNum\n";
  }
}

// Sample Enquiries
$checkEnq = $mysqli->query("SELECT id FROM enquiries LIMIT 1");
if ($checkEnq && $checkEnq->num_rows === 0) {
  $mysqli->query("INSERT INTO enquiries (name, email, phone, subject, message, status) VALUES
    ('Ananya Kapoor', 'ananya@gmail.com', '+91 98112 34567', 'Custom stitching for Georgette Suit', 'Hello, do you provide customized stitching according to my specific measurements for the Anarkali suit?', 'new'),
    ('Vikram Malhotra', 'vikram@yahoo.com', '+91 98223 45678', 'Bulk wedding order inquiry', 'Hi, I need 10 kurta sets for my wedding party. Can you share bulk quotation?', 'in_progress')");
  echo "✔ Added sample contact enquiries\n";
}

echo "🎉 Database 'cloths' seeded successfully with sample fashion products, colors, multi-images, orders, and inquiries!\n";
