import React, { useState, useEffect } from 'react';
import { Timer, ArrowRight, Zap, Copy, Check } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const FlashSaleBanner = () => {
  const { navigateTo, addToast } = useStore();
  const [copied, setCopied] = useState(false);

  const [timeLeft, setTimeLeft] = useState({
    hours: 14,
    minutes: 32,
    seconds: 45
  });

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft(prev => {
        if (prev.seconds > 0) {
          return { ...prev, seconds: prev.seconds - 1 };
        } else if (prev.minutes > 0) {
          return { ...prev, minutes: 59, seconds: 59 };
        } else if (prev.hours > 0) {
          return { hours: prev.hours - 1, minutes: 59, seconds: 59 };
        }
        return { hours: 12, minutes: 0, seconds: 0 };
      });
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const handleCopyCode = () => {
    navigator.clipboard.writeText('AURA20');
    setCopied(true);
    addToast('Coupon code AURA20 copied to clipboard!', 'success');
    setTimeout(() => setCopied(false), 3000);
  };

  return (
    <section className="py-12 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#0F172A] via-[#1E293B] to-[#334155] p-8 sm:p-12 lg:p-16 text-white shadow-2xl">
          
          {/* Background subtle light flare */}
          <div className="absolute top-0 right-0 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            {/* Left Content */}
            <div className="lg:col-span-7 space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-600/30 border border-orange-500/40 text-orange-400 text-xs font-bold uppercase tracking-wider">
                <Zap className="w-3.5 h-3.5 fill-orange-400" />
                <span>Limited Hour Flash Drop</span>
              </div>

              <h3 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                Up to 50% OFF on Luxury Linen &amp; Heavyweight Tees
              </h3>

              <p className="text-stone-300 text-xs sm:text-sm leading-relaxed max-w-lg">
                Score our top-rated artisanal kurtas, oversized mineral wash drops, and resort co-ords at our lowest prices of the season.
              </p>

              {/* Coupon Copy Pill */}
              <div className="pt-2 flex flex-wrap items-center gap-3">
                <span className="text-xs text-stone-400">Use Promo Code:</span>
                <button
                  onClick={handleCopyCode}
                  className="flex items-center gap-2 bg-stone-800/90 hover:bg-stone-700 text-orange-400 border border-orange-500/30 px-3 py-1.5 rounded-xl font-mono text-xs font-bold transition-all shadow"
                >
                  <span>AURA20</span>
                  {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5 text-stone-400" />}
                </button>
              </div>
            </div>

            {/* Right: Countdown Timer & CTA */}
            <div className="lg:col-span-5 flex flex-col items-start lg:items-end justify-center space-y-6">
              <div>
                <span className="text-xs font-bold uppercase tracking-widest text-stone-400 mb-2 flex items-center gap-1.5">
                  <Timer className="w-4 h-4 text-orange-400" />
                  <span>Flash Offer Ends In:</span>
                </span>

                <div className="flex items-center gap-3">
                  <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-4 py-3 text-center min-w-[70px]">
                    <span className="font-mono text-2xl sm:text-3xl font-black text-white">
                      {String(timeLeft.hours).padStart(2, '0')}
                    </span>
                    <span className="block text-[10px] font-bold text-stone-400 uppercase">Hours</span>
                  </div>

                  <span className="text-2xl font-bold text-stone-500">:</span>

                  <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-4 py-3 text-center min-w-[70px]">
                    <span className="font-mono text-2xl sm:text-3xl font-black text-white">
                      {String(timeLeft.minutes).padStart(2, '0')}
                    </span>
                    <span className="block text-[10px] font-bold text-stone-400 uppercase">Mins</span>
                  </div>

                  <span className="text-2xl font-bold text-stone-500">:</span>

                  <div className="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl px-4 py-3 text-center min-w-[70px]">
                    <span className="font-mono text-2xl sm:text-3xl font-black text-orange-400">
                      {String(timeLeft.seconds).padStart(2, '0')}
                    </span>
                    <span className="block text-[10px] font-bold text-stone-400 uppercase">Secs</span>
                  </div>
                </div>
              </div>

              <button
                onClick={() => navigateTo('shop', { filter: 'sale' })}
                className="w-full sm:w-auto bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-8 py-4 rounded-2xl shadow-xl shadow-orange-600/30 transition-all flex items-center justify-center gap-2 group"
              >
                <span>Shop The Flash Sale</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>
            </div>

          </div>
        </div>
      </div>
    </section>
  );
};
