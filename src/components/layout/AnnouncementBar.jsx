import React, { useState, useEffect } from 'react';
import { Sparkles, ArrowRight, ShieldCheck } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AnnouncementBar = () => {
  const { navigateTo } = useStore();
  const announcements = [
    '✨ NEW DROP: The Summer 2026 Collection is now live — Use code WELCOME10 for 10% OFF',
    '🚚 FREE EXPRESS SHIPPING on all prepaid & COD orders above ₹999 across India',
    '⚡ FLASH SALE: Up to 50% OFF on Luxury Linen & Oversized Drops — Limited Stock'
  ];

  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % announcements.length);
    }, 4500);
    return () => clearInterval(timer);
  }, [announcements.length]);

  return (
    <div className="bg-[#0F172A] text-white py-2 px-4 text-xs md:text-sm font-medium tracking-wide relative overflow-hidden transition-colors">
      <div className="max-w-7xl mx-auto flex items-center justify-between">
        <div className="hidden md:flex items-center gap-2 text-stone-400 text-xs">
          <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
          <span>100% Genuine Artisanal Fashion</span>
        </div>

        <div className="flex-1 text-center truncate px-2">
          <div className="inline-flex items-center gap-2 animate-fade-in key={currentIndex}">
            <Sparkles className="w-3.5 h-3.5 text-amber-400 shrink-0" />
            <span className="truncate">{announcements[currentIndex]}</span>
          </div>
        </div>

        <div className="hidden md:flex items-center gap-3 text-xs text-stone-300">
          <button 
            onClick={() => navigateTo('track-order')}
            className="hover:text-white transition-colors underline-offset-4 hover:underline"
          >
            Track Order
          </button>
          <span className="text-stone-600">|</span>
          <button 
            onClick={() => navigateTo('contact')}
            className="hover:text-white transition-colors underline-offset-4 hover:underline"
          >
            Help & Support
          </button>
        </div>
      </div>
    </div>
  );
};
