import React, { useState } from 'react';
import { X, Star, ShoppingBag, ArrowRight, ShieldCheck, Ruler, Heart } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const QuickViewModal = () => {
  const { 
    quickViewProduct, 
    setQuickViewProduct, 
    addToCart, 
    navigateTo, 
    toggleWishlist, 
    isInWishlist,
    setIsSizeChartOpen 
  } = useStore();

  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [selectedSize, setSelectedSize] = useState('M');
  const [selectedColor, setSelectedColor] = useState(null);

  if (!quickViewProduct) return null;

  const isFavorited = isInWishlist(quickViewProduct.id);
  const activeColor = selectedColor || quickViewProduct.colors?.[0]?.name;

  const handleAddToCart = () => {
    addToCart(quickViewProduct, selectedSize, activeColor);
    setQuickViewProduct(null);
  };

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-12 flex items-center justify-center">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
        onClick={() => setQuickViewProduct(null)}
      ></div>

      <div className="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-stone-100 overflow-hidden animate-scale-in">
        
        {/* Close Button */}
        <button 
          onClick={() => setQuickViewProduct(null)}
          className="absolute top-4 right-4 z-20 p-2 rounded-full bg-white/90 text-stone-700 hover:text-stone-900 hover:bg-white shadow transition-colors"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="grid grid-cols-1 md:grid-cols-2">
          
          {/* Left: Image Gallery */}
          <div className="bg-stone-100 p-6 flex flex-col justify-between">
            <div className="aspect-[3/4] w-full rounded-2xl overflow-hidden shadow bg-white">
              <img
                src={quickViewProduct.images[activeImageIndex] || quickViewProduct.images[0]}
                alt={quickViewProduct.name}
                className="w-full h-full object-cover object-center transition-all duration-300"
              />
            </div>

            {/* Thumbnail switcher */}
            {quickViewProduct.images.length > 1 && (
              <div className="flex gap-2 mt-4 justify-center">
                {quickViewProduct.images.map((img, idx) => (
                  <button
                    key={idx}
                    onClick={() => setActiveImageIndex(idx)}
                    className={`w-14 h-16 rounded-xl overflow-hidden border-2 transition-all ${
                      activeImageIndex === idx ? 'border-orange-600 shadow' : 'border-transparent opacity-70 hover:opacity-100'
                    }`}
                  >
                    <img src={img} alt="thumb" className="w-full h-full object-cover" />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Right: Details & Actions */}
          <div className="p-6 sm:p-8 flex flex-col justify-between space-y-6">
            <div>
              {/* Category & SKU */}
              <div className="flex items-center justify-between text-xs text-stone-500 mb-2">
                <span className="font-bold uppercase tracking-wider text-orange-600">
                  {quickViewProduct.category}
                </span>
                <span className="font-mono">{quickViewProduct.sku}</span>
              </div>

              {/* Title */}
              <h2 className="font-serif text-2xl font-bold text-stone-900 leading-tight">
                {quickViewProduct.name}
              </h2>

              {/* Rating */}
              <div className="flex items-center gap-2 mt-2">
                <div className="flex text-amber-400">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-amber-400" />
                  ))}
                </div>
                <span className="text-xs font-bold text-stone-800">{quickViewProduct.rating}</span>
                <span className="text-xs text-stone-400">({quickViewProduct.reviewCount} customer reviews)</span>
              </div>

              {/* Price */}
              <div className="flex items-baseline gap-3 mt-4">
                <span className="text-2xl font-extrabold text-[#0F172A]">
                  ₹{quickViewProduct.price.toLocaleString()}
                </span>
                {quickViewProduct.originalPrice > quickViewProduct.price && (
                  <span className="text-sm text-stone-400 line-through">
                    ₹{quickViewProduct.originalPrice.toLocaleString()}
                  </span>
                )}
                {quickViewProduct.discountPercent > 0 && (
                  <span className="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">
                    Save {quickViewProduct.discountPercent}%
                  </span>
                )}
              </div>

              {/* Short Bio */}
              <p className="text-xs text-stone-600 mt-4 leading-relaxed line-clamp-3">
                {quickViewProduct.description}
              </p>

              {/* Size Selector */}
              <div className="mt-6">
                <div className="flex items-center justify-between text-xs mb-2">
                  <span className="font-bold text-stone-900">Select Size</span>
                  <button
                    onClick={() => setIsSizeChartOpen(true)}
                    className="text-orange-600 hover:underline flex items-center gap-1 font-semibold"
                  >
                    <Ruler className="w-3.5 h-3.5" />
                    <span>Size Guide</span>
                  </button>
                </div>
                <div className="flex flex-wrap gap-2">
                  {quickViewProduct.sizes?.map((s) => {
                    const isOutOfStock = s.stock === 0;
                    return (
                      <button
                        key={s.size}
                        disabled={isOutOfStock}
                        onClick={() => setSelectedSize(s.size)}
                        className={`min-w-[44px] h-10 px-3 text-xs font-bold rounded-xl border transition-all ${
                          isOutOfStock
                            ? 'border-stone-200 text-stone-300 bg-stone-50 cursor-not-allowed line-through'
                            : selectedSize === s.size
                              ? 'border-[#0F172A] bg-[#0F172A] text-white shadow'
                              : 'border-stone-200 bg-white text-stone-800 hover:border-stone-400'
                        }`}
                      >
                        {s.size}
                      </button>
                    );
                  })}
                </div>
              </div>
            </div>

            {/* CTAs */}
            <div className="space-y-3 pt-4 border-t border-stone-100">
              <div className="flex gap-3">
                <button
                  onClick={handleAddToCart}
                  className="flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
                >
                  <ShoppingBag className="w-4 h-4" />
                  <span>Add to Bag</span>
                </button>

                <button
                  onClick={() => toggleWishlist(quickViewProduct.id)}
                  className={`p-3.5 rounded-2xl border transition-all ${
                    isFavorited 
                      ? 'border-rose-300 bg-rose-50 text-rose-600' 
                      : 'border-stone-200 text-stone-700 hover:bg-stone-50'
                  }`}
                  title="Wishlist"
                >
                  <Heart className={`w-5 h-5 ${isFavorited ? 'fill-rose-600' : ''}`} />
                </button>
              </div>

              <button
                onClick={() => {
                  setQuickViewProduct(null);
                  navigateTo('product', { slug: quickViewProduct.slug });
                }}
                className="w-full text-center text-xs font-bold text-stone-700 hover:text-orange-600 py-2 flex items-center justify-center gap-1.5 transition-colors"
              >
                <span>View Full Specifications &amp; Reviews</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </button>
            </div>

          </div>

        </div>

      </div>
    </div>
  );
};
