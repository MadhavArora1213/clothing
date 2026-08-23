import React from 'react';
import { Star, CheckCircle2, Quote, Sparkles } from 'lucide-react';
import { testimonials } from '../../data/mockData';

export const Testimonials = () => {
  return (
    <section className="py-20 bg-white border-t border-stone-100">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto mb-14 space-y-2">
          <div className="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest">
            <Sparkles className="w-3.5 h-3.5" />
            <span>Community Love</span>
          </div>
          <h2 className="font-serif text-3xl sm:text-4xl font-bold text-[#0F172A]">
            Loved by 40,000+ Aesthetic Enthusiasts
          </h2>
          <p className="text-xs sm:text-sm text-stone-500">
            Real reviews from verified buyers across Bengaluru, Mumbai, Delhi &amp; beyond.
          </p>
        </div>

        {/* Testimonials Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {testimonials.map((t) => (
            <div
              key={t.id}
              className="bg-stone-50/80 hover:bg-stone-50 rounded-3xl p-6 sm:p-8 border border-stone-200/70 hover:border-stone-300 transition-all flex flex-col justify-between space-y-6 shadow-sm hover:shadow-lg"
            >
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <div className="flex text-amber-400">
                    {[...Array(t.rating)].map((_, i) => (
                      <Star key={i} className="w-4 h-4 fill-amber-400" />
                    ))}
                  </div>
                  <Quote className="w-6 h-6 text-stone-300" />
                </div>

                <h4 className="font-bold text-sm text-stone-900 leading-snug">
                  "{t.title}"
                </h4>

                <p className="text-xs text-stone-600 leading-relaxed italic">
                  "{t.comment}"
                </p>
              </div>

              <div className="pt-4 border-t border-stone-200/60 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <img
                    src={t.avatar}
                    alt={t.name}
                    className="w-10 h-10 rounded-full object-cover border border-stone-200"
                  />
                  <div>
                    <h5 className="font-bold text-xs text-stone-900">{t.name}</h5>
                    <p className="text-[10px] text-stone-400">{t.location}</p>
                  </div>
                </div>

                {t.verified && (
                  <div className="flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                    <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                    <span>Verified</span>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};
