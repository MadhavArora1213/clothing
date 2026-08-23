import React, { useState } from 'react';
import { 
  ArrowRight, 
  ShieldCheck, 
  Truck, 
  RotateCcw, 
  Headphones, 
  Mail, 
  CheckCircle2, 
  Lock 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const Footer = () => {
  const { navigateTo, addToast } = useStore();
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  const handleSubscribe = (e) => {
    e.preventDefault();
    if (!newsletterEmail || !newsletterEmail.includes('@')) {
      addToast('Please enter a valid email address', 'error');
      return;
    }
    setSubscribed(true);
    addToast('🎉 Welcome to the AURA VIP Club! Check your inbox for your 10% coupon.', 'success');
    setNewsletterEmail('');
  };

  return (
    <footer className="bg-[#0F172A] text-white pt-16 pb-12 border-t border-stone-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Value Proposition Features Strip */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-12 border-b border-stone-800">
          <div className="flex items-start gap-4">
            <div className="w-12 h-12 rounded-2xl bg-stone-800/80 border border-stone-700 flex items-center justify-center text-orange-400 shrink-0">
              <Truck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-bold text-stone-100">Free Express Delivery</h4>
              <p className="text-xs text-stone-400 mt-1">Complimentary air shipping across India on orders above ₹999.</p>
            </div>
          </div>

          <div className="flex items-start gap-4">
            <div className="w-12 h-12 rounded-2xl bg-stone-800/80 border border-stone-700 flex items-center justify-center text-orange-400 shrink-0">
              <RotateCcw className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-bold text-stone-100">7-Day Easy Exchange</h4>
              <p className="text-xs text-stone-400 mt-1">Doorstep reverse pickup with zero questions asked.</p>
            </div>
          </div>

          <div className="flex items-start gap-4">
            <div className="w-12 h-12 rounded-2xl bg-stone-800/80 border border-stone-700 flex items-center justify-center text-orange-400 shrink-0">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-bold text-stone-100">100% Genuine Fabrics</h4>
              <p className="text-xs text-stone-400 mt-1">Pure organic French flax linen &amp; 240+ GSM combed cotton.</p>
            </div>
          </div>

          <div className="flex items-start gap-4">
            <div className="w-12 h-12 rounded-2xl bg-stone-800/80 border border-stone-700 flex items-center justify-center text-orange-400 shrink-0">
              <Headphones className="w-6 h-6" />
            </div>
            <div>
              <h4 className="text-base font-bold text-stone-100">24/7 Dedicated Care</h4>
              <p className="text-xs text-stone-400 mt-1">Instant WhatsApp concierge &amp; email support for all queries.</p>
            </div>
          </div>
        </div>

        {/* Main Footer Links & Newsletter */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 py-12">
          
          {/* Brand Col */}
          <div className="lg:col-span-2 space-y-4">
            <div className="flex items-baseline gap-1">
              <span className="font-serif text-3xl font-black tracking-widest text-white">AURA</span>
              <span className="text-xs font-bold text-orange-500 tracking-widest uppercase">&amp; CO.</span>
            </div>
            <p className="text-sm text-stone-400 max-w-sm leading-relaxed">
              Crafting modern luxury streetwear and heritage fusion silhouettes for the contemporary aesthetic individual. Designed with intention in India.
            </p>
            <div className="flex items-center gap-3 pt-2">
              <a href="#instagram" className="w-9 h-9 rounded-full bg-stone-800 hover:bg-orange-600 hover:text-white text-stone-400 flex items-center justify-center transition-colors">
                <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              </a>
              <a href="#twitter" className="w-9 h-9 rounded-full bg-stone-800 hover:bg-orange-600 hover:text-white text-stone-400 flex items-center justify-center transition-colors">
                <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              </a>
              <a href="#facebook" className="w-9 h-9 rounded-full bg-stone-800 hover:bg-orange-600 hover:text-white text-stone-400 flex items-center justify-center transition-colors">
                <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.667 5H18V0h-3.889C10.5 0 9 1.5 9 4.667V8z"/></svg>
              </a>
            </div>
          </div>

          {/* Quick Shop Links */}
          <div>
            <h4 className="text-xs font-bold uppercase tracking-wider text-orange-400 mb-4">Shop Collections</h4>
            <ul className="space-y-2.5 text-sm text-stone-300">
              <li><button onClick={() => navigateTo('shop', { category: 'men' })} className="hover:text-white transition-colors">Men's Wardrobe</button></li>
              <li><button onClick={() => navigateTo('shop', { category: 'women' })} className="hover:text-white transition-colors">Women's Edit</button></li>
              <li><button onClick={() => navigateTo('shop', { category: 'oversized' })} className="hover:text-white transition-colors">Oversized Drop Tees</button></li>
              <li><button onClick={() => navigateTo('shop', { category: 'co-ords' })} className="hover:text-white transition-colors">Resort Co-Ord Sets</button></li>
              <li><button onClick={() => navigateTo('shop', { category: 'ethnic-fusion' })} className="hover:text-white transition-colors">Arya Ethnic Fusion</button></li>
              <li><button onClick={() => navigateTo('shop', { filter: 'sale' })} className="text-orange-400 hover:text-orange-300 font-semibold">Special Sale (Up to 50%)</button></li>
            </ul>
          </div>

          {/* Customer Support */}
          <div>
            <h4 className="text-xs font-bold uppercase tracking-wider text-orange-400 mb-4">Customer Care</h4>
            <ul className="space-y-2.5 text-sm text-stone-300">
              <li><button onClick={() => navigateTo('track-order')} className="hover:text-white transition-colors">Track Your Order</button></li>
              <li><button onClick={() => navigateTo('contact')} className="hover:text-white transition-colors">Help Center / Contact Us</button></li>
              <li><button onClick={() => navigateTo('shipping-policy')} className="hover:text-white transition-colors">Shipping &amp; Delivery</button></li>
              <li><button onClick={() => navigateTo('returns-policy')} className="hover:text-white transition-colors">Returns &amp; Refunds</button></li>
              <li><button onClick={() => navigateTo('faqs')} className="hover:text-white transition-colors">Frequently Asked Questions</button></li>
              <li><button onClick={() => navigateTo('admin')} className="text-stone-400 hover:text-orange-400 font-mono text-xs">Admin Management</button></li>
            </ul>
          </div>

          {/* Newsletter signup */}
          <div>
            <h4 className="text-xs font-bold uppercase tracking-wider text-orange-400 mb-4">Join The Club</h4>
            <p className="text-xs text-stone-400 mb-3 leading-relaxed">
              Subscribe to get secret drop notifications, style edits, and 10% off your initial purchase.
            </p>
            {subscribed ? (
              <div className="bg-stone-800/80 border border-emerald-500/40 rounded-xl p-3 text-emerald-300 text-xs flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0" />
                <span>You're in! Use code <strong className="text-white">WELCOME10</strong></span>
              </div>
            ) : (
              <form onSubmit={handleSubscribe} className="space-y-2">
                <div className="relative">
                  <input
                    type="email"
                    value={newsletterEmail}
                    onChange={(e) => setNewsletterEmail(e.target.value)}
                    placeholder="Enter your email address"
                    className="w-full bg-stone-900 border border-stone-700 text-white text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 placeholder-stone-500"
                    required
                  />
                </div>
                <button
                  type="submit"
                  className="w-full bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold py-2.5 rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-md"
                >
                  <span>Subscribe &amp; Get 10% OFF</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>
              </form>
            )}
          </div>

        </div>

        {/* Bottom copyright and legal */}
        <div className="pt-8 border-t border-stone-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-400">
          <div className="flex items-center gap-2">
            <Lock className="w-3.5 h-3.5 text-emerald-400" />
            <span>256-Bit SSL Encrypted &bull; 100% Safe Payments (UPI, Cards, NetBanking, COD)</span>
          </div>

          <div className="flex items-center gap-4">
            <button onClick={() => navigateTo('privacy-policy')} className="hover:text-white transition-colors">Privacy Policy</button>
            <span>&bull;</span>
            <button onClick={() => navigateTo('terms')} className="hover:text-white transition-colors">Terms of Service</button>
          </div>

          <p className="text-stone-500">
            &copy; {new Date().getFullYear()} AURA &amp; CO. Apparel Inc. All rights reserved.
          </p>
        </div>

      </div>
    </footer>
  );
};
