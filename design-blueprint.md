# Premium Clothing E-Commerce — Design Blueprint

> **Reference Baseline:** [The Souled Store](https://www.thesouledstore.com)  
> **Target Standard:** 10/10 Premium Light-Theme E-Commerce  
> **Last Updated:** 2026-08-19

---

## 1. Visual Identity

### 1.1 Color Palette (Light Theme)

| Role | Hex | Usage | Rationale |
|------|-----|-------|-----------|
| **Background Primary** | `#FAFAF9` | Page body, main sections | Warm stone-white avoids sterile clinical feel; reduces eye strain |
| **Background Secondary** | `#FFFFFF` | Cards, modals, dropdowns | Pure white for elevated surfaces and content containers |
| **Surface Elevated** | `#F5F5F4` | Hover states, subtle dividers | Stone-100 for layered depth without harsh contrast |
| **Text Primary** | `#1C1917` | Headlines, body copy, CTAs | Stone-900; near-black for maximum readability |
| **Text Secondary** | `#57534E` | Subtitles, meta text, captions | Stone-600; maintains hierarchy without gray-mud |
| **Text Tertiary** | `#A8A29E` | Placeholders, disabled states | Stone-400; subtle but accessible |
| **Accent Primary** | `#0F172A` | Primary buttons, active nav, links | Slate-900; sophisticated dark anchor |
| **Accent Secondary** | `#EA580C` | Sale badges, urgency CTAs, icons | Orange-600; warm energetic pop against neutral base |
| **Accent Tertiary** | `#D6D3D1` | Borders, input outlines, dividers | Stone-300; soft structural lines |
| **Success** | `#16A34A` | In-stock badges, success toasts | Green-600 |
| **Error** | `#DC2626` | Out-of-stock, error messages | Red-600 |
| **Overlay / Backdrop** | `rgba(28, 25, 23, 0.4)` | Modals, drawers, image zoom | Semi-transparent stone-900 |

### 1.2 Typography Pairings

**Font Stack (System-First, Premium Fallbacks):**

```css
/* Headings — Editorial Authority */
--font-display: 'Playfair Display', 'Noto Serif Display', Georgia, 'Times New Roman', serif;

/* Body — Clean Modern Sans */
--font-body: 'Inter', 'SF Pro Display', 'Segoe UI', system-ui, sans-serif;

/* Utility / Mono (optional) */
--font-mono: 'JetBrains Mono', 'SF Mono', 'Fira Code', monospace;
```

**Type Scale (Modular, 1.25 ratio):**

| Element | Size | Weight | Line Height | Letter Spacing | Usage |
|---------|------|--------|-------------|----------------|-------|
| Display / Hero Headline | 64px | 700 | 1.1 | -0.02em | Homepage hero, campaign banners |
| H1 | 48px | 700 | 1.15 | -0.01em | Page titles, collection headers |
| H2 | 36px | 600 | 1.2 | 0 | Section titles |
| H3 | 28px | 600 | 1.25 | 0 | Product titles, card headers |
| H4 | 22px | 500 | 1.3 | 0 | Category names, filter labels |
| Body Large | 18px | 400 | 1.6 | 0 | Featured product descriptions |
| Body | 16px | 400 | 1.6 | 0 | General content, PDP descriptions |
| Body Small | 14px | 400 | 1.5 | 0.01em | Meta info, timestamps |
| Caption | 12px | 500 | 1.4 | 0.05em | Labels, badges, overlines |
| Button Text | 14px | 600 | 1.4 | 0.02em | All CTAs |

### 1.3 Spacing Guidelines (8-Point Grid)

| Token | Value | Usage |
|-------|-------|-------|
| `--space-1` | 4px | Tight gaps, icon-to-text |
| `--space-2` | 8px | Micro spacing within components |
| `--space-3` | 12px | Compact padding, card internals |
| `--space-4` | 16px | Standard component padding |
| `--space-5` | 20px | Medium gaps between elements |
| `--space-6` | 24px | Section internal padding |
| `--space-8` | 32px | Between related components |
| `--space-10` | 40px | Standard section separation |
| `--space-12` | 48px | Large visual breaks |
| `--space-16` | 64px | Hero-to-content, major sections |
| `--space-20` | 80px | Full-page vertical rhythm |
| `--space-24` | 96px | Footer / top-level separation |

**Spacing Rules:**
- All margins/paddings must be multiples of 4px.
- Use `space-4` for internal component padding, `space-6` for section internals.
- Horizontal rhythm: content max-width `1440px` with `space-6` side padding on desktop, `space-4` on mobile.
- Vertical rhythm between sections: minimum `space-12`.

---

## 2. User Interface Components

### 2.1 Navigation Menu

**Desktop:**
- **Height:** 72px fixed top bar with `backdrop-blur-md` and `bg-white/80` for frosted-glass effect on scroll.
- **Layout:** Logo (left, 160px) → Primary Nav (center, 6-8 items) → Actions (right: Search icon, Wishlist heart with count badge, User avatar, Cart with count badge).
- **Hover:** Underline animation — 2px slate-900 line slides in from center on hover, 200ms ease-out.
- **Active State:** Bold 600 weight + underline. Dropdown menus for "Men", "Women", "Accessories" appear as full-width mega-menus (800px width) with category tiles + featured image.
- **Mega-Menu Content:** 4-column grid — Category list (col 1-2), Featured collection image (col 3), Promo banner (col 4). Staggered fade-in 50ms per column.

**Mobile:**
- **Header:** 60px with hamburger (left), Logo (center, scaled), Cart icon (right).
- **Drawer:** Full-height slide-in from left. Categories listed vertically with chevron expansion for subcategories. Search bar at top, user links at bottom.
- **Bottom Tab Bar (optional):** Home, Categories, Search, Wishlist, Cart.

**Search Overlay:**
- Triggered by Cmd+K or search icon. Full-screen modal with centered large search input (48px height).
- Real-time suggestions dropdown with product thumbnails, recent searches, and trending queries.
- High-contrast backdrop, input auto-focus.

### 2.2 Homepage Hero Section

**Layout (Desktop):**
- Full-viewport height (`100vh`), split layout — 55% visual, 45% content.
- **Left (Visual):** Full-height lifestyle image or video. Object-fit cover. Subtle gradient overlay (stone-900/10 → transparent) from right for text legibility if text overlaps. Optional: floating product card (3D tilt on hover) in lower-right corner of image.
- **Right (Content):** Vertically centered. `space-12` left padding.
  - **Overline:** "NEW ARRIVALS" — Caption size, tracking-wide, accent secondary color.
  - **Headline:** Playfair Display, H1, 2-3 lines max.
  - **Subheadline:** Inter, Body Large, stone-600, max-width 480px.
  - **CTA Group:** Primary button (filled slate-900, white text, 56px height, 24px radius) + Secondary ghost button (outlined, slate-900 border). 16px gap between.
  - **Trust Badges:** Below CTAs — "Free Shipping | 30-Day Returns | Secure Checkout" — 14px, stone-500, with tiny icons.
- **Background:** Clean `#FAFAF9` with a subtle radial gradient (stone-900/3) behind content for depth.

**Layout (Mobile):**
- Stacked: 60vh image on top, content below.
- Headline scaled to 36px. CTAs full-width.
- Horizontal scroll for trust badges.

### 2.3 Product Grids

**Grid System:**
- Desktop: 4 columns (default), 5 on wide (`>1440px`), 3 on tablet (`1024px`), 2 on mobile (`<640px`).
- Gap: `space-6` (24px) between cards.

**Product Card Design:**
- **Container:** White background, `border-radius: 16px`, `overflow: hidden`, subtle `box-shadow: 0 1px 3px rgba(0,0,0,0.04)`. Hover: shadow elevates to `0 8px 30px rgba(0,0,0,0.08)`, translateY(-4px), 300ms ease.
- **Image Area:** Aspect ratio 3:4 (standard fashion ratio). Image covers fully. Bottom 20% has gradient fade for text overlay if needed.
  - **Badges:** Top-left corner — "NEW" (white text on slate-900), "SALE" (white on orange-600), "BESTSELLER" (white on stone-800). 10px padding, 6px radius, Caption size.
  - **Wishlist Heart:** Top-right, 36px circle, white bg with blur, heart icon. Fills red on click.
  - **Quick View:** Appears on hover — centered semi-transparent button "QUICK VIEW" at bottom of image.
  - **Color Variants:** Below image, horizontal row of 6px circles (max 4 shown, +3 indicator).
- **Content Area:** `space-4` padding.
  - **Brand:** Caption, stone-500, tracking-wide uppercase.
  - **Title:** H4 equivalent (20px), 2-line clamp, stone-900.
  - **Price Row:** Current price (18px, 600, stone-900) + Original price (14px, 400, line-through, stone-400) + Discount % (14px, 600, orange-600).
  - **Rating:** Star row (16px stars) + count in parentheses (12px, stone-500).

**Special Cards:**
- **Featured / Hero Card:** Spans 2 columns, 4:3 aspect ratio, larger CTA overlay.
- **Category Card:** Text overlay bottom, bold label, subtle gradient.

### 2.4 Product Detail Page (PDP)

**Layout (Desktop):**
- Two-column: 55% media (left), 45% content (right).
- **Sticky sidebar:** Content column sticks on scroll (`position: sticky; top: 96px`).

**Media Column:**
- Main image: 4:5 aspect ratio, rounded-2xl, `object-fit: cover`.
- Thumbnail strip: Below main, 5 thumbnails (60x80px), active state has stone-900 border (2px), rounded-lg.
- Image zoom on hover (2x, cursor zoom-in). Swipe gestures on mobile.

**Content Column:**
- **Breadcrumb:** Home > Men > T-Shirts > [Product Name] — 13px, stone-500.
- **Brand + Title:** Brand (Caption, uppercase), Title (H3, 28px).
- **Rating + Reviews:** Stars + "4.8 (124 reviews)" link → scrolls to review section.
- **Price Block:** Large price (32px, 700), discount badge next to it, EMI info in smaller text.
- **Variant Selectors:**
  - **Color:** Label "Color: [Selected Name]" + pill buttons (32px circles) with subtle ring on active.
  - **Size:** Label "Size: [Selected]" + rectangular buttons (48px height, auto width, min 56px). Out of stock sizes: gray bg, strikethrough, `cursor-not-allowed`, tooltip "Notify Me".
  - **Fit Guide:** Small link below sizes.
- **Actions:**
  - **Quantity:** Stepper (− 1 +) with 48px height inputs.
  - **Add to Cart:** Full-width, 56px height, slate-900 bg, white text, rounded-xl. Hover: slight scale(1.01), active: scale(0.98). Loading state: spinner inside button.
  - **Wishlist + Share:** Icon buttons below, 48px, outlined.
- **Accordion Details:**
  - Description (rich text, 2-3 paragraphs)
  - Features (bulleted list)
  - Material & Care
  - Shipping & Returns
  - Each accordion: 16px padding, border-top, smooth height transition (300ms).
- **Sticky Bottom Bar (Mobile):** Fixed bar with size selector + Add to Cart button.

---

## 3. User Experience Enhancements

### 3.1 Micro-Interactions

| Interaction | Trigger | Animation | Duration | Easing |
|-------------|---------|-----------|----------|--------|
| **Add to Cart** | Click "Add to Cart" | Button fills with checkmark, then cart icon bounces in header | 600ms | cubic-bezier(0.34, 1.56, 0.64, 1) |
| **Wishlist Heart** | Click heart | Scale pop (1 → 1.3 → 1), particle burst (3 tiny hearts float up) | 500ms | ease-out |
| **Image Swap** | Hover thumbnail | Crossfade with slight scale (0.98 → 1) | 250ms | ease-in-out |
| **Button Hover** | Mouse enter | Background color shift, subtle shadow lift | 200ms | ease-out |
| **Quantity Stepper** | Click +/− | Number flips vertically (rotateX) | 150ms | ease-out |
| **Filter Toggle** | Click filter | Slide-in drawer from right with backdrop fade | 300ms | cubic-bezier(0.16, 1, 0.3, 1) |
| **Toast Notification** | Add to cart / error | Slide in from top-right, auto-dismiss 3s | 400ms in / 300ms out | cubic-bezier(0.16, 1, 0.3, 1) |
| **Page Transition** | Route change | Fade + slight upward slide of content | 300ms | ease-out |
| **Skeleton Loading** | Content loading | Shimmer gradient sweep (left to right) | 1.5s infinite | linear |

### 3.2 Smooth Transitions

- **Page Load:** Initial skeleton screens with shimmer for images + text. Actual content fades in once loaded.
- **Image Lazy Load:** Blur-up technique. Low-res placeholder (20px wide) with `blur(20px)` transitions to sharp image.
- **Filter Application:** Products re-stagger into new positions using FLIP animation (First, Last, Invert, Play). Stagger delay of 30ms per card.
- **Drawer / Modal:** Backdrop fades in first (200ms), then content slides/scales in (300ms). Close in reverse.
- **Scroll Behavior:** Smooth scroll to anchor links. Header shrinks from 72px → 56px on scroll past 100px with shadow appearance.

### 3.3 Intuitive Navigation

- **Breadcrumbs:** Always visible on PDP and category pages. Last item is current page (non-linked, bold).
- **Sticky Add-to-Cart Bar (Mobile):** Persists at bottom on PDP, includes size selector and CTA. Only appears after scrolling past media.
- **Quick View Modal:** From any grid, opens centered modal (90% width, max 1000px) with image carousel + add-to-cart. No page navigation required.
- **Recently Viewed:** Horizontal scroll strip at bottom of PDP and homepage footer. Stores last 12 items in `localStorage`.
- **Smart Search:**
  - Autocomplete with product previews (image + price + name).
  - Typo tolerance ("t-sirt" → "t-shirt").
  - Recent searches shown as chips.
  - "No results" state with suggestions and popular categories.
- **Filter Drawer:** Persistent filters on desktop (sidebar), drawer on mobile. Applied filters shown as removable chips above grid. "Clear All" button.

### 3.4 Conversion Rate Optimizations

- **Social Proof:** "XX people are viewing this right now" near PDP. "Sold 5 minutes ago" on product cards.
- **Urgency Badges:** Limited stock indicator ("Only 3 left!") with subtle orange pulse animation.
- **Trust Signals:** Secure checkout lock icon, return policy snippet near CTA, verified reviews with photos.
- **Size Guide Trigger:** Contextual tooltip next to size selector — "Not sure? View Size Guide" opens a modal with measurement chart and model stats.
- **Cart Persistence:** Cart survives session and device (guest + logged-in sync).
- **Guest Checkout:** Prominent option, no forced registration.
- **Wishlist Notifications:** Email/push when wishlisted item goes on sale or back in stock.

---

## 4. Layout Structure (Wireframes)

### 4.1 Desktop Wireframe

```
┌─────────────────────────────────────────────────────────────────────┐
│ [Logo]      [Men ▼] [Women ▼] [Accessories] [Sale] [New]    🔍 ♡ 👤 🛒 │  ← 72px Sticky Header
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   [Lifestyle Image / Video - 55%]   │  NEW ARRIVALS                 │
│                                     │  ─────────────                │
│                                     │                               │
│                                     │  The Essential               │
│                                     │  Summer Collection            │
│                                     │                               │
│                                     │  Effortless style for every   │
│                                     │  occasion. Discover pieces    │
│                                     │  that define your wardrobe.   │
│                                     │                               │
│                                     │  [Shop Now →]  [Lookbook]    │
│                                     │                               │
│                                     │  ✓ Free Shipping  ✓ Returns   │
│                                     │                               │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  TRENDING NOW                                                       │
│  ──────────────────────────                                         │
│                                                                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │  [IMG]   │ │  [IMG]   │ │  [IMG]   │ │  [IMG]   │             │
│  │  NEW     │ │  SALE    │ │          │ │  NEW     │             │
│  │ ♡        │ │ ♡        │ │ ♡        │ │ ♡        │             │
│  │ Brand    │ │ Brand    │ │ Brand    │ │ Brand    │             │
│  │ Product  │ │ Product  │ │ Product  │ │ Product  │             │
│  │ Title    │ │ Title    │ │ Title    │ │ Title    │             │
│  │ ₹899     │ │ ₹1,200   │ │ ₹1,499   │ │ ₹799     │             │
│  │ ₹1,499   │ │ ₹1,999   │ │ ₹2,499   │ │ ₹1,299   │             │
│  │ [-40%]   │ │ [-40%]   │ │ [-40%]   │ │ [-40%]   │             │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘             │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│  SHOP BY CATEGORY                                                   │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐      │
│  │                 │ │                 │ │                 │      │
│  │   [IMG]         │ │   [IMG]         │ │   [IMG]         │      │
│  │                 │ │                 │ │                 │      │
│  │   MEN           │ │   WOMEN         │ │   ACCESSORIES   │      │
│  │                 │ │                 │ │                 │      │
│  └─────────────────┘ └─────────────────┘ └─────────────────┘      │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│  FOOTER                                                             │
│  [Logo]                     [About] [Contact] [Careers] [Blog]     │
│  [Newsletter Signup]        [Returns] [Shipping] [Size Guide]      │
│  © 2026 Brand Name          [Instagram] [Twitter] [YouTube]        │
└─────────────────────────────────────────────────────────────────────┘
```

### 4.2 Mobile Wireframe

```
┌──────────────────────────────┐
│ ☰        BRAND NAME      🛒   │  ← 60px Header
├──────────────────────────────┤
│                              │
│       [Hero Image 60vh]      │
│                              │
├──────────────────────────────┤
│                              │
│   NEW ARRIVALS               │
│   The Essential Summer        │
│   Collection                  │
│                              │
│   Effortless style for every  │
│   occasion...                 │
│                              │
│   [Shop Now →]  [Lookbook]   │
│                              │
├──────────────────────────────┤
│                              │
│  TRENDING NOW                │
│  [← Scroll Horizontally →]   │
│  ┌────┐ ┌────┐ ┌────┐       │
│  │IMG │ │IMG │ │IMG │       │
│  └────┘ └────┘ └────┘       │
│                              │
├──────────────────────────────┤
│                              │
│  SHOP BY CATEGORY            │
│  ┌──────────────┐            │
│  │   [IMG]      │            │
│  │   MEN        │            │
│  └──────────────┘            │
│  ┌──────────────┐            │
│  │   [IMG]      │            │
│  │   WOMEN      │            │
│  └──────────────┘            │
│  ┌──────────────┐            │
│  │   [IMG]      │            │
│  │   ACCESSORIES│            │
│  └──────────────┘            │
│                              │
├──────────────────────────────┤
│ FOOTER                       │
│ About | Contact | Returns     │
│ © 2026 Brand Name            │
│                              │
├──────────────────────────────┤
│ 🏠  🗂  🔍  ♡  🛒           │  ← Bottom Tab Bar
└──────────────────────────────┘
```

### 4.3 PDP Desktop Wireframe

```
┌──────────────────────────────────────────────────────────────────────┐
│ [Logo]      [Men ▼] [Women ▼] ...                            🔍 ♡ 🛒 │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ← Back to T-Shirts                                                 │
│                                                                      │
│  ┌─────────────────────────┐  Brand: BRAND NAME                     │
│  │                         │  Product Title (H3)                    │
│  │                         │  ⭐⭐⭐⭐⭐ 4.8 (124 reviews)            │
│  │                         │                                        │
│  │     [Main Image]        │  ₹1,299   ₹1,999   [-35%]             │
│  │                         │  inclusive of all taxes                 │
│  │                         │                                        │
│  │                         │  Color: [●] [●] [●] [●]               │
│  │                         │  Size:   [S] [M] [L] [XL] [XXL]       │
│  │                         │           [Notify Me ↗]                 │
│  │                         │                                        │
│  │                         │  Qty: [− 1 +]                          │
│  │                         │                                        │
│  │                         │  [   ADD TO CART   ]                   │
│  │                         │                                        │
│  │                         │  ♡ Wishlist    ↗ Share                 │
│  │                         │                                        │
│  │  [thumb] [thumb] [...]  │  ▼ Description                         │
│  │                         │  ▼ Features                            │
│  │                         │  ▼ Material & Care                     │
│  └─────────────────────────┘  ▼ Shipping & Returns                  │
│                                                                      │
│  YOU MAY ALSO LIKE                                                   │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐                                      │
│  │    │ │    │ │    │ │    │                                      │
│  └────┘ └────┘ └────┘ └────┘                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 5. Design System Summary

| Aspect | Decision |
|--------|----------|
| **Theme** | Light, warm stone-white base |
| **Primary Accent** | Slate-900 (dark navy-black) |
| **Secondary Accent** | Orange-600 (warm energetic) |
| **Corner Radius** | 12px (buttons), 16px (cards), 24px (hero CTA) |
| **Shadows** | 3 tiers: sm (cards), md (hover), lg (modals) |
| **Border Width** | 1px (dividers), 2px (active states) |
| **Transition Default** | 200ms ease-out; motion-safe media query respected |
| **Max Content Width** | 1440px |
| **Breakpoints** | sm: 640px, md: 768px, lg: 1024px, xl: 1280px, 2xl: 1536px |

---

## 6. Recommended Tech Stack

| Layer | Recommendation | Rationale |
|-------|---------------|-----------|
| **Framework** | Next.js 14 (App Router) | SSR/SSG for SEO, image optimization, built-in routing |
| **Styling** | Tailwind CSS + CSS Modules | Utility-first consistency, custom animations in modules |
| **Animation** | Framer Motion | Declarative transitions, FLIP for grid reordering |
| **State** | Zustand + React Query | Lightweight global state, server state caching |
| **CMS** | Sanity.io or Shopify Headless | Product content management + checkout |
| **Images** | Next Image + Cloudinary | Auto format/WebP, lazy loading, CDN |
| **Fonts** | Google Fonts (Playfair + Inter) | Self-hosted via `next/font` for performance |
| **Icons** | Lucide React | Consistent line-weight, tree-shakeable |
| **Testing** | Vitest + Playwright | Unit + E2E with visual regression snapshots |

---

*End of Blueprint*
