import React, { useState } from 'react';
import { Tag, Plus, Trash2, X, Check, Calendar, Sparkles } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminCoupons = () => {
  const { coupons, setCoupons, addToast } = useStore();

  const [modalOpen, setModalOpen] = useState(false);
  const [code, setCode] = useState('');
  const [type, setType] = useState('percentage'); // 'percentage' | 'fixed'
  const [discount, setDiscount] = useState(15);
  const [minSpend, setMinSpend] = useState(999);
  const [description, setDescription] = useState('');
  const [expiresAt, setExpiresAt] = useState('2027-12-31');

  const handleCreateCoupon = (e) => {
    e.preventDefault();
    if (!code) return;

    const newCoupon = {
      code: code.toUpperCase().replace(/\s+/g, ''),
      type,
      discount: Number(discount),
      minSpend: Number(minSpend),
      description: description || `${type === 'percentage' ? `${discount}% OFF` : `Flat ₹${discount} OFF`} on orders above ₹${minSpend}`,
      isActive: true,
      expiresAt: expiresAt || '2027-12-31'
    };

    setCoupons(prev => [newCoupon, ...prev]);
    setModalOpen(false);
    setCode('');
    setDescription('');
    addToast(`Coupon "${newCoupon.code}" created successfully!`, 'success');
  };

  const handleDeleteCoupon = (codeToDelete) => {
    setCoupons(prev => prev.filter(c => c.code !== codeToDelete));
    addToast('Coupon code removed', 'info');
  };

  const handleToggleStatus = (targetCode) => {
    setCoupons(prev => prev.map(c => c.code === targetCode ? { ...c, isActive: !c.isActive } : c));
    addToast('Coupon status updated', 'info');
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Promotions &amp; Coupon Codes
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Create discount promo codes for campaigns, influencers, and seasonal sales
          </p>
        </div>

        <button
          onClick={() => setModalOpen(true)}
          className="bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
        >
          <Plus className="w-4 h-4" />
          <span>Create New Coupon</span>
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {coupons.map((coupon) => (
          <div
            key={coupon.code}
            className={`bg-white rounded-3xl p-6 border shadow-sm flex flex-col justify-between space-y-4 transition-all ${
              coupon.isActive ? 'border-stone-200' : 'border-stone-200 opacity-60 bg-stone-50'
            }`}
          >
            <div>
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <Tag className="w-4 h-4 text-orange-600" />
                  <span className="font-mono font-black text-base text-stone-900 tracking-wider">
                    {coupon.code}
                  </span>
                </div>
                <button
                  onClick={() => handleToggleStatus(coupon.code)}
                  className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                    coupon.isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600'
                  }`}
                >
                  {coupon.isActive ? 'Active' : 'Disabled'}
                </button>
              </div>

              <div className="mt-4 pt-4 border-t border-stone-100 space-y-2 text-xs">
                <p className="font-bold text-stone-900 text-sm">
                  {coupon.type === 'percentage' ? `${coupon.discount}% Discount` : `Flat ₹${coupon.discount} OFF`}
                </p>
                <p className="text-stone-600">{coupon.description}</p>
                <p className="text-stone-400 text-[11px]">
                  <strong>Min. Cart Spend:</strong> ₹{coupon.minSpend?.toLocaleString()}
                </p>
                <p className="text-stone-400 text-[11px] flex items-center gap-1">
                  <Calendar className="w-3 h-3" />
                  <span>Valid until: {coupon.expiresAt}</span>
                </p>
              </div>
            </div>

            <div className="pt-3 border-t border-stone-100 flex items-center justify-end">
              <button
                onClick={() => handleDeleteCoupon(coupon.code)}
                className="text-stone-400 hover:text-rose-600 text-xs font-semibold flex items-center gap-1 p-1 transition-colors"
              >
                <Trash2 className="w-3.5 h-3.5" />
                <span>Delete</span>
              </button>
            </div>
          </div>
        ))}
      </div>

      {modalOpen && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setModalOpen(false)}></div>
          <div className="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl z-10 animate-scale-in space-y-4">
            <h3 className="font-serif text-lg font-bold text-stone-900">Create New Promo Code</h3>

            <form onSubmit={handleCreateCoupon} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold text-stone-700 mb-1">Coupon Code *</label>
                <input
                  type="text"
                  value={code}
                  onChange={(e) => setCode(e.target.value)}
                  placeholder="e.g. SUMMER30"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono uppercase font-bold"
                  required
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Discount Type</label>
                  <select
                    value={type}
                    onChange={(e) => setType(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-medium"
                  >
                    <option value="percentage">Percentage (% OFF)</option>
                    <option value="fixed">Flat Amount (₹ OFF)</option>
                  </select>
                </div>
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Discount Value *</label>
                  <input
                    type="number"
                    value={discount}
                    onChange={(e) => setDiscount(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono font-bold"
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Min. Spend (₹)</label>
                  <input
                    type="number"
                    value={minSpend}
                    onChange={(e) => setMinSpend(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono"
                    required
                  />
                </div>
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Expiry Date</label>
                  <input
                    type="date"
                    value={expiresAt}
                    onChange={(e) => setExpiresAt(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono"
                  />
                </div>
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Description</label>
                <input
                  type="text"
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  placeholder="e.g. 15% OFF for new summer drop items"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                />
              </div>

              <div className="flex gap-2 pt-3 border-t border-stone-100">
                <button type="submit" className="flex-1 bg-orange-600 text-white font-bold py-3 rounded-xl">
                  Create Coupon
                </button>
                <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-3 border rounded-xl font-semibold">
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

    </div>
  );
};
