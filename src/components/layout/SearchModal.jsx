import React, { useState, useEffect, useRef } from 'react';
import { Search, X, ArrowRight, TrendingUp, Sparkles } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const SearchModal = () => {
  const { isSearchOpen, setIsSearchOpen, products, navigateTo } = useStore();
  const [searchTerm, setSearchTerm] = useState('');
  const inputRef = useRef(null);

  useEffect(() => {
    if (isSearchOpen) {
      setTimeout(() => inputRef.current?.focus(), 50);
    }
  }, [isSearchOpen]);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setIsSearchOpen(true);
      }
      if (e.key === 'Escape' && isSearchOpen) {
        setIsSearchOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isSearchOpen, setIsSearchOpen]);

  if (!isSearchOpen) return null;

  const popularTags = ['Oversized Tee', 'Linen Co-Ord', 'Kurta Set', 'Cargoes', 'Hoodie', 'Satin Dress', 'Chikankari'];

  const filteredProducts = searchTerm.trim() === '' 
    ? [] 
    : products.filter(p => {
        const query = searchTerm.toLowerCase();
        return (
          p.name.toLowerCase().includes(query) ||
          p.category.toLowerCase().includes(query) ||
          (p.subCategory && p.subCategory.toLowerCase().includes(query)) ||
          p.tags?.some(tag => tag.toLowerCase().includes(query))
        );
      });

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-md transition-opacity"
        onClick={() => setIsSearchOpen(false)}
      ></div>

      {/* Modal Dialog */}
      <div className="relative mx-auto max-w-2xl bg-white rounded-3xl shadow-2xl border border-stone-100 overflow-hidden animate-scale-in">
        
        {/* Search Input Bar */}
        <div className="flex items-center px-6 py-4 border-b border-stone-100 bg-stone-50/50">
          <Search className="w-5 h-5 text-stone-400 shrink-0 mr-3" />
          <input
            ref={inputRef}
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search products, categories, styles (e.g. linen, oversized, kurta)..."
            className="w-full bg-transparent text-stone-900 placeholder-stone-400 text-base font-medium focus:outline-none"
          />
          {searchTerm && (
            <button 
              onClick={() => setSearchTerm('')}
              className="text-xs font-semibold text-stone-400 hover:text-stone-700 mr-2"
            >
              Clear
            </button>
          )}
          <button 
            onClick={() => setIsSearchOpen(false)}
            className="p-1 rounded-full text-stone-400 hover:text-stone-900 hover:bg-stone-200/60 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-6 max-h-[65vh] overflow-y-auto">
          
          {/* Trending suggestions if search is empty */}
          {searchTerm.trim() === '' ? (
            <div className="space-y-6">
              <div>
                <div className="flex items-center gap-2 text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">
                  <TrendingUp className="w-4 h-4 text-orange-500" />
                  <span>Popular Searches</span>
                </div>
                <div className="flex flex-wrap gap-2">
                  {popularTags.map((tag) => (
                    <button
                      key={tag}
                      onClick={() => setSearchTerm(tag)}
                      className="px-3.5 py-1.5 rounded-full bg-stone-100 hover:bg-orange-50 hover:text-orange-600 text-stone-700 text-xs font-medium transition-all"
                    >
                      {tag}
                    </button>
                  ))}
                </div>
              </div>

              <div>
                <div className="flex items-center gap-2 text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">
                  <Sparkles className="w-4 h-4 text-amber-500" />
                  <span>Featured Highlights</span>
                </div>
                <div className="grid grid-cols-2 gap-3">
                  {products.slice(0, 2).map((p) => (
                    <div
                      key={p.id}
                      onClick={() => {
                        setIsSearchOpen(false);
                        navigateTo('product', { slug: p.slug });
                      }}
                      className="flex items-center gap-3 p-2.5 rounded-2xl bg-stone-50 hover:bg-stone-100/80 cursor-pointer transition-all border border-stone-100"
                    >
                      <img src={p.images[0]} alt={p.name} className="w-14 h-16 object-cover rounded-xl shrink-0" />
                      <div className="min-w-0">
                        <p className="text-xs font-bold text-stone-900 truncate">{p.name}</p>
                        <p className="text-xs text-orange-600 font-bold mt-0.5">₹{p.price.toLocaleString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ) : filteredProducts.length > 0 ? (
            <div>
              <p className="text-xs font-semibold text-stone-500 mb-3">
                Found {filteredProducts.length} results for "{searchTerm}"
              </p>
              <div className="space-y-3">
                {filteredProducts.map((p) => (
                  <div
                    key={p.id}
                    onClick={() => {
                      setIsSearchOpen(false);
                      navigateTo('product', { slug: p.slug });
                    }}
                    className="flex items-center justify-between p-3 rounded-2xl bg-stone-50 hover:bg-orange-50/50 hover:border-orange-200 border border-stone-100 cursor-pointer transition-all group"
                  >
                    <div className="flex items-center gap-4 min-w-0">
                      <img src={p.images[0]} alt={p.name} className="w-14 h-16 object-cover rounded-xl shrink-0" />
                      <div className="min-w-0">
                        <h4 className="text-sm font-bold text-stone-900 truncate group-hover:text-orange-600 transition-colors">
                          {p.name}
                        </h4>
                        <div className="flex items-center gap-2 mt-1">
                          <span className="text-xs font-bold text-stone-900">₹{p.price.toLocaleString()}</span>
                          {p.originalPrice > p.price && (
                            <span className="text-xs text-stone-400 line-through">₹{p.originalPrice.toLocaleString()}</span>
                          )}
                          <span className="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">
                            {p.discountPercent}% OFF
                          </span>
                        </div>
                      </div>
                    </div>
                    <ArrowRight className="w-4 h-4 text-stone-400 group-hover:text-orange-600 group-hover:translate-x-1 transition-all shrink-0 ml-2" />
                  </div>
                ))}
              </div>
            </div>
          ) : (
            <div className="text-center py-10">
              <p className="text-sm font-bold text-stone-800">No styles found for "{searchTerm}"</p>
              <p className="text-xs text-stone-500 mt-1">Try checking for spelling or searching broader terms like "shirt", "linen", or "dress".</p>
            </div>
          )}

        </div>

        {/* Footer info */}
        <div className="px-6 py-3 bg-stone-50 border-t border-stone-100 flex items-center justify-between text-xs text-stone-400">
          <span>Tip: Press <kbd className="font-mono bg-white px-1.5 py-0.5 rounded border border-stone-200 text-stone-600">Esc</kbd> to close</span>
          <button
            onClick={() => {
              setIsSearchOpen(false);
              navigateTo('shop', { search: searchTerm });
            }}
            className="text-xs font-bold text-orange-600 hover:underline"
          >
            View all in Catalogue &rarr;
          </button>
        </div>

      </div>
    </div>
  );
};
