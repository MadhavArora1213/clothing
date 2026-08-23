import React, { useState } from 'react';
import { 
  ShoppingBag, 
  Trash2, 
  Plus, 
  Minus, 
  ArrowRight, 
  Truck, 
  CheckCircle2, 
  Tag, 
  ShieldCheck, 
  ArrowLeft 
} from 'lucide-react';
import { useStore } from '../context/StoreContext';

export const CartPage = () => {
  const { 
    cart, 
    cartItemCount, 
    updateCartQuantity, 
    removeFromCart, 
    cartSubtotal, 
    cartDiscount, 
    shippingFee, 
    estimatedTax, 
    cartGrandTotal, 
    freeShippingThreshold, 
    isFreeShipping, 
    freeShippingRemaining,
    appliedCoupon,
    applyCouponCode,
    removeCoupon,
    navigateTo 
  } = useStore();

  const [couponInput, setCouponInput] = useState('');
  const [couponError, setCouponError] = useState('');

  const handleApplyCoupon = (e) => {
    e.preventDefault();
    setCouponError('');
    const res = applyCouponCode(couponInput);
    if (!res.success) {
      setCouponError(res.message);
    } else {
      setCouponInput('');
    }
  };

  const progressPercent = Math.min(100, Math.round((cartSubtotal / freeShippingThreshold) * 100));

  if (cart.length === 0) {
    return (
      <div className="py-24 bg-[#FAFAF9] min-h-[70vh] flex items-center justify-center animate-fade-in">
        <div className="text-center max-w-md px-4 space-y-4">
          <div className="w-24 h-24 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-300">
            <ShoppingBag className="w-12 h-12" />
          </div>
          <h2 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">Your bag is empty</h2>
          <p className="text-xs text-stone-500">
            Looks like you haven't added any luxury streetwear or handcrafted essentials to your bag yet.
          </p>
          <button
            onClick={() => navigateTo('shop')}
            className="inline-flex items-center gap-2 bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-8 py-4 rounded-full shadow transition-all"
          >
            <span>Start Shopping</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="py-10 sm:py-16 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Page Title */}
        <div className="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8 border-b border-stone-200/80 pb-6">
          <div className="flex items-baseline gap-3">
            <h1 className="font-serif text-3xl sm:text-4xl font-black text-[#0F172A]">Shopping Bag</h1>
            <span className="text-sm font-bold text-stone-500">({cartItemCount} items)</span>
          </div>
          <button
            onClick={() => navigateTo('shop')}
            className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
            <span>Continue Shopping</span>
          </button>
        </div>

        {/* Free Shipping Meter */}
        <div className="bg-white p-4 sm:p-5 rounded-3xl border border-orange-200/80 shadow-sm mb-8">
          {isFreeShipping && cartSubtotal > 0 ? (
            <div className="flex items-center gap-2 text-emerald-700 font-bold text-sm">
              <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0" />
              <span>🎉 Congratulations! You have unlocked FREE Air Express Shipping!</span>
            </div>
          ) : (
            <div className="space-y-2">
              <div className="flex items-center justify-between text-xs sm:text-sm text-stone-800 font-medium">
                <span className="flex items-center gap-2">
                  <Truck className="w-4 h-4 text-orange-600" />
                  <span>Add <strong>₹{freeShippingRemaining}</strong> more to qualify for <strong>FREE Air Express Delivery</strong></span>
                </span>
                <span className="font-bold text-orange-600">{progressPercent}%</span>
              </div>
              <div className="w-full bg-stone-100 h-2 rounded-full overflow-hidden">
                <div 
                  className="bg-orange-600 h-full rounded-full transition-all duration-500"
                  style={{ width: `${progressPercent}%` }}
                ></div>
              </div>
            </div>
          )}
        </div>

        {/* Main Grid: Items List + Order Summary */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Left: Bag Items List */}
          <div className="lg:col-span-8 space-y-4">
            {cart.map((item) => (
              <div
                key={item.cartItemId}
                className="bg-white p-4 sm:p-6 rounded-3xl border border-stone-200/80 shadow-sm flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between"
              >
                {/* Product Thumbnail & Details */}
                <div className="flex items-center gap-4 min-w-0">
                  <img
                    src={item.image}
                    alt={item.name}
                    className="w-20 h-24 sm:w-24 sm:h-28 object-cover rounded-2xl shrink-0 cursor-pointer"
                    onClick={() => navigateTo('product', { slug: item.slug })}
                  />
                  <div className="min-w-0 space-y-1">
                    <h3
                      onClick={() => navigateTo('product', { slug: item.slug })}
                      className="font-serif text-base font-bold text-stone-900 truncate hover:text-orange-600 cursor-pointer"
                    >
                      {item.name}
                    </h3>
                    <div className="flex flex-wrap gap-2 text-xs text-stone-500">
                      <span className="bg-stone-100 px-2 py-0.5 rounded font-semibold text-stone-700">
                        Size: {item.size}
                      </span>
                      {item.color && (
                        <span className="bg-stone-100 px-2 py-0.5 rounded text-stone-600">
                          Color: {item.color}
                        </span>
                      )}
                    </div>
                    <div className="flex items-baseline gap-2 pt-1">
                      <span className="text-sm font-bold text-stone-900 font-mono">
                        ₹{item.price.toLocaleString()}
                      </span>
                      {item.originalPrice > item.price && (
                        <span className="text-xs text-stone-400 line-through font-mono">
                          ₹{item.originalPrice.toLocaleString()}
                        </span>
                      )}
                    </div>
                  </div>
                </div>

                {/* Quantity modifier, Total, Remove */}
                <div className="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-stone-100">
                  <div className="flex items-center bg-stone-50 rounded-xl border border-stone-200 px-2 py-1">
                    <button
                      onClick={() => updateCartQuantity(item.cartItemId, -1)}
                      className="text-stone-500 hover:text-stone-900 p-1"
                    >
                      <Minus className="w-3.5 h-3.5" />
                    </button>
                    <span className="w-7 text-center text-xs font-bold text-stone-900">{item.quantity}</span>
                    <button
                      onClick={() => updateCartQuantity(item.cartItemId, 1)}
                      className="text-stone-500 hover:text-stone-900 p-1"
                    >
                      <Plus className="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <div className="flex items-center gap-4">
                    <span className="text-base font-extrabold text-[#0F172A] font-mono">
                      ₹{(item.price * item.quantity).toLocaleString()}
                    </span>
                    <button
                      onClick={() => removeFromCart(item.cartItemId)}
                      className="text-stone-400 hover:text-rose-600 transition-colors p-1"
                      title="Remove item"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>

              </div>
            ))}
          </div>

          {/* Right: Order Summary & Coupon Engine */}
          <div className="lg:col-span-4 space-y-6">
            <div className="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-sm space-y-6">
              <h3 className="font-serif text-lg font-bold text-stone-900">Order Summary</h3>

              {/* Coupon Engine */}
              <div>
                <label className="block text-xs font-bold text-stone-700 mb-2">Apply Promo Code</label>
                {appliedCoupon ? (
                  <div className="bg-emerald-50 border border-emerald-200 rounded-2xl p-3 flex items-center justify-between text-xs">
                    <div className="flex items-center gap-2 text-emerald-800 font-bold">
                      <Tag className="w-4 h-4 text-emerald-600" />
                      <span>{appliedCoupon.code} applied (-₹{cartDiscount.toLocaleString()})</span>
                    </div>
                    <button 
                      onClick={removeCoupon}
                      className="text-emerald-700 hover:text-rose-600 font-bold underline"
                    >
                      Remove
                    </button>
                  </div>
                ) : (
                  <form onSubmit={handleApplyCoupon} className="space-y-1.5">
                    <div className="flex gap-2">
                      <input
                        type="text"
                        value={couponInput}
                        onChange={(e) => {
                          setCouponInput(e.target.value);
                          setCouponError('');
                        }}
                        placeholder="e.g. WELCOME10, AURA20"
                        className="flex-1 bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl uppercase font-mono focus:outline-none focus:border-orange-500 focus:bg-white"
                      />
                      <button
                        type="submit"
                        className="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors shrink-0"
                      >
                        Apply
                      </button>
                    </div>
                    {couponError && (
                      <p className="text-xs text-rose-600 font-medium">{couponError}</p>
                    )}
                  </form>
                )}
              </div>

              {/* Price Details */}
              <div className="space-y-3 pt-4 border-t border-stone-100 text-xs text-stone-600">
                <div className="flex justify-between">
                  <span>Bag Subtotal</span>
                  <span className="font-semibold text-stone-900 font-mono">₹{cartSubtotal.toLocaleString()}</span>
                </div>
                {appliedCoupon && (
                  <div className="flex justify-between text-emerald-600 font-semibold">
                    <span>Discount ({appliedCoupon.code})</span>
                    <span className="font-mono">-₹{cartDiscount.toLocaleString()}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span>Express Shipping</span>
                  <span className={shippingFee === 0 ? 'text-emerald-600 font-bold' : 'text-stone-900 font-mono font-semibold'}>
                    {shippingFee === 0 ? 'FREE' : `₹${shippingFee}`}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>Estimated GST (5%)</span>
                  <span className="font-semibold text-stone-900 font-mono">₹{estimatedTax.toLocaleString()}</span>
                </div>
                <div className="flex justify-between pt-4 border-t border-stone-200 text-base font-bold text-stone-900">
                  <span>Grand Total</span>
                  <span className="text-xl text-orange-600 font-mono">₹{cartGrandTotal.toLocaleString()}</span>
                </div>
              </div>

              {/* Checkout CTA */}
              <button
                onClick={() => navigateTo('checkout')}
                className="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2 group"
              >
                <span>Proceed to Checkout</span>
                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
              </button>

              <div className="flex items-center justify-center gap-2 text-[11px] text-stone-400">
                <ShieldCheck className="w-4 h-4 text-emerald-600" />
                <span>100% Encrypted &bull; 7-Day Doorstep Returns</span>
              </div>

            </div>
          </div>

        </div>

      </div>
    </div>
  );
};
