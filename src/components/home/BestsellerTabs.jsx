import React, { useState } from 'react';
import { ArrowRight, Flame } from 'lucide-react';
import { useStore } from '../../context/StoreContext';
import { ProductCard } from '../common/ProductCard';

export const BestsellerTabs = () => {
  const { products, navigateTo } = useStore();
  const [activeTab, setActiveTab] = useState('all');

  const tabs = [
    { id: 'all', label: 'All Featured' },
    { id: 'oversized', label: 'Oversized Drops' },
    { id: 'ethnic-fusion', label: 'Arya Ethnic Fusion' },
    { id: 'co-ords', label: 'Co-Ord Sets' },
    { id: 'men', label: 'Men’s Fits' },
    { id: 'women', label: 'Women’s Edit' }
  ];

  const filteredProducts = activeTab === 'all' 
    ? products.slice(0, 8)
    : products.filter(p => p.category === activeTab || p.gender === activeTab).slice(0, 8);

  return (
    <section className="py-16 bg-[#FAFAF9]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header with Title & Filter Tabs */}
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6">
          <div>
            <div className="flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest mb-1.5">
              <Flame className="w-4 h-4 text-orange-600 fill-orange-600" />
              <span>Trending Right Now</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold text-[#0F172A]">
              Most Wanted Silhouettes
            </h2>
          </div>

          {/* Filter Pills */}
          <div className="flex flex-wrap gap-2 overflow-x-auto pb-1 hide-scrollbar">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`px-4 py-2 rounded-full text-xs font-bold transition-all whitespace-nowrap ${
                  activeTab === tab.id
                    ? 'bg-[#0F172A] text-white shadow-md'
                    : 'bg-white text-stone-600 hover:text-stone-900 border border-stone-200/80 hover:border-stone-300'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </div>
        </div>

        {/* Product Cards Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {filteredProducts.map((product) => (
            <ProductCard key={product.id} product={product} />
          ))}
        </div>

        {/* Bottom CTA */}
        <div className="mt-12 text-center">
          <button
            onClick={() => navigateTo('shop', activeTab !== 'all' ? { category: activeTab } : {})}
            className="inline-flex items-center gap-2 bg-white hover:bg-stone-100 text-stone-900 text-xs font-bold px-8 py-3.5 rounded-full border border-stone-300 shadow-sm transition-all group"
          >
            <span>View All {activeTab !== 'all' ? tabs.find(t => t.id === activeTab)?.label : 'Products'}</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </button>
        </div>

      </div>
    </section>
  );
};
