import React from 'react';
import { Sparkles, ShieldCheck, Feather, Layers, ArrowRight } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const BrandStory = () => {
  const { navigateTo } = useStore();

  return (
    <section className="py-20 bg-[#FAFAF9] border-t border-stone-200/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          {/* Left: Visual Collage */}
          <div className="lg:col-span-6 grid grid-cols-2 gap-4">
            <div className="space-y-4">
              <div className="rounded-3xl overflow-hidden shadow-md bg-stone-100 aspect-[4/5]">
                <img
                  src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&auto=format&fit=crop&q=80"
                  alt="Artisanal Hand block Crafting"
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="bg-white p-5 rounded-3xl border border-stone-200/80 shadow-sm">
                <Feather className="w-6 h-6 text-orange-600 mb-2" />
                <h4 className="font-bold text-sm text-stone-900">100% Breathable Flax</h4>
                <p className="text-xs text-stone-500 mt-1">Sustainably cultivated organic linen ensuring cool airflow even in high humidity.</p>
              </div>
            </div>

            <div className="space-y-4 pt-8">
              <div className="bg-[#0F172A] text-white p-5 rounded-3xl shadow-lg">
                <span className="text-2xl font-black font-serif text-orange-400">260+</span>
                <span className="block text-xs font-bold text-stone-300 uppercase tracking-wider mt-1">GSM French Terry</span>
                <p className="text-xs text-stone-400 mt-1">Heavyweight dense knit for architectural streetwear drape that never sags.</p>
              </div>
              <div className="rounded-3xl overflow-hidden shadow-md bg-stone-100 aspect-[4/5]">
                <img
                  src="https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=600&auto=format&fit=crop&q=80"
                  alt="Precision Tailored Streetwear"
                  className="w-full h-full object-cover"
                />
              </div>
            </div>
          </div>

          {/* Right: Narrative */}
          <div className="lg:col-span-6 space-y-6">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100/80 text-orange-800 text-xs font-bold uppercase tracking-wider">
              <Sparkles className="w-3.5 h-3.5" />
              <span>Ethos &amp; Craftsmanship</span>
            </div>

            <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#0F172A] leading-tight">
              Where Arya Heritage Meets Urban Street Architecture
            </h2>

            <p className="text-sm text-stone-600 leading-relaxed">
              At <strong>AURA &amp; CO.</strong>, we don't believe in fast fashion throwaways. We bridge two worlds: the intricate artisanal hand-block and Chikankari traditions of classic Indian couture, seamlessly blended with the boxy, drop-shoulder silhouettes of modern Tokyo &amp; London streetwear.
            </p>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
              <div className="p-4 rounded-2xl bg-white border border-stone-200/80">
                <div className="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 mb-2">
                  <ShieldCheck className="w-4 h-4" />
                </div>
                <h4 className="font-bold text-xs text-stone-900">Pre-Shrunk &amp; Bio-Washed</h4>
                <p className="text-[11px] text-stone-500 mt-0.5">Enzyme treated cotton prevents shrinkage and pill build-up across years of washes.</p>
              </div>

              <div className="p-4 rounded-2xl bg-white border border-stone-200/80">
                <div className="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-2">
                  <Layers className="w-4 h-4" />
                </div>
                <h4 className="font-bold text-xs text-stone-900">Zero Plastic Packaging</h4>
                <p className="text-[11px] text-stone-500 mt-0.5">All drops are dispatched in custom 100% recyclable matte paper mailers.</p>
              </div>
            </div>

            <div className="pt-2">
              <button
                onClick={() => navigateTo('shop', { category: 'ethnic-fusion' })}
                className="inline-flex items-center gap-2 bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-7 py-3.5 rounded-full shadow transition-all group"
              >
                <span>Discover The Artisanal Fusion Edit</span>
                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
              </button>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
};
