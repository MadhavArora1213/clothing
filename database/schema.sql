-- ============================================
-- CLOTHS E-Commerce Database Schema
-- Database: cloths (MySQL 8.0+)
-- ============================================

CREATE DATABASE IF NOT EXISTS cloths CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cloths;

-- ============================================
-- 1. ADMINS
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
  avatar VARCHAR(500) NULL,
  is_active TINYINT(1) DEFAULT 1,
  last_login DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CATEGORIES (Parent: Women, Men, Kids, Accessories)
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(100) UNIQUE NOT NULL,
  department ENUM('women', 'men', 'kids', 'all') DEFAULT 'all',
  description TEXT NULL,
  image VARCHAR(500) NULL,
  parent_id INT UNSIGNED DEFAULT 0,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. PRODUCTS
-- ============================================
CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  sku VARCHAR(100) UNIQUE NULL,
  brand VARCHAR(100) DEFAULT 'AURA & CO.',
  gender ENUM('women', 'men', 'kids', 'unisex') DEFAULT 'women',
  description TEXT NULL,
  features JSON NULL,
  material VARCHAR(255) NULL,
  care_instructions TEXT NULL,
  price DECIMAL(10,2) NOT NULL,
  original_price DECIMAL(10,2) NULL,
  discount_percent TINYINT DEFAULT 0,
  shipping_charge DECIMAL(10,2) DEFAULT 0,
  free_shipping TINYINT(1) DEFAULT 1,
  shipping_days VARCHAR(50) DEFAULT '3-5',
  category_id INT UNSIGNED NOT NULL,
  subcategory_id INT UNSIGNED NULL,
  image VARCHAR(500) NULL,
  is_featured TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  meta_title VARCHAR(255) NULL,
  meta_description TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. PRODUCT IMAGES (Main Image + Sub-Images e.g. Salwar, Dupatta, Back View)
-- ============================================
CREATE TABLE IF NOT EXISTS product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  image_label VARCHAR(100) NULL DEFAULT 'Main View', -- e.g. "Main Front", "Salwar", "Dupatta", "Back View", "Fabric Detail"
  alt_text VARCHAR(255) NULL,
  sort_order INT DEFAULT 0,
  is_primary TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. PRODUCT SIZES & STOCK
-- ============================================
CREATE TABLE IF NOT EXISTS product_sizes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  size VARCHAR(20) NOT NULL,
  stock INT DEFAULT 0,
  sku VARCHAR(100) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  UNIQUE KEY unique_product_size (product_id, size)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. PRODUCT COLORS (Visual Square Swatches)
