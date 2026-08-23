import React from 'react';
import { ShoppingBag, ArrowRight } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const LookbookGrid = () => {
  const { navigateTo } = useStore();

  const looks = [
    {
      id: 1,
      image: 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&auto=format&fit=crop&q=80',
      handle: '@priya.style',
      item: 'Elysian Draped Satin Dress',
      price: '₹2,199'
    },
    {
      id: 2,
      image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80',
      handle: '@urban_nomad',
      item: 'Nomad Acid-Wash Tee',
      price: '₹1,299'
    },
    {
      id: 3,
      image: 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=600&auto=format&fit=crop&q=80',
      handle: '@travel_with_kabir',
      item: 'Sorrento Resort Linen Co-Ord',
      price: '₹2,499'
    },
    {
      id: 4,
      image: 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&auto=format&fit=crop&q=80',
      handle: '@ananya_vibe',
      item: 'Modern Chikankari Short Kurti',
      price: '₹1,799'
    }
  ];

  return (
    <section className="py-16 bg-[#FAFAF9] border-t border-stone-200/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
          <div>
            <div className="flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest mb-1">
              <svg className="w-4 h-4 fill-current text-orange-600" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              <span>@aura.apparel.studio</span>
            </div>
            <h2 className="font-serif text-3xl font-bold text-[#0F172A]">
              Styled by You #AURAEveryday
            </h2>
          </div>

          <a
            href="#instagram"
            className="inline-flex items-center gap-1.5 text-xs font-bold text-stone-700 hover:text-orange-600 transition-colors"
          >
            <span>Follow us on Instagram</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </a>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {looks.map((look) => (
            <div
              key={look.id}
              onClick={() => navigateTo('shop')}
              className="group relative rounded-2xl overflow-hidden aspect-[3/4] cursor-pointer shadow-sm hover:shadow-xl transition-all"
            >
              <img
                src={look.image}
                alt={look.item}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />

              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end text-white">
                <span className="text-[10px] text-stone-300 font-mono">{look.handle}</span>
                <p className="text-xs font-bold truncate mt-0.5">{look.item}</p>
                <div className="flex items-center justify-between mt-2 pt-2 border-t border-white/20">
                  <span className="text-xs font-extrabold text-orange-400">{look.price}</span>
                  <div className="w-7 h-7 rounded-full bg-white text-stone-900 flex items-center justify-center shadow">
                    <ShoppingBag className="w-3.5 h-3.5 text-orange-600" />
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};
