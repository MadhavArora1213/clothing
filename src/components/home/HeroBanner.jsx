import React, { useState, useEffect } from 'react';
import { ArrowRight, Sparkles, ShieldCheck, ChevronLeft, ChevronRight } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const HeroBanner = () => {
  const { navigateTo } = useStore();
  const [activeSlide, setActiveSlide] = useState(0);

  const slides = [
    {
      id: 1,
      badge: 'Summer 2026 Collection',
      title: 'The Art of Effortless Luxury',
      subtitle: 'Premium French flax linen, heavyweight 260 GSM acid-wash tees, and modern tailored silhouettes.',
      image: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&auto=format&fit=crop&q=85',
      primaryBtnText: 'Shop New Arrivals',
      primaryAction: { page: 'shop', params: { filter: 'new' } },
      secondaryBtnText: 'Explore Men',
      secondaryAction: { page: 'shop', params: { category: 'men' } }
    },
    {
      id: 2,
      badge: 'Arya Creation Inspired &bull; Heritage Chic',
      title: 'Modern Ethnic & Artisanal Fusion',
      subtitle: 'Hand-block prints, fine Lucknowi Chikankari, and contemporary kurtas designed for effortless day-to-night elegance.',
      image: 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=1600&auto=format&fit=crop&q=85',
      primaryBtnText: 'Explore Ethnic Fusion',
      primaryAction: { page: 'shop', params: { category: 'ethnic-fusion' } },
      secondaryBtnText: 'Women’s Edit',
      secondaryAction: { page: 'shop', params: { category: 'women' } }
    },
    {
      id: 3,
      badge: 'Urban Streetwear &bull; Limited Drop',
      title: 'Heavyweight Drop-Shoulder Culture',
      subtitle: 'Boxy silhouettes in 380 GSM fleece and parachute tactical utility cargoes.',
      image: 'https://images.unsplash.com/photo-1552374196-1ab2a1c593e8?w=1600&auto=format&fit=crop&q=85',
      primaryBtnText: 'Shop Oversized Drops',
      primaryAction: { page: 'shop', params: { category: 'oversized' } },
      secondaryBtnText: 'View Lookbook',
      secondaryAction: { page: 'shop' }
    }
  ];

  useEffect(() => {
    const timer = setInterval(() => {
      setActiveSlide((prev) => (prev + 1) % slides.length);
    }, 6000);
    return () => clearInterval(timer);
  }, [slides.length]);

  const slide = slides[activeSlide];

  return (
    <section className="relative overflow-hidden bg-stone-100 min-h-[580px] lg:min-h-[660px] flex items-center">
      {/* Background Image Carousel with Overlay */}
      {slides.map((s, index) => (
        <div
          key={s.id}
          className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${
            activeSlide === index ? 'opacity-100 scale-100' : 'opacity-0 scale-105 pointer-events-none'
          }`}
        >
          <img
            src={s.image}
            alt={s.title}
            className="w-full h-full object-cover object-center"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-[#0F172A]/90 via-[#0F172A]/50 to-transparent lg:w-3/4"></div>
        </div>
      ))}

      {/* Content Container */}
      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 z-10 w-full">
        <div className="max-w-2xl text-white space-y-6">
          
          {/* Badge */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-bold tracking-wider uppercase text-orange-400">
            <Sparkles className="w-3.5 h-3.5 text-amber-400" />
            <span>{slide.badge}</span>
          </div>

          {/* Headline */}
          <h1 className="font-serif text-4xl sm:text-5xl lg:text-6xl font-black leading-[1.1] tracking-tight">
            {slide.title}
          </h1>

          {/* Subtitle */}
          <p className="text-sm sm:text-base text-stone-200 leading-relaxed max-w-xl font-normal">
            {slide.subtitle}
          </p>

          {/* CTAs */}
          <div className="flex flex-wrap items-center gap-4 pt-2">
            <button
              onClick={() => navigateTo(slide.primaryAction.page, slide.primaryAction.params)}
              className="bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold px-8 py-4 rounded-full shadow-lg shadow-orange-600/30 transition-all flex items-center gap-2 group"
            >
              <span>{slide.primaryBtnText}</span>
              <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </button>

            <button
              onClick={() => navigateTo(slide.secondaryAction.page, slide.secondaryAction.params)}
              className="bg-white/20 hover:bg-white text-white hover:text-stone-900 backdrop-blur-md text-sm font-bold px-8 py-4 rounded-full border border-white/40 transition-all"
            >
              {slide.secondaryBtnText}
            </button>
          </div>

          {/* Trust Highlights */}
          <div className="flex items-center gap-6 pt-6 border-t border-white/15 text-xs text-stone-300">
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span>100% Bio-Washed Cotton</span>
            </div>
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-orange-400"></span>
              <span>Express 48h Dispatch</span>
            </div>
            <div className="flex items-center gap-2">
              <span className="w-2 h-2 rounded-full bg-amber-400"></span>
              <span>COD &amp; Easy Returns</span>
            </div>
          </div>

        </div>
      </div>

      {/* Slide Navigation Arrows */}
      <div className="absolute right-6 bottom-8 z-20 flex items-center gap-2">
        <button
          onClick={() => setActiveSlide((prev) => (prev === 0 ? slides.length - 1 : prev - 1))}
          className="w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md flex items-center justify-center transition-all"
          aria-label="Previous slide"
        >
          <ChevronLeft className="w-5 h-5" />
        </button>

        <div className="flex gap-1.5 px-2">
          {slides.map((_, idx) => (
            <button
              key={idx}
              onClick={() => setActiveSlide(idx)}
              className={`h-2 rounded-full transition-all ${
                activeSlide === idx ? 'w-6 bg-orange-500' : 'w-2 bg-white/40'
              }`}
              aria-label={`Slide ${idx + 1}`}
            />
          ))}
        </div>

        <button
          onClick={() => setActiveSlide((prev) => (prev + 1) % slides.length)}
          className="w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white backdrop-blur-md flex items-center justify-center transition-all"
          aria-label="Next slide"
        >
          <ChevronRight className="w-5 h-5" />
        </button>
      </div>

    </section>
  );
};
