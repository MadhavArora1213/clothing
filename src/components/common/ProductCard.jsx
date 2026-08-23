import React, { useState } from 'react';
import { Heart, Eye, ShoppingBag, Star, Check } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const ProductCard = ({ product }) => {
  const { navigateTo, toggleWishlist, isInWishlist, addToCart, setQuickViewProduct } = useStore();
  const [isHovered, setIsHovered] = useState(false);
  const [selectedQuickSize, setSelectedQuickSize] = useState(null);
  const [isAdding, setIsAdding] = useState(false);

  const isFavorited = isInWishlist(product.id);
  const primaryImage = product.images[0];
  const secondaryImage = product.images[1] || product.images[0];

  const handleQuickAdd = (e, size) => {
    e.stopPropagation();
    setIsAdding(true);
    addToCart(product, size);
    setTimeout(() => setIsAdding(false), 800);
  };

  return (
    <div 
      className="group relative flex flex-col rounded-3xl bg-white border border-stone-100/80 hover:border-stone-200/90 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
    >
      {/* Product Image Container */}
      <div 
        className="relative aspect-[3/4] w-full overflow-hidden bg-stone-100 cursor-pointer"
        onClick={() => navigateTo('product', { slug: product.slug })}
      >
        {/* Main Image */}
        <img
          src={isHovered ? secondaryImage : primaryImage}
          alt={product.name}
          className="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
          loading="lazy"
        />

        {/* Badges */}
        <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
          {product.discountPercent > 0 && (
            <span className="bg-orange-600 text-white text-[11px] font-extrabold px-2.5 py-1 rounded-full shadow-md">
              {product.discountPercent}% OFF
            </span>
          )}
          {product.isBestseller && (
            <span className="bg-[#0F172A] text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
              Bestseller
            </span>
          )}
          {product.isNewArrival && (
            <span className="bg-amber-400 text-stone-900 text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
              New Drop
            </span>
          )}
        </div>

        {/* Action Buttons: Wishlist & QuickView */}
        <div className="absolute top-3 right-3 flex flex-col gap-2 z-10">
          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              toggleWishlist(product.id);
            }}
            className={`w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 shadow-md ${
              isFavorited
                ? 'bg-rose-50 text-rose-600 scale-105'
                : 'bg-white/90 text-stone-700 hover:bg-white hover:text-rose-600 backdrop-blur-sm'
            }`}
            title={isFavorited ? 'Remove from wishlist' : 'Save to wishlist'}
          >
            <Heart className={`w-4 h-4 ${isFavorited ? 'fill-rose-600' : ''}`} />
          </button>

          <button
            type="button"
            onClick={(e) => {
              e.stopPropagation();
              setQuickViewProduct(product);
            }}
            className="w-9 h-9 rounded-full bg-white/90 text-stone-700 hover:bg-white hover:text-[#0F172A] backdrop-blur-sm flex items-center justify-center transition-all duration-200 shadow-md opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0"
            title="Quick View"
          >
            <Eye className="w-4 h-4" />
          </button>
        </div>

        {/* Hover Quick Size Selector Bar */}
        <div className="absolute inset-x-3 bottom-3 z-10 transition-all duration-300 opacity-0 translate-y-3 group-hover:opacity-100 group-hover:translate-y-0">
          <div className="bg-white/95 backdrop-blur-md rounded-2xl p-2.5 shadow-lg border border-stone-200/80">
            <div className="flex items-center justify-between mb-1.5 px-1">
              <span className="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Quick Add Size</span>
              <span className="text-[10px] font-semibold text-emerald-600">In Stock</span>
            </div>
            <div className="flex items-center gap-1.5 justify-between">
              {product.sizes?.map((sizeObj) => {
                const isOutOfStock = sizeObj.stock === 0;
                return (
                  <button
                    key={sizeObj.size}
                    disabled={isOutOfStock}
                    onClick={(e) => handleQuickAdd(e, sizeObj.size)}
                    className={`flex-1 py-1 text-xs font-bold rounded-lg border transition-all ${
                      isOutOfStock
                        ? 'border-stone-200 text-stone-300 cursor-not-allowed bg-stone-50 line-through'
                        : 'border-stone-200 bg-white hover:border-orange-600 hover:bg-orange-50 hover:text-orange-600 text-stone-800'
                    }`}
                  >
                    {sizeObj.size}
                  </button>
                );
              })}
            </div>
          </div>
        </div>

      </div>

      {/* Product Content Details */}
      <div className="p-4 flex flex-col flex-1 justify-between bg-white">
        <div>
          {/* Category & Rating */}
          <div className="flex items-center justify-between gap-2 text-xs mb-1.5">
            <span className="text-[11px] font-bold uppercase tracking-wider text-orange-600 truncate">
              {product.category}
            </span>
            <div className="flex items-center gap-1 bg-amber-50 px-1.5 py-0.5 rounded text-amber-900 font-bold text-[10px]">
              <Star className="w-3 h-3 fill-amber-400 text-amber-400" />
              <span>{product.rating}</span>
              <span className="text-stone-400">({product.reviewCount})</span>
            </div>
          </div>

          {/* Product Title */}
          <h3 
            onClick={() => navigateTo('product', { slug: product.slug })}
            className="font-semibold text-sm text-stone-900 line-clamp-2 hover:text-orange-600 transition-colors cursor-pointer leading-snug"
          >
            {product.name}
          </h3>
        </div>

        {/* Price Row */}
        <div className="pt-3 border-t border-stone-100 flex items-center justify-between mt-3">
          <div className="flex items-baseline gap-2">
            <span className="text-base font-extrabold text-[#0F172A]">
              ₹{product.price.toLocaleString()}
            </span>
            {product.originalPrice > product.price && (
              <span className="text-xs text-stone-400 line-through">
                ₹{product.originalPrice.toLocaleString()}
              </span>
            )}
          </div>

          <button
            onClick={() => navigateTo('product', { slug: product.slug })}
            className="text-xs font-bold text-stone-700 hover:text-orange-600 flex items-center gap-1 transition-colors"
          >
            Details &rarr;
          </button>
        </div>
      </div>
    </div>
  );
};
