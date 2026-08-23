<?php
/**
 * Database Seeder
 * Run this script to populate the database with sample products
 */

require_once dirname(__DIR__) . '/config/database.php';

$products = [
  [
    'name' => 'Essential Cotton Crew Neck Tee',
    'slug' => 'essential-cotton-tee',
    'sku' => 'ATL-TSH-001',
    'brand' => 'ATELIER',
    'description' => 'Crafted from 100% organic cotton, this essential crew neck tee offers a relaxed yet refined silhouette. The heavyweight 220GSM fabric provides excellent structure while maintaining exceptional softness.',
    'features' => ['100% Organic Cotton, 220GSM', 'Relaxed fit with dropped shoulders', 'Ribbed collar and cuffs', 'Pre-shrunk fabric', 'Ethically manufactured'],
    'material' => '100% Organic Cotton',
    'care_instructions' => 'Machine wash cold with like colors. Tumble dry low. Do not bleach. Iron on medium heat if needed.',
    'price' => 1299,
    'original_price' => 1999,
    'discount_percent' => 35,
    'category_id' => 1,
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Black', 'code' => '#1C1917', 'sort' => 0],
      ['name' => 'White', 'code' => '#FAFAF9', 'sort' => 1],
      ['name' => 'Grey', 'code' => '#57534E', 'sort' => 2],
    ],
    'sizes' => [
      ['size' => 'S', 'stock' => 10],
      ['size' => 'M', 'stock' => 25],
      ['size' => 'L', 'stock' => 20],
      ['size' => 'XL', 'stock' => 15],
      ['size' => 'XXL', 'stock' => 5],
    ]
  ],
  [
    'name' => 'Relaxed Linen Blend Oversized Shirt',
    'slug' => 'linen-blend-shirt',
    'sku' => 'ATL-SHT-001',
    'brand' => 'ATELIER',
    'description' => 'A masterful blend of linen and cotton creates this effortlessly stylish oversized shirt. The breathable fabric drapes beautifully, making it perfect for both casual outings and elevated everyday wear.',
    'features' => ['55% Linen, 45% Cotton', 'Oversized relaxed fit', 'Mother of pearl buttons', 'Curved hem', 'Patch pocket'],
    'material' => '55% Linen, 45% Cotton',
    'care_instructions' => 'Machine wash gentle cycle. Hang dry. Iron while slightly damp for best results.',
    'price' => 2499,
    'original_price' => 3499,
    'discount_percent' => 29,
    'category_id' => 1,
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'White', 'code' => '#FAFAF9', 'sort' => 0],
      ['name' => 'Sky Blue', 'code' => '#87CEEB', 'sort' => 1],
    ],
    'sizes' => [
      ['size' => 'S', 'stock' => 8],
      ['size' => 'M', 'stock' => 15],
      ['size' => 'L', 'stock' => 12],
      ['size' => 'XL', 'stock' => 6],
    ]
  ],
  [
    'name' => 'Slim Fit Stretch Chino Trousers',
    'slug' => 'slim-fit-chinos',
    'sku' => 'ATL-TRS-001',
    'brand' => 'ATELIER',
    'description' => 'These slim fit chinos are cut from a premium stretch cotton twill that moves with you. The modern silhouette sits perfectly at the waist with a tapered leg.',
    'features' => ['98% Cotton, 2% Elastane', 'Slim fit, tapered leg', 'Mid-weight twill', 'Five-pocket styling', 'Flat front'],
    'material' => '98% Cotton, 2% Elastane',
    'care_instructions' => 'Machine wash cold. Tumble dry low. Warm iron if needed.',
    'price' => 1899,
    'original_price' => 2499,
    'discount_percent' => 24,
    'category_id' => 1,
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Khaki', 'code' => '#D2B48C', 'sort' => 0],
      ['name' => 'Olive', 'code' => '#556B2F', 'sort' => 1],
    ],
    'sizes' => [
      ['size' => '28', 'stock' => 5],
      ['size' => '30', 'stock' => 10],
      ['size' => '32', 'stock' => 12],
      ['size' => '34', 'stock' => 8],
    ]
  ],
  [
    'name' => 'Merino Wool Crew Neck Sweater',
    'slug' => 'merino-wool-sweater',
    'sku' => 'ATL-SWT-001',
    'brand' => 'ATELIER',
    'description' => 'Luxuriously soft 100% extra-fine merino wool knitted into a timeless crew neck silhouette. Naturally temperature-regulating and moisture-wicking.',
    'features' => ['100% Extra-fine Merino Wool', 'Regular fit', 'Ribbed collar, cuffs and hem', 'Naturally odor resistant', 'Temperature regulating'],
    'material' => '100% Extra-fine Merino Wool',
    'care_instructions' => 'Hand wash cold or dry clean. Lay flat to dry. Do not wring.',
    'price' => 3299,
    'original_price' => 4299,
    'discount_percent' => 23,
    'category_id' => 1,
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1434389677669-e08b4cda3a0a?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Forest', 'code' => '#2F4F4F', 'sort' => 0],
      ['name' => 'Brown', 'code' => '#8B4513', 'sort' => 1],
      ['name' => 'Cream', 'code' => '#FAFAF9', 'sort' => 2],
    ],
    'sizes' => [
      ['size' => 'S', 'stock' => 6],
      ['size' => 'M', 'stock' => 10],
      ['size' => 'L', 'stock' => 8],
      ['size' => 'XL', 'stock' => 4],
    ]
  ],
  [
    'name' => 'Relaxed Silk Blend Button Down Blouse',
    'slug' => 'silk-blouse',
    'sku' => 'ATL-BLS-001',
    'brand' => 'ATELIER',
    'description' => 'A silk-blend button down that effortlessly bridges casual and formal. The relaxed silhouette drapes beautifully while the subtle sheen of silk adds an element of refined luxury.',
    'features' => ['70% Viscose, 30% Silk', 'Relaxed fit', 'Point collar', 'Mother of pearl buttons', 'Curved hem'],
    'material' => '70% Viscose, 30% Silk',
    'care_instructions' => 'Dry clean recommended. If hand washing, use cold water and mild detergent.',
    'price' => 2799,
    'original_price' => 3499,
    'discount_percent' => 20,
    'category_id' => 2,
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1564257631407-4deb1f99d992?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1551488852-0801756b1a81?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'White', 'code' => '#FAFAF9', 'sort' => 0],
      ['name' => 'Blush', 'code' => '#FFB6C1', 'sort' => 1],
      ['name' => 'Lavender', 'code' => '#E6E6FA', 'sort' => 2],
    ],
    'sizes' => [
      ['size' => 'XS', 'stock' => 5],
      ['size' => 'S', 'stock' => 10],
      ['size' => 'M', 'stock' => 12],
      ['size' => 'L', 'stock' => 6],
    ]
  ],
  [
    'name' => 'High Rise Wide Leg Tailored Trousers',
    'slug' => 'high-rise-trousers',
    'sku' => 'ATL-WLT-001',
    'brand' => 'ATELIER',
    'description' => 'Tailored with a modern wide leg and high rise, these trousers create a powerful silhouette. The premium fabric blend ensures a comfortable drape with just the right amount of structure.',
    'features' => ['97% Polyester, 3% Elastane', 'High rise, wide leg', 'Pressed center crease', 'Side pockets', 'Back welt pockets'],
    'material' => '97% Polyester, 3% Elastane',
    'care_instructions' => 'Machine wash cold. Hang dry. Medium iron if needed.',
    'price' => 2299,
    'original_price' => 2999,
    'discount_percent' => 23,
    'category_id' => 2,
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Black', 'code' => '#1C1917', 'sort' => 0],
      ['name' => 'Tan', 'code' => '#D2B48C', 'sort' => 1],
      ['name' => 'Grey', 'code' => '#708090', 'sort' => 2],
    ],
    'sizes' => [
      ['size' => 'XS', 'stock' => 4],
      ['size' => 'S', 'stock' => 8],
      ['size' => 'M', 'stock' => 10],
      ['size' => 'L', 'stock' => 6],
    ]
  ],
  [
    'name' => 'Relaxed Fit Cashmere Cardigan',
    'slug' => 'cashmere-cardigan',
    'sku' => 'ATL-CDG-001',
    'brand' => 'ATELIER',
    'description' => 'Pure cashmere knitted into an effortlessly relaxed cardigan. The brushed interior provides cloud-like softness while the clean, minimal design ensures it pairs with everything.',
    'features' => ['100% Grade-A Cashmere', 'Relaxed fit', 'Patch pockets', 'Ribbed trim', 'Drop shoulders'],
    'material' => '100% Grade-A Cashmere',
    'care_instructions' => 'Dry clean only. Store folded, not hung.',
    'price' => 5499,
    'original_price' => 6999,
    'discount_percent' => 21,
    'category_id' => 2,
    'is_featured' => 0,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1434389677669-e08b4cda3a0a?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Cream', 'code' => '#FAFAF9', 'sort' => 0],
      ['name' => 'Camel', 'code' => '#D2B48C', 'sort' => 1],
      ['name' => 'Charcoal', 'code' => '#696969', 'sort' => 2],
    ],
    'sizes' => [
      ['size' => 'S', 'stock' => 3],
      ['size' => 'M', 'stock' => 6],
      ['size' => 'L', 'stock' => 4],
    ]
  ],
  [
    'name' => 'Heavyweight Canvas Tote Bag',
    'slug' => 'canvas-tote-bag',
    'sku' => 'ATL-BAG-001',
    'brand' => 'ATELIER',
    'description' => 'A substantial everyday tote crafted from heavyweight 18oz canvas. The reinforced base and thick webbing handles ensure it carries everything you need.',
    'features' => ['18oz Heavyweight Canvas', 'Reinforced base panel', 'Interior zip pocket', 'Magnetic snap closure', 'Reinforced webbing handles'],
    'material' => '100% Cotton Canvas',
    'care_instructions' => 'Spot clean or hand wash cold. Air dry.',
    'price' => 899,
    'original_price' => 1299,
    'discount_percent' => 31,
    'category_id' => 3,
    'is_featured' => 1,
    'is_active' => 1,
    'images' => [
      'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&h=800&fit=crop',
      'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=600&h=800&fit=crop',
    ],
    'colors' => [
      ['name' => 'Natural', 'code' => '#FAFAF9', 'sort' => 0],
      ['name' => 'Black', 'code' => '#1C1917', 'sort' => 1],
    ],
    'sizes' => [
      ['size' => 'One Size', 'stock' => 20],
    ]
  ],
];

