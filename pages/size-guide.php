<?php
require_once dirname(__DIR__) . '/config/database.php';
$pageTitle       = 'Size Guide — Find Your Perfect Fit | Urban Outfit Collection';
$pageDescription = 'Find your perfect size with our comprehensive size guide for men, women & kids. Chest, waist, hip measurements for tees, kurtas, co-ords & more.';
$pageKeywords    = 'size guide fashion india, clothing size chart, how to measure size kurta tshirt, urban outfit sizes';
$pageCanonical   = 'https://urbanoutfitshop.com/pages/size-guide.php';
include dirname(__DIR__) . '/includes/header.php';
?>

<main class="page-shell">
  <div class="container">
    <div class="page-hero reveal-up">
      <img src="https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=1800&h=900&fit=crop" alt="Size guide banner" loading="eager">
      <div class="page-hero-content">
        <h1>Size Guide</h1>
        <p>Find your perfect fit with our detailed measurements.</p>
      </div>
    </div>
    <div class="admin-form-page">
      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin-bottom: var(--space-4);">How to Measure</h2>
      <p style="color: var(--color-text-secondary); margin-bottom: var(--space-6);">Use a flexible tape measure to take your measurements. For the most accurate results, measure over lightweight clothing.</p>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Body Measurements (Inches)</h2>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Size</th>
              <th>Bust</th>
              <th>Waist</th>
              <th>Hips</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>XS</td><td>32-33</td><td>24-25</td><td>34-35</td></tr>
            <tr><td>S</td><td>34-35</td><td>26-27</td><td>36-37</td></tr>
            <tr><td>M</td><td>36-37</td><td>28-29</td><td>38-39</td></tr>
            <tr><td>L</td><td>38-40</td><td>30-32</td><td>40-42</td></tr>
            <tr><td>XL</td><td>41-43</td><td>33-35</td><td>43-45</td></tr>
            <tr><td>XXL</td><td>44-46</td><td>36-38</td><td>46-48</td></tr>
          </tbody>
        </table>
      </div>

      <h2 style="font-family: var(--font-display); font-size: var(--text-h3); margin: var(--space-8) 0 var(--space-4);">Still Unsure?</h2>
      <p style="color: var(--color-text-secondary);">If you are between sizes, we recommend sizing up for a more comfortable fit. You can also contact us at <?= sanitize(getSetting('site_email', 'hello@example.com')) ?> for personalized sizing advice.</p>
    </div>
  </div>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
