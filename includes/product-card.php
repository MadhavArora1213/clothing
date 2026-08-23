<?php
/**
 * Product card partial — ARYA CREATION style
 * Expects $product array with: slug, name, price, original_price, discount_percent, image
 */
$cardImage = $product['image'] ?? 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&h=800&fit=crop';
$hasDiscount = !empty($product['original_price']) && (float)$product['original_price'] > (float)$product['price'];
?>
<article class="arya-product-card">
  <div class="arya-product-media">
    <a href="<?= BASE_URL ?>/product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="arya-product-link">
      <img src="<?= htmlspecialchars($cardImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
    </a>
    <div class="arya-product-actions">
      <a href="<?= BASE_URL ?>/product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="arya-action-btn">Quick view</a>
      <button type="button" class="arya-action-btn arya-wishlist-btn" aria-label="Add to wishlist">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3.33.93-4.17 2.36a.75.75 0 0 1-1.33 0C10.33 3.93 8.76 3 7 3A5.5 5.5 0 0 0 1.5 8.5c0 2.3 1.51 4.04 3 5.5l7.5 7.5L19 14z"/></svg>
      </button>
      <button type="button" class="arya-action-btn arya-add-btn" data-slug="<?= htmlspecialchars($product['slug']) ?>" aria-label="Add to cart">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </button>
    </div>
    <?php if ($hasDiscount): ?>
      <span class="arya-sale-badge">Sale</span>
    <?php endif; ?>
  </div>
  <div class="arya-product-info">
    <h3 class="arya-product-title">
      <a href="<?= BASE_URL ?>/product.php?slug=<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a>
    </h3>
    <div class="arya-product-price">
      <?php if ($hasDiscount): ?>
        <span class="arya-price-original">Rs. <?= number_format((float)$product['original_price'], 2) ?></span>
      <?php endif; ?>
      <span class="arya-price-current">Rs. <?= number_format((float)$product['price'], 2) ?></span>
    </div>
  </div>
</article>
