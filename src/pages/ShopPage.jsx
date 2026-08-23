import React, { useState, useMemo } from 'react';
import { 
  Filter, 
  SlidersHorizontal, 
  X, 
  ChevronDown, 
  LayoutGrid, 
  List, 
  Star, 
  Sparkles, 
  Check, 
  RotateCcw 
} from 'lucide-react';
import { useStore } from '../context/StoreContext';
import { ProductCard } from '../components/common/ProductCard';

export const ShopPage = () => {
  const { products, categories, pageParams, wishlist } = useStore();

  // Filters State
  const [selectedCategory, setSelectedCategory] = useState(pageParams?.category || 'all');
  const [selectedGender, setSelectedGender] = useState('all');
  const [selectedSizes, setSelectedSizes] = useState([]);
  const [selectedColors, setSelectedColors] = useState([]);
  const [priceRange, setPriceRange] = useState(5000);
  const [minDiscount, setMinDiscount] = useState(0);
  const [inStockOnly, setInStockOnly] = useState(false);
  const [sortBy, setSortBy] = useState('featured');
  const [viewMode, setViewMode] = useState('grid'); // 'grid' | 'list'
  const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false);

  // Check if opened with special filters like wishlist or sale
  const isWishlistFilter = pageParams?.filter === 'wishlist';
  const isSaleFilter = pageParams?.filter === 'sale';
  const isNewFilter = pageParams?.filter === 'new';

  const allSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
  const colorOptions = [
    { name: 'Charcoal', hex: '#333333' },
    { name: 'Indigo', hex: '#1F3A60' },
    { name: 'Sand', hex: '#C2B280' },
    { name: 'Olive', hex: '#556B2F' },
    { name: 'Emerald', hex: '#00563B' },
    { name: 'Powder Blue', hex: '#B0E0E6' },
    { name: 'Terracotta', hex: '#CC4E33' },
    { name: 'White', hex: '#FFFFFF' }
  ];

  const toggleSize = (size) => {
    setSelectedSizes(prev => 
      prev.includes(size) ? prev.filter(s => s !== size) : [...prev, size]
    );
  };

  const toggleColor = (colorName) => {
    setSelectedColors(prev => 
      prev.includes(colorName) ? prev.filter(c => c !== colorName) : [...prev, colorName]
    );
  };

  const clearAllFilters = () => {
    setSelectedCategory('all');
    setSelectedGender('all');
    setSelectedSizes([]);
    setSelectedColors([]);
    setPriceRange(5000);
    setMinDiscount(0);
    setInStockOnly(false);
  };

  // Filter and Sort Logic
  const filteredProducts = useMemo(() => {
    return products.filter((product) => {
      // Wishlist special route
      if (isWishlistFilter && !wishlist.includes(product.id)) {
        return false;
      }

      // Sale special route
      if (isSaleFilter && product.discountPercent < 20) {
        return false;
      }

      // New arrivals route
      if (isNewFilter && !product.isNewArrival) {
        return false;
      }

      // Category filter
      if (selectedCategory !== 'all') {
        if (product.category !== selectedCategory) return false;
      }

      // Gender filter
      if (selectedGender !== 'all') {
        if (product.gender !== selectedGender && product.gender !== 'unisex') return false;
      }

      // Price slider
      if (product.price > priceRange) return false;

      // Min discount
      if (product.discountPercent < minDiscount) return false;

      // In Stock filter
      if (inStockOnly) {
        const totalStock = product.sizes?.reduce((sum, s) => sum + s.stock, 0) || 0;
        if (totalStock === 0) return false;
      }

      // Size filter
      if (selectedSizes.length > 0) {
        const hasSize = product.sizes?.some(s => selectedSizes.includes(s.size) && s.stock > 0);
        if (!hasSize) return false;
      }

      // Color filter
      if (selectedColors.length > 0) {
        const hasColor = product.colors?.some(c => 
          selectedColors.some(sc => c.name.toLowerCase().includes(sc.toLowerCase()))
        );
        if (!hasColor) return false;
      }

      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'newest') return (b.isNewArrival ? 1 : 0) - (a.isNewArrival ? 1 : 0);
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0; // featured
    });
  }, [
    products, 
    selectedCategory, 
    selectedGender, 
    selectedSizes, 
    selectedColors, 
    priceRange, 
    minDiscount, 
    inStockOnly, 
    sortBy, 
    isWishlistFilter, 
    isSaleFilter, 
    isNewFilter, 
    wishlist
  ]);

  const activeFilterCount = (selectedCategory !== 'all' ? 1 : 0) +
    (selectedGender !== 'all' ? 1 : 0) +
    selectedSizes.length +
    selectedColors.length +
    (minDiscount > 0 ? 1 : 0) +
    (inStockOnly ? 1 : 0) +
    (priceRange < 5000 ? 1 : 0);

  return (
    <div className="py-8 sm:py-12 bg-[#FAFAF9] min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Breadcrumbs & Header Banner */}
        <div className="mb-8">
          <div className="flex items-center gap-2 text-xs text-stone-500 mb-2">
            <span>Home</span>
            <span>/</span>
            <span className="font-semibold text-stone-900">
              {isWishlistFilter ? 'My Saved Wishlist' : isSaleFilter ? 'Sale Drops' : 'Catalogue'}
            </span>
          </div>

          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-stone-200/80 pb-6">
            <div>
              <h1 className="font-serif text-3xl sm:text-4xl font-black text-[#0F172A]">
                {isWishlistFilter 
                  ? 'Your Saved Wishlist' 
                  : isSaleFilter 
                    ? 'Special Sale & Discounts' 
                    : selectedCategory !== 'all' 
                      ? categories.find(c => c.id === selectedCategory)?.name || 'Collection'
                      : 'All Apparel & Drops'}
              </h1>
              <p className="text-xs text-stone-500 mt-1">
                Showing {filteredProducts.length} premium handcrafted &amp; streetwear pieces
              </p>
            </div>

            {/* Sorting & Layout Switcher */}
            <div className="flex items-center gap-3">
              {/* Mobile Filter Button */}
              <button
                onClick={() => setMobileFiltersOpen(true)}
                className="lg:hidden flex items-center gap-2 bg-white px-4 py-2.5 rounded-xl border border-stone-300 text-xs font-bold text-stone-800 shadow-sm"
              >
                <SlidersHorizontal className="w-3.5 h-3.5 text-orange-600" />
                <span>Filters {activeFilterCount > 0 && `(${activeFilterCount})`}</span>
              </button>

              {/* Sort By Dropdown */}
              <div className="flex items-center gap-2 bg-white px-3.5 py-2.5 rounded-xl border border-stone-300 shadow-sm">
                <span className="text-xs text-stone-500 font-medium hidden sm:inline">Sort by:</span>
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className="bg-transparent text-xs font-bold text-stone-900 focus:outline-none cursor-pointer"
                >
                  <option value="featured">Featured &amp; Recommended</option>
                  <option value="newest">Newest Drops First</option>
                  <option value="price-low">Price: Low to High</option>
                  <option value="price-high">Price: High to Low</option>
                  <option value="rating">Highest Customer Rating</option>
                </select>
              </div>

              {/* Grid / List View Toggle */}
              <div className="hidden sm:flex bg-white p-1 rounded-xl border border-stone-300 shadow-sm">
                <button
                  onClick={() => setViewMode('grid')}
                  className={`p-1.5 rounded-lg transition-colors ${viewMode === 'grid' ? 'bg-[#0F172A] text-white' : 'text-stone-500 hover:text-stone-900'}`}
                  title="Grid View"
                >
                  <LayoutGrid className="w-4 h-4" />
                </button>
                <button
                  onClick={() => setViewMode('list')}
                  className={`p-1.5 rounded-lg transition-colors ${viewMode === 'list' ? 'bg-[#0F172A] text-white' : 'text-stone-500 hover:text-stone-900'}`}
                  title="List View"
                >
                  <List className="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* Active Filters Pill Bar */}
        {activeFilterCount > 0 && (
          <div className="flex flex-wrap items-center gap-2 mb-6">
            <span className="text-xs font-bold text-stone-500">Active Filters:</span>
            {selectedCategory !== 'all' && (
              <span className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                Category: {selectedCategory}
                <button onClick={() => setSelectedCategory('all')} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            )}
            {selectedGender !== 'all' && (
              <span className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                Gender: {selectedGender}
                <button onClick={() => setSelectedGender('all')} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            )}
            {selectedSizes.map(sz => (
              <span key={sz} className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                Size: {sz}
                <button onClick={() => toggleSize(sz)} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            ))}
            {selectedColors.map(cl => (
              <span key={cl} className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                Color: {cl}
                <button onClick={() => toggleColor(cl)} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            ))}
            {minDiscount > 0 && (
              <span className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                {minDiscount}%+ Discount
                <button onClick={() => setMinDiscount(0)} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            )}
            {priceRange < 5000 && (
              <span className="inline-flex items-center gap-1.5 bg-white border border-stone-200 px-3 py-1 rounded-full text-xs font-semibold text-stone-800">
                Max ₹{priceRange}
                <button onClick={() => setPriceRange(5000)} className="hover:text-rose-600"><X className="w-3 h-3" /></button>
              </span>
            )}
            <button
              onClick={clearAllFilters}
              className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1 ml-2"
            >
              <RotateCcw className="w-3 h-3" />
              <span>Reset All</span>
            </button>
          </div>
        )}

        {/* Main Content Layout (Sidebar + Grid) */}
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          
          {/* Desktop Filter Sidebar */}
          <aside className="hidden lg:block lg:col-span-1 space-y-6">
            <div className="bg-white rounded-3xl p-6 border border-stone-200/80 shadow-sm space-y-6">
              
              {/* Category Filter */}
              <div>
                <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900 mb-3">
                  Department / Category
                </h4>
                <div className="space-y-1.5">
                  <button
                    onClick={() => setSelectedCategory('all')}
                    className={`w-full text-left px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors flex items-center justify-between ${
                      selectedCategory === 'all' ? 'bg-[#0F172A] text-white' : 'text-stone-700 hover:bg-stone-100'
                    }`}
                  >
                    <span>All Products</span>
                    <span className="text-[10px]">{products.length}</span>
                  </button>
                  {categories.map((c) => (
                    <button
                      key={c.id}
                      onClick={() => setSelectedCategory(c.id)}
                      className={`w-full text-left px-3 py-1.5 rounded-xl text-xs font-semibold transition-colors flex items-center justify-between ${
                        selectedCategory === c.id ? 'bg-[#0F172A] text-white' : 'text-stone-700 hover:bg-stone-100'
                      }`}
                    >
                      <span>{c.name}</span>
                      <span className="text-[10px]">
                        {products.filter(p => p.category === c.id).length}
                      </span>
                    </button>
                  ))}
                </div>
              </div>

              {/* Price Range Filter Slider */}
              <div className="pt-4 border-t border-stone-100">
                <div className="flex items-center justify-between mb-2">
                  <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900">
                    Max Price
                  </h4>
                  <span className="text-xs font-bold text-orange-600 font-mono">₹{priceRange.toLocaleString()}</span>
                </div>
                <input
                  type="range"
                  min="500"
                  max="5000"
                  step="100"
                  value={priceRange}
                  onChange={(e) => setPriceRange(Number(e.target.value))}
                  className="w-full accent-orange-600 cursor-pointer"
                />
                <div className="flex justify-between text-[10px] text-stone-400 font-mono mt-1">
                  <span>₹500</span>
                  <span>₹5,000</span>
                </div>
              </div>

              {/* Gender / Fit Filter */}
              <div className="pt-4 border-t border-stone-100">
                <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900 mb-3">
                  Gender &amp; Fit
                </h4>
                <div className="grid grid-cols-3 gap-1.5">
                  {['all', 'men', 'women'].map((g) => (
                    <button
                      key={g}
                      onClick={() => setSelectedGender(g)}
                      className={`py-1.5 text-xs font-bold rounded-xl capitalize transition-all border ${
                        selectedGender === g
                          ? 'border-[#0F172A] bg-[#0F172A] text-white'
                          : 'border-stone-200 text-stone-700 hover:bg-stone-50'
                      }`}
                    >
                      {g}
                    </button>
                  ))}
                </div>
              </div>

              {/* Sizes Filter */}
              <div className="pt-4 border-t border-stone-100">
                <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900 mb-3">
                  Sizes
                </h4>
                <div className="grid grid-cols-3 gap-2">
                  {allSizes.map((size) => {
                    const isSelected = selectedSizes.includes(size);
                    return (
                      <button
                        key={size}
                        onClick={() => toggleSize(size)}
                        className={`py-1.5 text-xs font-bold rounded-xl border transition-all ${
                          isSelected
                            ? 'border-orange-600 bg-orange-600 text-white shadow-sm'
                            : 'border-stone-200 text-stone-700 hover:border-stone-400 bg-white'
                        }`}
                      >
                        {size}
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Colors Swatches */}
              <div className="pt-4 border-t border-stone-100">
                <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900 mb-3">
                  Colors
                </h4>
                <div className="flex flex-wrap gap-2">
                  {colorOptions.map((c) => {
                    const isSelected = selectedColors.includes(c.name);
                    return (
                      <button
                        key={c.name}
                        onClick={() => toggleColor(c.name)}
                        className={`w-7 h-7 rounded-full border-2 transition-all relative flex items-center justify-center ${
                          isSelected ? 'border-orange-600 scale-110 shadow' : 'border-stone-300 hover:scale-105'
                        }`}
                        style={{ backgroundColor: c.hex }}
                        title={c.name}
                      >
                        {isSelected && (
                          <Check className={`w-3.5 h-3.5 ${c.hex === '#FFFFFF' || c.hex === '#FDFBF7' ? 'text-black' : 'text-white'}`} />
                        )}
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Discount Filter */}
              <div className="pt-4 border-t border-stone-100">
                <h4 className="text-xs font-bold uppercase tracking-wider text-stone-900 mb-2">
                  Minimum Discount
                </h4>
                <div className="space-y-1.5 text-xs font-semibold text-stone-700">
                  {[0, 20, 30, 40, 50].map((disc) => (
                    <label key={disc} className="flex items-center gap-2 cursor-pointer hover:text-orange-600">
                      <input
                        type="radio"
                        name="discount"
                        checked={minDiscount === disc}
                        onChange={() => setMinDiscount(disc)}
                        className="accent-orange-600"
                      />
                      <span>{disc === 0 ? 'All Items (No Min)' : `${disc}% OFF or more`}</span>
                    </label>
                  ))}
                </div>
              </div>

              {/* In Stock Only Checkbox */}
              <div className="pt-4 border-t border-stone-100">
                <label className="flex items-center gap-2 text-xs font-bold text-stone-800 cursor-pointer">
                  <input
                    type="checkbox"
                    checked={inStockOnly}
                    onChange={(e) => setInStockOnly(e.target.checked)}
                    className="w-4 h-4 rounded accent-orange-600"
                  />
                  <span>Exclude Out of Stock</span>
                </label>
              </div>

            </div>
          </aside>

          {/* Product Grid Area */}
          <div className="lg:col-span-3">
            {filteredProducts.length === 0 ? (
              <div className="bg-white rounded-3xl p-12 text-center border border-stone-200 space-y-4">
                <div className="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400">
                  <Sparkles className="w-8 h-8" />
                </div>
                <h3 className="font-serif text-xl font-bold text-stone-800">No styles match your filters</h3>
                <p className="text-xs text-stone-500 max-w-sm mx-auto">
                  Try widening your price range or clearing active size and color filters.
                </p>
                <button
                  onClick={clearAllFilters}
                  className="bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-6 py-3 rounded-full shadow transition-all"
                >
                  Clear All Filters
                </button>
              </div>
            ) : (
              <div className={`grid gap-6 ${
                viewMode === 'grid' 
                  ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' 
                  : 'grid-cols-1'
              }`}>
                {filteredProducts.map((product) => (
                  <ProductCard key={product.id} product={product} />
                ))}
              </div>
            )}
          </div>

        </div>

      </div>

      {/* Mobile Filter Drawer */}
      {mobileFiltersOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div 
            className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm"
            onClick={() => setMobileFiltersOpen(false)}
          ></div>
          <div className="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl z-50 p-6 flex flex-col justify-between overflow-y-auto animate-slide-in-right">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-stone-100">
                <h3 className="font-serif text-lg font-bold text-stone-900">Filters</h3>
                <button onClick={() => setMobileFiltersOpen(false)}><X className="w-5 h-5 text-stone-400" /></button>
              </div>

              {/* Mobile Filter Content */}
              <div className="py-4 space-y-6">
                <div>
                  <h4 className="text-xs font-bold uppercase text-stone-800 mb-2">Category</h4>
                  <select
                    value={selectedCategory}
                    onChange={(e) => setSelectedCategory(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 text-xs p-2.5 rounded-xl font-medium"
                  >
                    <option value="all">All Departments</option>
                    {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                </div>

                <div>
                  <h4 className="text-xs font-bold uppercase text-stone-800 mb-2">Max Price: ₹{priceRange}</h4>
                  <input
                    type="range"
                    min="500"
                    max="5000"
                    step="100"
                    value={priceRange}
                    onChange={(e) => setPriceRange(Number(e.target.value))}
                    className="w-full accent-orange-600"
                  />
                </div>

                <div>
                  <h4 className="text-xs font-bold uppercase text-stone-800 mb-2">Sizes</h4>
                  <div className="grid grid-cols-3 gap-2">
                    {allSizes.map(sz => (
                      <button
                        key={sz}
                        onClick={() => toggleSize(sz)}
                        className={`py-1.5 text-xs font-bold rounded-xl border ${
                          selectedSizes.includes(sz) ? 'bg-orange-600 text-white border-orange-600' : 'border-stone-200 bg-stone-50'
                        }`}
                      >
                        {sz}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            </div>

            <div className="pt-4 border-t border-stone-100 space-y-2">
              <button
                onClick={() => setMobileFiltersOpen(false)}
                className="w-full bg-orange-600 text-white font-bold py-3 rounded-xl text-xs shadow-md"
              >
                Apply Filters ({filteredProducts.length} items)
              </button>
              <button
                onClick={clearAllFilters}
                className="w-full bg-stone-100 text-stone-700 font-semibold py-2 rounded-xl text-xs"
              >
                Reset All
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
