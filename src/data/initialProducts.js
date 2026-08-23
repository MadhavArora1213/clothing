export const initialProducts = [
  {
    id: 'prod-1',
    name: 'Vintage Nomad Acid-Wash Oversized Tee',
    slug: 'vintage-nomad-acid-wash-oversized-tee',
    sku: 'AUR-TSH-001',
    category: 'oversized',
    subCategory: 'acid-wash',
    gender: 'unisex',
    price: 1299,
    originalPrice: 2499,
    discountPercent: 48,
    isFeatured: true,
    isNewArrival: true,
    isBestseller: true,
    rating: 4.9,
    reviewCount: 142,
    images: [
      'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'Constructed from heavy 260 GSM single jersey cotton with artisanal mineral wash for a distinct vintage patina. Features a relaxed drop-shoulder cut, reinforced ribbed crewneck, and subtle tonal embroidery.',
    fabricDetails: '100% Combed Heavyweight Cotton (260 GSM), Bio-washed & Pre-shrunk',
    fit: 'Relaxed Oversized Fit (Size down for regular fit)',
    careInstructions: 'Machine wash cold inside out, do not bleach, tumble dry low, warm iron if needed.',
    sizes: [
      { size: 'S', stock: 12 },
      { size: 'M', stock: 8 },
      { size: 'L', stock: 15 },
      { size: 'XL', stock: 4 },
      { size: 'XXL', stock: 2 }
    ],
    colors: [
      { name: 'Washed Charcoal', hex: '#333333' },
      { name: 'Vintage Olive', hex: '#556B2F' },
      { name: 'Oatmeal Dust', hex: '#D2B48C' }
    ],
    tags: ['Streetwear', 'Drop Shoulder', 'Heavyweight', 'Trending']
  },
  {
    id: 'prod-2',
    name: 'Artisanal Hand-Block Indigo Linen Kurta Set',
    slug: 'artisanal-indigo-linen-kurta-set',
    sku: 'AUR-ETH-002',
    category: 'ethnic-fusion',
    subCategory: 'kurta-sets',
    gender: 'men',
    price: 2899,
    originalPrice: 4999,
    discountPercent: 42,
    isFeatured: true,
    isNewArrival: false,
    isBestseller: true,
    rating: 4.8,
    reviewCount: 98,
    images: [
      'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'Inspired by Arya Creation heritage aesthetics. Breathable organic French flax linen tailored with geometric hand-block patterns, modern mandarin collar, wooden buttons, and matching tapered linen trousers.',
    fabricDetails: '100% Pure Organic Linen, Natural Vegetable Dyes',
    fit: 'Tailored Comfort Fit',
    careInstructions: 'Dry clean recommended for initial wash, gentle hand wash thereafter.',
    sizes: [
      { size: 'S', stock: 5 },
      { size: 'M', stock: 10 },
      { size: 'L', stock: 7 },
      { size: 'XL', stock: 3 },
      { size: 'XXL', stock: 1 }
    ],
    colors: [
      { name: 'Royal Indigo', hex: '#1F3A60' },
      { name: 'Sand Khaki', hex: '#C2B280' },
      { name: 'Ivory White', hex: '#FDFBF7' }
    ],
    tags: ['Ethnic Chic', 'Arya Inspired', 'Pure Linen', 'Occasion Wear']
  },
  {
    id: 'prod-3',
    name: 'Sorrento Breathable Resort Linen Co-Ord',
    slug: 'sorrento-resort-linen-co-ord',
    sku: 'AUR-CRD-003',
    category: 'co-ords',
    subCategory: 'linen-sets',
    gender: 'men',
    price: 2499,
    originalPrice: 4199,
    discountPercent: 40,
    isFeatured: true,
    isNewArrival: true,
    isBestseller: false,
    rating: 4.7,
    reviewCount: 64,
    images: [
      'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1516826957135-700dedea698c?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'An effortless 2-piece pairing featuring a camp-collar relaxed shirt with horn buttons, paired with matching elasticated-waist drawstring tailored shorts.',
    fabricDetails: '70% European Linen, 30% Egyptian Cotton blend for anti-wrinkle resilience',
    fit: 'Breezy Resort Fit',
    careInstructions: 'Cold machine wash, air dry in shade.',
    sizes: [
      { size: 'S', stock: 8 },
      { size: 'M', stock: 14 },
      { size: 'L', stock: 12 },
      { size: 'XL', stock: 6 },
      { size: 'XXL', stock: 0 }
    ],
    colors: [
      { name: 'Sage Green', hex: '#87A96B' },
      { name: 'Terracotta', hex: '#CC4E33' },
      { name: 'Raw Ecru', hex: '#F0EAD6' }
    ],
    tags: ['Vacation', 'Co-ord', 'Summer 2026', 'Breathable']
  },
  {
    id: 'prod-4',
    name: 'Kyoto Minimalist Drop-Shoulder Heavy Hoodie',
    slug: 'kyoto-minimalist-drop-shoulder-hoodie',
    sku: 'AUR-HOD-004',
    category: 'men',
    subCategory: 'hoodies',
    gender: 'unisex',
    price: 1899,
    originalPrice: 3499,
    discountPercent: 45,
    isFeatured: true,
    isNewArrival: false,
    isBestseller: true,
    rating: 4.9,
    reviewCount: 230,
    images: [
      'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1578587018452-892bacefd3f2?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'Crafted from 380 GSM ultra-plush French Terry fleece. Dual-lined structured hood without drawstrings for an ultra-clean architectural aesthetic. Kangaroo pocket and double-stitched ribbed cuffs.',
    fabricDetails: '100% Cotton French Terry (380 GSM)',
    fit: 'Boxy Drop Shoulder Fit',
    careInstructions: 'Machine wash cold with like colors, do not iron on print.',
    sizes: [
      { size: 'S', stock: 10 },
      { size: 'M', stock: 20 },
      { size: 'L', stock: 18 },
      { size: 'XL', stock: 9 },
      { size: 'XXL', stock: 4 }
    ],
    colors: [
      { name: 'Pitch Black', hex: '#111111' },
      { name: 'Stone Grey', hex: '#9E9E9E' },
      { name: 'Muted Lavender', hex: '#967BB6' }
    ],
    tags: ['Heavyweight', 'Fleece', 'Essential', 'Winter Streetwear']
  },
  {
    id: 'prod-5',
    name: 'Elysian Draped Satin Maxi Evening Dress',
    slug: 'elysian-draped-satin-maxi-dress',
    sku: 'AUR-DRS-005',
    category: 'women',
    subCategory: 'dresses',
    gender: 'women',
    price: 2199,
    originalPrice: 3999,
    discountPercent: 45,
    isFeatured: true,
    isNewArrival: true,
    isBestseller: true,
    rating: 4.8,
    reviewCount: 115,
    images: [
      'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'High-lustre fluid liquid satin cut on the bias to hug curves gracefully. Features cowl neckline, delicate adjustable criss-cross back straps, and a thigh-high side slit for fluid movement.',
    fabricDetails: 'Premium Mulberry Silk & Viscose Satin Blend',
    fit: 'Fluid Bias-Cut Fit',
    careInstructions: 'Dry clean only or steam gently on silk setting.',
    sizes: [
      { size: 'XS', stock: 4 },
      { size: 'S', stock: 9 },
      { size: 'M', stock: 11 },
      { size: 'L', stock: 5 },
      { size: 'XL', stock: 2 }
    ],
    colors: [
      { name: 'Emerald Jade', hex: '#00563B' },
      { name: 'Champagne Gold', hex: '#F7E7CE' },
      { name: 'Midnight Noir', hex: '#0D0D0D' }
    ],
    tags: ['Luxury', 'Evening Wear', 'Satin', 'Party']
  },
  {
    id: 'prod-6',
    name: 'Tactical Multi-Pocket Parachute Cargoes',
    slug: 'tactical-multi-pocket-parachute-cargoes',
    sku: 'AUR-CRG-006',
    category: 'men',
    subCategory: 'cargoes',
    gender: 'unisex',
    price: 1999,
    originalPrice: 3599,
    discountPercent: 44,
    isFeatured: false,
    isNewArrival: true,
    isBestseller: true,
    rating: 4.7,
    reviewCount: 89,
    images: [
      'https://images.unsplash.com/photo-1517445312882-bc9910d016b7?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'Ripstop durable stretch cotton twill equipped with 8 ergonomic bellows pockets, tactical matte black D-rings, adjustable bungee hem toggles to switch between flared and tapered silhouette.',
    fabricDetails: '98% Cotton Ripstop Twill, 2% Spandex',
    fit: 'Relaxed Baggy / Tapered Adjustable',
    careInstructions: 'Machine wash cold inside out with like colors.',
    sizes: [
      { size: 'S', stock: 7 },
      { size: 'M', stock: 16 },
      { size: 'L', stock: 14 },
      { size: 'XL', stock: 8 },
      { size: 'XXL', stock: 3 }
    ],
    colors: [
      { name: 'Desert Sand', hex: '#C2B280' },
      { name: 'Matte Olive', hex: '#4B5320' },
      { name: 'Stealth Black', hex: '#1C1917' }
    ],
    tags: ['Streetwear', 'Cargoes', 'Utility', 'Parachute']
  },
  {
    id: 'prod-7',
    name: 'AURA Monogram Embossed Heavy Canvas Tote',
    slug: 'aura-monogram-heavy-canvas-tote',
    sku: 'AUR-ACC-007',
    category: 'accessories',
    subCategory: 'tote-bags',
    gender: 'unisex',
    price: 899,
    originalPrice: 1799,
    discountPercent: 50,
    isFeatured: false,
    isNewArrival: true,
    isBestseller: false,
    rating: 4.9,
    reviewCount: 47,
    images: [
      'https://images.unsplash.com/photo-1544816155-12df9643f363?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80'
    ],
    description: '16oz heavyweight organic washed canvas tote bag with reinforced genuine leather handles, interior padded 15-inch laptop sleeve, and YKK zipper closure.',
    fabricDetails: '100% Heavy Organic Cotton Canvas (16oz), Leather Trim',
    fit: 'One Size (38cm x 42cm x 12cm)',
    careInstructions: 'Spot clean with damp cloth.',
    sizes: [
      { size: 'FREE SIZE', stock: 35 }
    ],
    colors: [
      { name: 'Natural Canvas', hex: '#FDF6E2' },
      { name: 'Charcoal Black', hex: '#262626' }
    ],
    tags: ['Accessory', 'Laptop Tote', 'Everyday Essential']
  },
  {
    id: 'prod-8',
    name: 'Aura Modern Chikankari Embroidered Short Kurti',
    slug: 'aura-modern-chikankari-short-kurti',
    sku: 'AUR-ETH-008',
    category: 'women',
    subCategory: 'fusion-wear',
    gender: 'women',
    price: 1799,
    originalPrice: 3299,
    discountPercent: 45,
    isFeatured: true,
    isNewArrival: true,
    isBestseller: true,
    rating: 4.9,
    reviewCount: 168,
    images: [
      'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=1000&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=1000&auto=format&fit=crop&q=80'
    ],
    description: 'Exquisite tone-on-tone handcrafted Lucknowi Chikankari embroidery on lightweight pure modal fabric. Featuring bell sleeves and shell buttons for a modern fusion statement.',
    fabricDetails: '100% Breathable Modal Cotton',
    fit: 'Relaxed Flared Fit',
    careInstructions: 'Gentle hand wash in cold water.',
    sizes: [
      { size: 'XS', stock: 6 },
      { size: 'S', stock: 12 },
      { size: 'M', stock: 15 },
      { size: 'L', stock: 10 },
      { size: 'XL', stock: 4 }
    ],
    colors: [
      { name: 'Powder Blue', hex: '#B0E0E6' },
      { name: 'Blush Rose', hex: '#FFB6C1' },
      { name: 'Pristine White', hex: '#FFFFFF' }
    ],
    tags: ['Arya Inspired', 'Chikankari', 'Handcrafted', 'Festive Ready']
  }
];