-- ============================================
CREATE TABLE IF NOT EXISTS product_colors (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  color_code VARCHAR(20) NOT NULL, -- Hex code, e.g. #FFFFFF, #FF69B4, #800080
  color_name VARCHAR(100) NOT NULL, -- e.g. White, Pink, Black, Purple, Maroon
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. CUSTOMERS
-- ============================================
CREATE TABLE IF NOT EXISTS customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  phone VARCHAR(20) NULL,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(500) NULL,
  gender ENUM('male', 'female', 'other') NULL,
  is_active TINYINT(1) DEFAULT 1,
  last_login DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. ADDRESSES
-- ============================================
CREATE TABLE IF NOT EXISTS addresses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NOT NULL,
  label ENUM('home', 'work', 'other') DEFAULT 'home',
  full_name VARCHAR(200) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address_line1 VARCHAR(255) NOT NULL,
  address_line2 VARCHAR(255) NULL,
  city VARCHAR(100) NOT NULL,
  state VARCHAR(100) NOT NULL,
  postal_code VARCHAR(20) NOT NULL,
  country VARCHAR(100) DEFAULT 'India',
  is_default TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. ORDERS
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(50) UNIQUE NOT NULL,
  customer_id INT UNSIGNED NULL,
  customer_name VARCHAR(200) NOT NULL,
  customer_email VARCHAR(255) NOT NULL,
  customer_phone VARCHAR(20) NOT NULL,
  billing_address JSON NOT NULL,
  shipping_address JSON NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  discount_amount DECIMAL(10,2) DEFAULT 0.00,
  coupon_code VARCHAR(50) NULL,
  shipping_amount DECIMAL(10,2) DEFAULT 0.00,
  tax_amount DECIMAL(10,2) DEFAULT 0.00,
  grand_total DECIMAL(10,2) NOT NULL,
  payment_method ENUM('online', 'cod', 'upi') DEFAULT 'cod',
  payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
  tracking_number VARCHAR(100) NULL,
  notes TEXT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. ORDER ITEMS
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  product_sku VARCHAR(100) NULL,
  size VARCHAR(20) NULL,
  color_name VARCHAR(100) NULL,
  color_code VARCHAR(20) NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. ORDER STATUS HISTORY
-- ============================================
CREATE TABLE IF NOT EXISTS order_status_history (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned') NOT NULL,
  note TEXT NULL,
  created_by INT UNSIGNED NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. ENQUIRIES / CONTACT
-- ============================================
CREATE TABLE IF NOT EXISTS enquiries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
  reply TEXT NULL,
  assigned_to INT UNSIGNED NULL,
  resolved_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. COUPONS
-- ============================================
CREATE TABLE IF NOT EXISTS coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL,
  type ENUM('percentage', 'fixed') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL,
  minimum_order_amount DECIMAL(10,2) DEFAULT 0.00,
  maximum_discount_amount DECIMAL(10,2) NULL,
  usage_limit INT NULL,
  usage_count INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  starts_at DATETIME NULL,
  expires_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. REVIEWS
-- ============================================
CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  customer_id INT UNSIGNED NULL,
  customer_name VARCHAR(200) NOT NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  title VARCHAR(255) NULL,
  comment TEXT NULL,
  is_approved TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. CARTS
-- ============================================
CREATE TABLE IF NOT EXISTS carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  customer_id INT UNSIGNED NULL,
  session_id VARCHAR(128) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. CART ITEMS
-- ============================================
CREATE TABLE IF NOT EXISTS cart_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cart_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  size VARCHAR(20) NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. SETTINGS
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) UNIQUE NOT NULL,
  value TEXT NULL,
  type ENUM('text', 'textarea', 'number', 'email', 'url', 'image') DEFAULT 'text',
  group_name VARCHAR(100) DEFAULT 'general',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INITIAL SEED DATA
-- ============================================

INSERT INTO admins (name, email, password, role) VALUES
('Super Admin', 'admin@urbanoutfit.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin')
ON DUPLICATE KEY UPDATE id=id;

-- Top-level Categories: Women, Men, Kids, Accessories
INSERT INTO categories (id, name, slug, department, description, parent_id, sort_order) VALUES
(1, 'Women', 'women', 'women', 'Women ethnic & modern wear collection', 0, 1),
(2, 'Men', 'men', 'men', 'Men ethnic, casual & formal collection', 0, 2),
(3, 'Kids', 'kids', 'kids', 'Boys & girls designer clothing collection', 0, 3),
(4, 'Accessories', 'accessories', 'all', 'Scarves, stoles, dupattas & accessories', 0, 4)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sub-categories for Women (parent_id = 1)
INSERT INTO categories (id, name, slug, department, description, parent_id, sort_order) VALUES
(10, 'Suits & Salwars', 'suits-salwars', 'women', 'Designer unstitched & readymade suit sets with salwar & dupatta', 1, 1),
(11, 'Kurtis & Tunics', 'kurtis-tunics', 'women', 'Daily wear & party wear kurtis', 1, 2),
(12, 'Sarees', 'sarees', 'women', 'Traditional & designer sarees', 1, 3),
(13, 'Lehengas', 'lehengas', 'women', 'Bridal & festive lehengas', 1, 4),
(14, 'Western Wear & Tops', 'women-western-tops', 'women', 'Tops, shirts, dresses & jeans', 1, 5)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sub-categories for Men (parent_id = 2)
INSERT INTO categories (id, name, slug, department, description, parent_id, sort_order) VALUES
(20, 'Kurta Sets & Sherwanis', 'men-kurta-sets', 'men', 'Ethnic kurtas, pyjamas & sherwanis', 2, 1),
(21, 'Shirts', 'men-shirts', 'men', 'Formal & casual shirts', 2, 2),
(22, 'T-Shirts & Polos', 'men-tshirts', 'men', 'Graphic & solid t-shirts', 2, 3),
(23, 'Trousers & Jeans', 'men-trousers-jeans', 'men', 'Chinos, formal trousers & denim', 2, 4),
(24, 'Jackets & Blazers', 'men-jackets-blazers', 'men', 'Winter wear & formal blazers', 2, 5)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sub-categories for Kids (parent_id = 3)
INSERT INTO categories (id, name, slug, department, description, parent_id, sort_order) VALUES
(30, 'Boys Ethnic Wear', 'boys-ethnic-wear', 'kids', 'Kurta pyjamas, nehru jackets for boys', 3, 1),
(31, 'Girls Ethnic & Frocks', 'girls-ethnic-frocks', 'kids', 'Lehengas, frocks, anarkalis for girls', 3, 2),
(32, 'Boys Casuals', 'boys-casuals', 'kids', 'T-shirts, shirts & shorts for boys', 3, 3),
(33, 'Girls Casuals', 'girls-casuals', 'kids', 'Tops, skirts & dresses for girls', 3, 4)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Initial Settings
INSERT INTO settings (`key`, value, type, group_name) VALUES
('site_name', 'AURA & CO.', 'text', 'general'),
('site_tagline', 'Luxury Fashion & Ethnic Couture', 'text', 'general'),
('site_email', 'contact@auraclothing.com', 'email', 'general'),
('site_phone', '+91 98765 43210', 'text', 'general'),
('currency_symbol', '₹', 'text', 'general'),
('site_address', 'Maharana Partap Chowk opposite Shri Guru Nanak Girls School, Mukerian', 'text', 'general'),
('shipping_standard', '0', 'number', 'shipping'),
('shipping_express', '0', 'number', 'shipping'),
('shipping_free_min', '0', 'number', 'shipping'),
('free_shipping', '1', 'number', 'shipping')
ON DUPLICATE KEY UPDATE `key`=`key`;
