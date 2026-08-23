import React from 'react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const CategoryPills = () => {
  const { categories, navigateTo } = useStore();

  const curatedCategories = [
    {
      id: 'men',
      name: "Men's Streetwear",
      tag: 'Urban Essentials',
      image: 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1 md:col-span-2'
    },
    {
      id: 'women',
      name: "Women's Edit",
      tag: 'Contemporary Chic',
      image: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1'
    },
    {
      id: 'oversized',
      name: 'Oversized Drops',
      tag: '260+ GSM Heavyweight',
      image: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1'
    },
    {
      id: 'ethnic-fusion',
      name: 'Arya Ethnic Fusion',
      tag: 'Artisanal Chikankari & Kurtas',
      image: 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1 md:col-span-2'
    },
    {
      id: 'co-ords',
      name: 'Resort Co-Ords',
      tag: 'Breezy Linen Matching Sets',
      image: 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1'
    },
    {
      id: 'accessories',
      name: 'Bags & Accessories',
      tag: 'Heavy Canvas Totes & Caps',
      image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&auto=format&fit=crop&q=80',
      span: 'col-span-1'
    }
  ];

  return (
    <section className="py-16 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
          <div>
            <div className="flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest mb-1.5">
              <Sparkles className="w-3.5 h-3.5" />
              <span>Curated Departments</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold text-[#0F172A]">
              Explore by Category
            </h2>
          </div>

          <button
            onClick={() => navigateTo('shop')}
            className="inline-flex items-center gap-1.5 text-xs font-bold text-stone-700 hover:text-orange-600 group transition-colors"
          >
            <span>View All Categories &amp; Drops</span>
            <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
          </button>
        </div>

        {/* Category Bento Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          {curatedCategories.map((cat) => (
            <div
              key={cat.id}
              onClick={() => navigateTo('shop', { category: cat.id })}
              className={`group relative rounded-3xl overflow-hidden cursor-pointer shadow-sm hover:shadow-2xl transition-all duration-500 min-h-[260px] sm:min-h-[300px] ${cat.span}`}
            >
              {/* Image */}
              <img
                src={cat.image}
                alt={cat.name}
                className="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                loading="lazy"
              />

              {/* Gradient Overlay */}
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent transition-opacity duration-300"></div>

              {/* Content */}
              <div className="absolute inset-0 p-6 flex flex-col justify-end text-white">
                <span className="text-[11px] font-bold uppercase tracking-wider text-orange-300 mb-1">
                  {cat.tag}
                </span>
                <h3 className="font-serif text-xl sm:text-2xl font-bold leading-tight group-hover:text-orange-200 transition-colors">
                  {cat.name}
                </h3>
                
                <div className="flex items-center gap-1.5 text-xs font-semibold text-stone-200 mt-2 opacity-90 group-hover:opacity-100 group-hover:translate-x-1 transition-all">
                  <span>Shop Collection</span>
                  <ArrowRight className="w-3 h-3 text-orange-400" />
                </div>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};
