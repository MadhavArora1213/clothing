import React, { useState } from 'react';
import { 
  Truck, 
  RotateCcw, 
  ShieldCheck, 
  Lock, 
  HelpCircle, 
  ChevronDown, 
  ChevronUp, 
  Search 
} from 'lucide-react';
import { useStore } from '../context/StoreContext';
import { faqs } from '../data/mockData';

export const PolicyPages = ({ type = 'shipping' }) => {
  const { currentPage, navigateTo } = useStore();
  const activeType = type || (currentPage.includes('shipping') ? 'shipping' : currentPage.includes('returns') ? 'returns' : currentPage.includes('privacy') ? 'privacy' : currentPage.includes('terms') ? 'terms' : 'faqs');

  const [faqSearch, setFaqSearch] = useState('');
  const [openFaqIndex, setOpenFaqIndex] = useState(0);

  const filteredFaqs = faqs.filter(f => 
    f.q.toLowerCase().includes(faqSearch.toLowerCase()) || 
    f.a.toLowerCase().includes(faqSearch.toLowerCase())
  );

  return (
    <div className="py-12 sm:py-20 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Navigation Tabs between policies */}
        <div className="flex flex-wrap gap-2 justify-center mb-10">
          <button
            onClick={() => navigateTo('shipping-policy')}
            className={`px-4 py-2 rounded-full text-xs font-bold transition-all ${
              activeType === 'shipping' ? 'bg-[#0F172A] text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
            }`}
          >
            Shipping &amp; Delivery
          </button>
          <button
            onClick={() => navigateTo('returns-policy')}
            className={`px-4 py-2 rounded-full text-xs font-bold transition-all ${
              activeType === 'returns' ? 'bg-[#0F172A] text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
            }`}
          >
            Returns &amp; Exchanges
          </button>
          <button
            onClick={() => navigateTo('faqs')}
            className={`px-4 py-2 rounded-full text-xs font-bold transition-all ${
              activeType === 'faqs' ? 'bg-[#0F172A] text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
            }`}
          >
            FAQs
          </button>
          <button
            onClick={() => navigateTo('privacy-policy')}
            className={`px-4 py-2 rounded-full text-xs font-bold transition-all ${
              activeType === 'privacy' ? 'bg-[#0F172A] text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
            }`}
          >
            Privacy Policy
          </button>
          <button
            onClick={() => navigateTo('terms')}
            className={`px-4 py-2 rounded-full text-xs font-bold transition-all ${
              activeType === 'terms' ? 'bg-[#0F172A] text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
            }`}
          >
            Terms of Service
          </button>
        </div>

        {/* Shipping Policy */}
        {activeType === 'shipping' && (
          <div className="bg-white rounded-3xl p-8 sm:p-12 border border-stone-200/80 shadow-xl space-y-6 text-xs text-stone-600 leading-relaxed">
            <div className="flex items-center gap-3 pb-4 border-b border-stone-100">
              <div className="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                <Truck className="w-5 h-5" />
              </div>
              <div>
                <h1 className="font-serif text-2xl font-bold text-stone-900">Shipping &amp; Delivery Policy</h1>
                <p className="text-[11px] text-stone-400">Last updated: August 2026</p>
              </div>
            </div>

            <div className="space-y-4">
              <h3 className="font-bold text-sm text-stone-900">1. Free Air Express Shipping</h3>
              <p>
                We provide complimentary Air Express Shipping across India on all prepaid and Cash On Delivery (COD) orders exceeding ₹999. For orders under ₹999, a flat shipping charge of ₹99 applies.
              </p>

              <h3 className="font-bold text-sm text-stone-900">2. Dispatch &amp; Delivery Timelines</h3>
              <p>
                All orders are dispatched from our Bengaluru fulfillment center within 24 hours of placement. Metro hubs (Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai, Kolkata) generally receive deliveries within <strong>2 to 3 business days</strong>. Tier 2 and Tier 3 regions receive delivery in <strong>4 to 5 business days</strong>.
              </p>

              <h3 className="font-bold text-sm text-stone-900">3. Live Shipment Tracking</h3>
              <p>
                As soon as your shipment departs our fulfillment warehouse, an automated SMS and email containing your Airway Bill (AWB) tracking link is dispatched to your registered contact.
              </p>
            </div>
          </div>
        )}

        {/* Returns Policy */}
        {activeType === 'returns' && (
          <div className="bg-white rounded-3xl p-8 sm:p-12 border border-stone-200/80 shadow-xl space-y-6 text-xs text-stone-600 leading-relaxed">
            <div className="flex items-center gap-3 pb-4 border-b border-stone-100">
              <div className="w-10 h-10 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                <RotateCcw className="w-5 h-5" />
              </div>
              <div>
                <h1 className="font-serif text-2xl font-bold text-stone-900">7-Day Doorstep Exchange &amp; Returns</h1>
                <p className="text-[11px] text-stone-400">Hassle-free reverse pickups</p>
              </div>
            </div>

            <div className="space-y-4">
              <h3 className="font-bold text-sm text-stone-900">1. Return Window</h3>
              <p>
                Items can be returned or exchanged for alternate sizes within <strong>7 days</strong> of delivery. Garments must remain unworn, unwashed, and accompanied by original tags and packaging.
              </p>

              <h3 className="font-bold text-sm text-stone-900">2. Doorstep Reverse Pickup</h3>
              <p>
                Our courier partners will arrange a reverse pickup directly from your shipping address at zero additional charge.
              </p>

              <h3 className="font-bold text-sm text-stone-900">3. Instant Refunds &amp; Store Credit</h3>
              <p>
                Prepaid order refunds are credited directly to the source payment method within 3-5 business days. COD orders are refunded via direct UPI transfer or store credit vouchers.
              </p>
            </div>
          </div>
        )}

        {/* FAQs */}
        {activeType === 'faqs' && (
          <div className="space-y-6">
            <div className="text-center space-y-2 mb-6">
              <h1 className="font-serif text-3xl font-bold text-stone-900">Frequently Asked Questions</h1>
              <p className="text-xs text-stone-500">Quick answers to common questions about our clothing and orders</p>
            </div>

            <div className="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm flex items-center gap-3">
              <Search className="w-4 h-4 text-stone-400" />
              <input
                type="text"
                value={faqSearch}
                onChange={(e) => setFaqSearch(e.target.value)}
                placeholder="Search FAQs (e.g. shipping, sizes, cod)..."
                className="w-full bg-transparent text-xs text-stone-900 focus:outline-none"
              />
            </div>

            <div className="space-y-3">
              {filteredFaqs.map((faq, idx) => (
                <div 
                  key={idx}
                  className="bg-white rounded-2xl border border-stone-200/80 overflow-hidden shadow-sm"
                >
                  <button
                    onClick={() => setOpenFaqIndex(openFaqIndex === idx ? null : idx)}
                    className="w-full p-5 text-left text-xs sm:text-sm font-bold text-stone-900 flex items-center justify-between gap-4"
                  >
                    <span>{faq.q}</span>
                    {openFaqIndex === idx ? <ChevronUp className="w-4 h-4 text-orange-600 shrink-0" /> : <ChevronDown className="w-4 h-4 text-stone-400 shrink-0" />}
                  </button>
                  {openFaqIndex === idx && (
                    <div className="px-5 pb-5 text-xs text-stone-600 border-t border-stone-100 pt-3 leading-relaxed">
                      {faq.a}
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Privacy & Terms */}
        {(activeType === 'privacy' || activeType === 'terms') && (
          <div className="bg-white rounded-3xl p-8 sm:p-12 border border-stone-200/80 shadow-xl space-y-4 text-xs text-stone-600 leading-relaxed">
            <h1 className="font-serif text-2xl font-bold text-stone-900">
              {activeType === 'privacy' ? 'Privacy & Data Security Policy' : 'Terms & Conditions of Service'}
            </h1>
            <p>
              AURA &amp; CO. is committed to upholding rigorous privacy protection. We never trade, rent, or sell client data. All payment details are handled through PCI-DSS Level 1 certified gateways with 256-bit SSL encryption.
            </p>
            <p>
              By utilizing our website, you agree to comply with our terms of service and acceptable usage guidelines.
            </p>
          </div>
        )}

      </div>
    </div>
  );
};
