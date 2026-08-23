import React, { useState } from 'react';
import { 
  Star, 
  Heart, 
  ShoppingBag, 
  Zap, 
  Ruler, 
  Truck, 
  RotateCcw, 
  ShieldCheck, 
  ChevronDown, 
  ChevronUp, 
  Check, 
  Sparkles, 
  ArrowRight, 
  MapPin, 
  MessageSquarePlus,
  Share2
} from 'lucide-react';
import { useStore } from '../context/StoreContext';
import { ProductCard } from '../components/common/ProductCard';

export const ProductDetailPage = () => {
  const { 
    products, 
    pageParams, 
    addToCart, 
    toggleWishlist, 
    isInWishlist, 
    setIsSizeChartOpen, 
    navigateTo, 
    addToast 
  } = useStore();

  const product = products.find(p => p.slug === pageParams?.slug) || products[0];

  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [selectedSize, setSelectedSize] = useState('M');
  const [selectedColor, setSelectedColor] = useState(product.colors?.[0]?.name || 'Standard');
  const [quantity, setQuantity] = useState(1);
  const [pincode, setPincode] = useState('');
  const [deliveryStatus, setDeliveryStatus] = useState(null);
  const [openAccordion, setOpenAccordion] = useState('desc'); // 'desc' | 'fabric' | 'shipping' | 'reviews'

  // Review Form Modal
  const [showReviewModal, setShowReviewModal] = useState(false);
  const [newRating, setNewRating] = useState(5);
  const [newAuthor, setNewAuthor] = useState('');
  const [newTitle, setNewTitle] = useState('');
  const [newComment, setNewComment] = useState('');
  const [localReviews, setLocalReviews] = useState([
    {
      id: 'rev-1',
      author: 'Rohit K.',
      rating: 5,
      date: '2 days ago',
      title: 'Flawless fit and luxury fabric weight!',
      comment: 'The mineral wash gives it such a nice vintage patina. Heavyweight cotton feels like an expensive Tokyo designer brand.',
      verified: true
    },
    {
      id: 'rev-2',
      author: 'Deepika S.',
      rating: 5,
      date: '1 week ago',
      title: 'Arya chic perfection',
      comment: 'Super breathable and gets compliments every time I wear it. Doorstep delivery was quick too.',
      verified: true
    }
  ]);

  if (!product) {
    return (
      <div className="py-20 text-center">
        <h2 className="text-xl font-bold">Product not found</h2>
        <button onClick={() => navigateTo('shop')} className="mt-4 text-orange-600 underline">
          Back to Catalogue
        </button>
      </div>
    );
  }

  const isFavorited = isInWishlist(product.id);
  const currentSizeObj = product.sizes?.find(s => s.size === selectedSize);
  const isLowStock = currentSizeObj && currentSizeObj.stock > 0 && currentSizeObj.stock <= 5;
  const isOutOfStock = currentSizeObj && currentSizeObj.stock === 0;

  const handlePincodeCheck = (e) => {
    e.preventDefault();
    if (!pincode || pincode.length !== 6) {
      addToast('Please enter a valid 6-digit Indian Pincode', 'error');
      return;
    }
    const days = pincode.startsWith('56') || pincode.startsWith('40') || pincode.startsWith('11') ? 2 : 4;
    setDeliveryStatus({
      available: true,
      message: `Express Delivery available in ${days} days (by ${new Date(Date.now() + days * 24 * 60 * 60 * 1000).toLocaleDateString('en-IN', { weekday: 'short', month: 'short', day: 'numeric' })})`,
      cod: true
    });
    addToast('Pincode verified! Free delivery available.', 'success');
  };

  const handleAddReview = (e) => {
    e.preventDefault();
    if (!newAuthor || !newComment) return;
    const newRev = {
      id: 'rev-' + Date.now(),
      author: newAuthor,
      rating: newRating,
      date: 'Just now',
      title: newTitle || 'Great purchase!',
      comment: newComment,
      verified: true
    };
    setLocalReviews([newRev, ...localReviews]);
    setShowReviewModal(false);
    setNewAuthor('');
    setNewTitle('');
    setNewComment('');
    addToast('Thank you! Your verified review is now live.', 'success');
  };

  const handleBuyNow = () => {
    addToCart(product, selectedSize, selectedColor, quantity);
    navigateTo('checkout');
  };

  const relatedProducts = products
    .filter(p => p.id !== product.id && (p.category === product.category || p.gender === product.gender))
    .slice(0, 4);

  return (
    <div className="py-8 sm:py-12 bg-[#FAFAF9] animate-fade-in min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumb Trail */}
        <nav className="flex items-center gap-2 text-xs text-stone-500 mb-6">
          <button onClick={() => navigateTo('home')} className="hover:text-stone-900">Home</button>
          <span>/</span>
          <button onClick={() => navigateTo('shop', { category: product.category })} className="hover:text-stone-900 capitalize">
            {product.category}
          </button>
          <span>/</span>
          <span className="font-semibold text-stone-900 truncate max-w-xs">{product.name}</span>
        </nav>

        {/* Product Hero Layout: Gallery + Purchasing Details */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
          
          {/* Left: Multi-Image Showcase Gallery */}
          <div className="lg:col-span-7 flex flex-col-reverse md:flex-row gap-4">
            
            {/* Thumbnails list */}
            {product.images?.length > 1 && (
              <div className="flex md:flex-col gap-3 overflow-x-auto hide-scrollbar shrink-0">
                {product.images.map((img, idx) => (
                  <button
                    key={idx}
                    onClick={() => setActiveImageIndex(idx)}
                    className={`w-16 h-20 sm:w-20 sm:h-24 rounded-2xl overflow-hidden border-2 transition-all shrink-0 bg-white ${
                      activeImageIndex === idx ? 'border-orange-600 shadow-md scale-105' : 'border-stone-200 opacity-70 hover:opacity-100'
                    }`}
                  >
                    <img src={img} alt="Thumbnail" className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            )}

            {/* Main Stage Image with Zoom effect on hover */}
            <div className="flex-1 relative rounded-3xl overflow-hidden bg-white border border-stone-200/80 shadow-md aspect-[3/4] group">
              <img
                src={product.images[activeImageIndex] || product.images[0]}
                alt={product.name}
                className="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-110 cursor-zoom-in"
              />

              {/* Badges on main image */}
              <div className="absolute top-4 left-4 flex flex-col gap-1.5 pointer-events-none">
                {product.discountPercent > 0 && (
                  <span className="bg-orange-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg">
                    {product.discountPercent}% OFF
                  </span>
                )}
                {product.isBestseller && (
                  <span className="bg-[#0F172A] text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">
                    Bestseller
                  </span>
                )}
              </div>
            </div>

          </div>

          {/* Right: Purchasing Configuration & Information */}
          <div className="lg:col-span-5 space-y-6">
            
            <div>
              {/* Category, SKU & Wishlist */}
              <div className="flex items-center justify-between gap-4 text-xs text-stone-500 mb-2">
                <span className="font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-full">
                  {product.category}
                </span>
                <span className="font-mono text-stone-400">SKU: {product.sku}</span>
              </div>

              {/* Title */}
              <h1 className="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#0F172A] leading-tight">
                {product.name}
              </h1>

              {/* Ratings */}
              <div className="flex items-center gap-3 mt-3">
                <div className="flex text-amber-400">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-amber-400" />
                  ))}
                </div>
                <span className="text-xs font-bold text-stone-800">{product.rating} / 5.0</span>
                <span className="text-xs text-stone-400">&bull;</span>
                <button 
                  onClick={() => setOpenAccordion('reviews')}
                  className="text-xs text-stone-500 underline hover:text-orange-600"
                >
                  {localReviews.length} Verified Customer Reviews
                </button>
              </div>

              {/* Price Row */}
              <div className="flex items-baseline gap-3 mt-4 pt-4 border-t border-stone-200/80">
                <span className="text-3xl font-black text-[#0F172A]">
                  ₹{product.price.toLocaleString()}
                </span>
                {product.originalPrice > product.price && (
                  <span className="text-lg text-stone-400 line-through">
                    ₹{product.originalPrice.toLocaleString()}
                  </span>
                )}
                {product.discountPercent > 0 && (
                  <span className="text-xs font-extrabold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-full">
                    Save ₹{(product.originalPrice - product.price).toLocaleString()} ({product.discountPercent}% OFF)
                  </span>
                )}
              </div>
              <p className="text-[11px] text-stone-400 mt-1">Inclusive of all taxes &bull; Free express delivery above ₹999</p>
            </div>

            {/* Color Swatch Selection */}
            {product.colors?.length > 0 && (
              <div>
                <span className="text-xs font-bold text-stone-900 block mb-2">
                  Color: <strong className="text-orange-600">{selectedColor}</strong>
                </span>
                <div className="flex gap-2.5">
                  {product.colors.map((c) => (
                    <button
                      key={c.name}
                      onClick={() => setSelectedColor(c.name)}
                      className={`flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold transition-all ${
                        selectedColor === c.name 
                          ? 'border-orange-600 bg-orange-50/50 text-orange-700 shadow-sm' 
                          : 'border-stone-200 bg-white text-stone-700 hover:border-stone-400'
                      }`}
                    >
                      <span className="w-3.5 h-3.5 rounded-full border border-black/10" style={{ backgroundColor: c.hex }}></span>
                      <span>{c.name}</span>
                    </button>
                  ))}
                </div>
              </div>
            )}

            {/* Size Selector & Stock Warning */}
            <div>
              <div className="flex items-center justify-between text-xs mb-2">
                <span className="font-bold text-stone-900">
                  Select Size {selectedSize && <span className="text-orange-600 font-extrabold">({selectedSize})</span>}
                </span>
                <button
                  type="button"
                  onClick={() => setIsSizeChartOpen(true)}
                  className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1"
                >
                  <Ruler className="w-3.5 h-3.5" />
                  <span>Size Chart &amp; Fit Guide</span>
                </button>
              </div>

              <div className="flex flex-wrap gap-2.5">
                {product.sizes?.map((s) => {
                  const outOfStock = s.stock === 0;
                  const isSelected = selectedSize === s.size;
                  return (
                    <button
                      key={s.size}
                      disabled={outOfStock}
                      onClick={() => setSelectedSize(s.size)}
                      className={`min-w-[48px] h-12 px-3 text-xs font-bold rounded-2xl border transition-all flex flex-col items-center justify-center ${
                        outOfStock
                          ? 'border-stone-200 bg-stone-100 text-stone-300 cursor-not-allowed line-through'
                          : isSelected
                            ? 'border-[#0F172A] bg-[#0F172A] text-white shadow-md scale-105'
                            : 'border-stone-200 bg-white text-stone-800 hover:border-stone-400'
                      }`}
                    >
                      <span>{s.size}</span>
                      {!outOfStock && s.stock <= 4 && (
                        <span className="text-[9px] text-amber-400 font-normal">
                          {s.stock} left
                        </span>
                      )}
                    </button>
                  );
                })}
              </div>

              {/* Dynamic Stock Indicator Badge */}
              {isLowStock && (
                <p className="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-2 mt-2 font-medium flex items-center gap-1.5">
                  <Sparkles className="w-3.5 h-3.5 text-amber-600" />
                  <span>Hurry! Only {currentSizeObj.stock} units left in Size {selectedSize}.</span>
                </p>
              )}
            </div>

            {/* Quantity Selector & Action Buttons */}
            <div className="space-y-3 pt-2">
              <div className="flex items-center gap-3">
                <div className="flex items-center bg-white rounded-2xl border border-stone-300 px-3 py-2">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="text-stone-500 hover:text-stone-900 font-bold px-2"
                  >
                    -
                  </button>
                  <span className="w-8 text-center text-xs font-bold text-stone-900">{quantity}</span>
                  <button
                    onClick={() => setQuantity(quantity + 1)}
                    className="text-stone-500 hover:text-stone-900 font-bold px-2"
                  >
                    +
                  </button>
                </div>

                <button
                  type="button"
                  onClick={() => addToCart(product, selectedSize, selectedColor, quantity)}
                  disabled={isOutOfStock}
                  className="flex-1 bg-orange-600 hover:bg-orange-500 disabled:bg-stone-300 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
                >
                  <ShoppingBag className="w-4 h-4" />
                  <span>{isOutOfStock ? 'Sold Out' : 'Add to Bag'}</span>
                </button>

                <button
                  type="button"
                  onClick={() => toggleWishlist(product.id)}
                  className={`p-4 rounded-2xl border transition-all ${
                    isFavorited 
                      ? 'border-rose-300 bg-rose-50 text-rose-600' 
                      : 'border-stone-300 bg-white text-stone-700 hover:bg-stone-50'
                  }`}
                  title="Wishlist"
                >
                  <Heart className={`w-5 h-5 ${isFavorited ? 'fill-rose-600' : ''}`} />
                </button>
              </div>

              <button
                type="button"
                onClick={handleBuyNow}
                disabled={isOutOfStock}
                className="w-full bg-[#0F172A] hover:bg-stone-800 disabled:bg-stone-300 text-white font-bold py-4 rounded-2xl shadow transition-all flex items-center justify-center gap-2"
              >
                <Zap className="w-4 h-4 text-amber-400 fill-amber-400" />
                <span>Buy Now &bull; Instant Fast Checkout</span>
              </button>
            </div>

            {/* Pincode Estimator Box */}
            <div className="bg-white p-4 rounded-2xl border border-stone-200/80 space-y-2">
              <span className="text-xs font-bold text-stone-900 flex items-center gap-1.5">
                <MapPin className="w-3.5 h-3.5 text-orange-600" />
                <span>Check Delivery &amp; COD Availability</span>
              </span>
              <form onSubmit={handlePincodeCheck} className="flex gap-2">
                <input
                  type="text"
                  maxLength={6}
                  value={pincode}
                  onChange={(e) => setPincode(e.target.value.replace(/\D/g, ''))}
                  placeholder="Enter 6-digit Pincode (e.g. 560034)"
                  className="flex-1 bg-stone-50 border border-stone-200 text-xs px-3 py-2 rounded-xl focus:outline-none focus:border-orange-500 font-mono"
                />
                <button
                  type="submit"
                  className="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors shrink-0"
                >
                  Check
                </button>
              </form>
              {deliveryStatus && (
                <p className="text-xs text-emerald-700 font-medium pt-1">
                  ✓ {deliveryStatus.message}
                </p>
              )}
            </div>

            {/* Product Accordions */}
            <div className="space-y-2 pt-2 border-t border-stone-200">
              {/* Description */}
              <div className="border border-stone-200 rounded-2xl overflow-hidden bg-white">
                <button
                  onClick={() => setOpenAccordion(openAccordion === 'desc' ? null : 'desc')}
                  className="w-full px-5 py-3.5 text-left text-xs font-bold text-stone-900 flex items-center justify-between"
                >
                  <span>Description &amp; Fit Guide</span>
                  {openAccordion === 'desc' ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                </button>
                {openAccordion === 'desc' && (
                  <div className="px-5 pb-4 text-xs text-stone-600 space-y-2 border-t border-stone-100 pt-3">
                    <p>{product.description}</p>
                    <p><strong>Fit:</strong> {product.fit}</p>
                  </div>
                )}
              </div>

              {/* Fabric & Care */}
              <div className="border border-stone-200 rounded-2xl overflow-hidden bg-white">
                <button
                  onClick={() => setOpenAccordion(openAccordion === 'fabric' ? null : 'fabric')}
                  className="w-full px-5 py-3.5 text-left text-xs font-bold text-stone-900 flex items-center justify-between"
                >
                  <span>Fabric Specifications &amp; Care</span>
                  {openAccordion === 'fabric' ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                </button>
                {openAccordion === 'fabric' && (
                  <div className="px-5 pb-4 text-xs text-stone-600 space-y-2 border-t border-stone-100 pt-3">
                    <p><strong>Composition:</strong> {product.fabricDetails}</p>
                    <p><strong>Care Instructions:</strong> {product.careInstructions}</p>
                  </div>
                )}
              </div>

              {/* Shipping & Easy Returns */}
              <div className="border border-stone-200 rounded-2xl overflow-hidden bg-white">
                <button
                  onClick={() => setOpenAccordion(openAccordion === 'shipping' ? null : 'shipping')}
                  className="w-full px-5 py-3.5 text-left text-xs font-bold text-stone-900 flex items-center justify-between"
                >
                  <span>Shipping, COD &amp; 7-Day Returns</span>
                  {openAccordion === 'shipping' ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                </button>
                {openAccordion === 'shipping' && (
                  <div className="px-5 pb-4 text-xs text-stone-600 space-y-2 border-t border-stone-100 pt-3">
                    <p>&bull; <strong>Free Air Express Shipping</strong> on all orders above ₹999.</p>
                    <p>&bull; <strong>Cash On Delivery (COD)</strong> available across 19,000+ pin codes.</p>
                    <p>&bull; <strong>7-Day Doorstep Exchange:</strong> Hassle-free size exchange with reverse pickup.</p>
                  </div>
                )}
              </div>

              {/* Customer Reviews Accordion */}
              <div className="border border-stone-200 rounded-2xl overflow-hidden bg-white">
                <button
                  onClick={() => setOpenAccordion(openAccordion === 'reviews' ? null : 'reviews')}
                  className="w-full px-5 py-3.5 text-left text-xs font-bold text-stone-900 flex items-center justify-between"
                >
                  <span>Customer Reviews ({localReviews.length})</span>
                  {openAccordion === 'reviews' ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                </button>
                {openAccordion === 'reviews' && (
                  <div className="px-5 pb-4 space-y-4 border-t border-stone-100 pt-4">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <span className="text-xl font-bold font-serif text-stone-900">{product.rating}</span>
                        <div className="flex text-amber-400">
                          {[...Array(5)].map((_, i) => (
                            <Star key={i} className="w-3.5 h-3.5 fill-amber-400" />
                          ))}
                        </div>
                      </div>
                      <button
                        onClick={() => setShowReviewModal(true)}
                        className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1"
                      >
                        <MessageSquarePlus className="w-3.5 h-3.5" />
                        <span>Write a Review</span>
                      </button>
                    </div>

                    <div className="space-y-3">
                      {localReviews.map((r) => (
                        <div key={r.id} className="p-3 rounded-xl bg-stone-50 text-xs space-y-1">
                          <div className="flex items-center justify-between">
                            <div className="flex items-center gap-1.5">
                              <span className="font-bold text-stone-900">{r.author}</span>
                              <span className="text-[10px] text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded font-semibold">Verified</span>
                            </div>
                            <span className="text-[10px] text-stone-400">{r.date}</span>
                          </div>
                          <p className="font-semibold text-stone-800">{r.title}</p>
                          <p className="text-stone-600">{r.comment}</p>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>

            </div>

          </div>

        </div>

        {/* You May Also Like / Related Drops */}
        {relatedProducts.length > 0 && (
          <div className="mt-20 pt-12 border-t border-stone-200">
            <div className="flex items-center justify-between mb-8">
              <div>
                <span className="text-xs font-bold text-orange-600 uppercase tracking-wider">Complete The Look</span>
                <h3 className="font-serif text-2xl font-bold text-stone-900">Similar Silhouettes</h3>
              </div>
              <button
                onClick={() => navigateTo('shop', { category: product.category })}
                className="text-xs font-bold text-stone-700 hover:text-orange-600 flex items-center gap-1"
              >
                <span>View Department</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {relatedProducts.map((p) => (
                <ProductCard key={p.id} product={p} />
              ))}
            </div>
          </div>
        )}

      </div>

      {/* Write a Review Modal */}
      {showReviewModal && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center">
          <div className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" onClick={() => setShowReviewModal(false)}></div>
          <div className="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl z-10 animate-scale-in">
            <h3 className="font-serif text-lg font-bold text-stone-900 mb-1">Write a Review</h3>
            <p className="text-xs text-stone-500 mb-4">Share your feedback on {product.name}</p>

            <form onSubmit={handleAddReview} className="space-y-3">
              <div>
                <label className="block text-xs font-bold text-stone-700 mb-1">Your Rating</label>
                <div className="flex gap-2">
                  {[1, 2, 3, 4, 5].map((num) => (
                    <button
                      type="button"
                      key={num}
                      onClick={() => setNewRating(num)}
                      className="p-1"
                    >
                      <Star className={`w-6 h-6 ${num <= newRating ? 'fill-amber-400 text-amber-400' : 'text-stone-300'}`} />
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-stone-700 mb-1">Your Name</label>
                <input
                  type="text"
                  value={newAuthor}
                  onChange={(e) => setNewAuthor(e.target.value)}
                  placeholder="e.g. Vikram Sharma"
                  className="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-orange-500"
                  required
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-stone-700 mb-1">Headline</label>
                <input
                  type="text"
                  value={newTitle}
                  onChange={(e) => setNewTitle(e.target.value)}
                  placeholder="e.g. Premium feel and true to size!"
                  className="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-orange-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-stone-700 mb-1">Your Review</label>
                <textarea
                  rows={3}
                  value={newComment}
                  onChange={(e) => setNewComment(e.target.value)}
                  placeholder="Tell others about the fabric, stitching, and comfort..."
                  className="w-full bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-orange-500"
                  required
                />
              </div>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  className="flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-2.5 rounded-xl text-xs shadow"
                >
                  Submit Verified Review
                </button>
                <button
                  type="button"
                  onClick={() => setShowReviewModal(false)}
                  className="px-4 py-2.5 rounded-xl border border-stone-200 text-xs font-semibold text-stone-600 hover:bg-stone-50"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
