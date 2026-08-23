import React, { useState } from 'react';
import { X, Ruler, CheckCircle2, Info } from 'lucide-react';
import { useStore } from '../../context/StoreContext';
import { sizeChartData } from '../../data/mockData';

export const SizeChartModal = () => {
  const { isSizeChartOpen, setIsSizeChartOpen } = useStore();
  const [unit, setUnit] = useState('inches'); // 'inches' | 'cm'

  if (!isSizeChartOpen) return null;

  const data = sizeChartData[unit];

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-10 flex items-center justify-center">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
        onClick={() => setIsSizeChartOpen(false)}
      ></div>

      <div className="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-stone-100 overflow-hidden animate-scale-in p-6 sm:p-8">
        
        {/* Header */}
        <div className="flex items-center justify-between pb-4 border-b border-stone-100">
          <div className="flex items-center gap-2.5">
            <div className="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-600">
              <Ruler className="w-5 h-5" />
            </div>
            <div>
              <h3 className="font-serif text-xl font-bold text-stone-900">Apparel Size &amp; Fit Guide</h3>
              <p className="text-xs text-stone-500">Standard unisex &amp; relaxed streetwear measurement chart</p>
            </div>
          </div>
          <button 
            onClick={() => setIsSizeChartOpen(false)}
            className="p-1.5 rounded-full text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Unit Toggle */}
        <div className="flex items-center justify-between my-5">
          <span className="text-xs font-bold text-stone-700">Dimensions Matrix</span>
          <div className="flex bg-stone-100 p-1 rounded-xl">
            <button
              onClick={() => setUnit('inches')}
              className={`px-3 py-1 text-xs font-bold rounded-lg transition-all ${
                unit === 'inches' ? 'bg-white text-stone-900 shadow-sm' : 'text-stone-500'
              }`}
            >
              Inches (in)
            </button>
            <button
              onClick={() => setUnit('cm')}
              className={`px-3 py-1 text-xs font-bold rounded-lg transition-all ${
                unit === 'cm' ? 'bg-white text-stone-900 shadow-sm' : 'text-stone-500'
              }`}
            >
              Centimeters (cm)
            </button>
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto rounded-2xl border border-stone-200">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-b border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3 px-4">Size</th>
                <th className="py-3 px-4">Chest (Round)</th>
                <th className="py-3 px-4">Length</th>
                <th className="py-3 px-4">Shoulder</th>
                <th className="py-3 px-4">Sleeve</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {data.map((row) => (
                <tr key={row.size} className="hover:bg-orange-50/40 transition-colors">
                  <td className="py-3 px-4 font-bold text-orange-600 bg-stone-50/50">{row.size}</td>
                  <td className="py-3 px-4">{row.chest}</td>
                  <td className="py-3 px-4">{row.length}</td>
                  <td className="py-3 px-4">{row.shoulder}</td>
                  <td className="py-3 px-4">{row.sleeve}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Measuring Tips */}
        <div className="mt-6 bg-stone-50 p-4 rounded-2xl border border-stone-200/80 space-y-2">
          <div className="flex items-center gap-2 text-xs font-bold text-stone-900">
            <Info className="w-4 h-4 text-orange-600" />
            <span>How to Measure Your Body</span>
          </div>
          <ul className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-stone-600">
            <li className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span><strong>Chest:</strong> Measure around fullest part under arms.</span>
            </li>
            <li className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span><strong>Length:</strong> Measure from high point shoulder to hem.</span>
            </li>
            <li className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span><strong>Shoulder:</strong> Measure across upper back bone to bone.</span>
            </li>
            <li className="flex items-center gap-1.5">
              <CheckCircle2 className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
              <span><strong>Fit Note:</strong> For loose relaxed fit, pick regular size.</span>
            </li>
          </ul>
        </div>

      </div>
    </div>
  );
};