foreach ($products as $product) {
  $featuresJson = json_encode($product['features']);
  $stmt = $mysqli->prepare('INSERT INTO products (name, slug, sku, brand, description, features, material, care_instructions, price, original_price, discount_percent, category_id, is_featured, is_active, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
  $stmt->bind_param('ssssssssddiiis', $product['name'], $product['slug'], $product['sku'], $product['brand'], $product['description'], $featuresJson, $product['material'], $product['care_instructions'], $product['price'], $product['original_price'], $product['discount_percent'], $product['category_id'], $product['is_featured'], $product['is_active'], $product['images'][0]);
  $stmt->execute();
  $productId = $mysqli->insert_id;

  foreach ($product['images'] as $index => $img) {
    $isPrimary = $index === 0 ? 1 : 0;
    $mysqli->query("INSERT INTO product_images (product_id, image_url, sort_order, is_primary) VALUES ($productId, '" . $mysqli->real_escape_string($img) . "', $index, $isPrimary)");
  }

  foreach ($product['colors'] as $color) {
    $mysqli->query("INSERT INTO product_colors (product_id, color_code, color_name, sort_order) VALUES ($productId, '" . $mysqli->real_escape_string($color['code']) . "', '" . $mysqli->real_escape_string($color['name']) . "', {$color['sort']})");
  }

  foreach ($product['sizes'] as $size) {
    $mysqli->query("INSERT INTO product_sizes (product_id, size, stock) VALUES ($productId, '" . $mysqli->real_escape_string($size['size']) . "', {$size['stock']})");
  }
}

echo "Seeded " . count($products) . " products successfully.\n";
