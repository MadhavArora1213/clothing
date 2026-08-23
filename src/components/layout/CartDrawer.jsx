import React, { useState } from 'react';
import { 
  X, 
  Trash2, 
  Plus, 
  Minus, 
  ShoppingBag, 
  ArrowRight, 
  Sparkles, 
  Tag, 
  CheckCircle2, 
  Truck, 
  ShieldCheck 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const CartDrawer = () => {
  const { 
    isCartOpen, 
    setIsCartOpen, 
    cart, 
    cartItemCount, 
    updateCartQuantity, 
    removeFromCart, 
    cartSubtotal, 
    cartDiscount, 
    shippingFee, 
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

  if (!isCartOpen) return null;

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

  return (
    <div className="fixed inset-0 z-50 overflow-hidden">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
        onClick={() => setIsCartOpen(false)}
      ></div>

      <div className="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div className="w-screen max-w-md bg-white shadow-2xl flex flex-col animate-slide-in-right">
          
          {/* Drawer Header */}
          <div className="p-5 border-b border-stone-100 flex items-center justify-between bg-stone-50/50">
            <div className="flex items-center gap-2">
              <ShoppingBag className="w-5 h-5 text-orange-600" />
              <h2 className="font-serif text-lg font-bold text-stone-900">Your Shopping Bag</h2>
              <span className="text-xs font-bold bg-stone-200 text-stone-700 px-2 py-0.5 rounded-full">
                {cartItemCount}
              </span>
            </div>
            <button 
              onClick={() => setIsCartOpen(false)}
              className="p-1.5 rounded-full text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Free Shipping Progress Indicator */}
          <div className="bg-orange-50/60 px-5 py-3 border-b border-orange-100/60 text-xs">
            {isFreeShipping && cartSubtotal > 0 ? (
              <div className="flex items-center gap-2 text-emerald-700 font-bold">
                <CheckCircle2 className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>🎉 Awesome! You've unlocked FREE Express Shipping!</span>
              </div>
            ) : (
              <div className="space-y-1.5">
                <div className="flex items-center justify-between text-stone-700 font-medium">
                  <span className="flex items-center gap-1.5">
                    <Truck className="w-3.5 h-3.5 text-orange-600" />
                    <span>Add <strong>₹{freeShippingRemaining}</strong> more for <strong>FREE Delivery</strong></span>
                  </span>
                  <span className="font-bold text-orange-600">{progressPercent}%</span>
                </div>
                <div className="w-full bg-stone-200 h-1.5 rounded-full overflow-hidden">
                  <div 
                    className="bg-orange-600 h-full rounded-full transition-all duration-500"
                    style={{ width: `${progressPercent}%` }}
                  ></div>
                </div>
              </div>
            )}
          </div>

          {/* Cart Item List */}
          <div className="flex-1 overflow-y-auto p-5 space-y-4">
            {cart.length === 0 ? (
              <div className="h-full flex flex-col items-center justify-center text-center py-12 space-y-4">
                <div className="w-20 h-20 rounded-full bg-stone-100 flex items-center justify-center text-stone-300">
                  <ShoppingBag className="w-10 h-10" />
                </div>
                <div>
                  <h3 className="font-serif text-lg font-bold text-stone-800">Your bag is empty</h3>
                  <p className="text-xs text-stone-500 mt-1 max-w-xs">
                    Explore our new arrivals and drop-shoulder streetwear to fill it up.
                  </p>
                </div>
                <button
                  onClick={() => {
                    setIsCartOpen(false);
                    navigateTo('shop');
                  }}
                  className="bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-6 py-3 rounded-full transition-all flex items-center gap-2 shadow"
                >
                  <span>Explore Collections</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>
              </div>
            ) : (
              cart.map((item) => (
                <div 
                  key={item.cartItemId}
                  className="flex gap-4 p-3 rounded-2xl bg-stone-50 border border-stone-100 hover:border-stone-200 transition-all"
                >
                  {/* Thumbnail */}
                  <img 
                    src={item.image} 
                    alt={item.name} 
                    className="w-20 h-24 object-cover rounded-xl shrink-0 cursor-pointer"
                    onClick={() => {
                      setIsCartOpen(false);
                      navigateTo('product', { slug: item.slug });
                    }}
                  />

                  {/* Details */}
                  <div className="flex-1 flex flex-col justify-between min-w-0">
                    <div>
                      <div className="flex items-start justify-between gap-2">
                        <h4 
                          onClick={() => {
                            setIsCartOpen(false);
                            navigateTo('product', { slug: item.slug });
                          }}
                          className="text-xs font-bold text-stone-900 line-clamp-1 hover:text-orange-600 transition-colors cursor-pointer"
                        >
                          {item.name}
                        </h4>
                        <button
                          onClick={() => removeFromCart(item.cartItemId)}
                          className="text-stone-400 hover:text-rose-600 transition-colors p-0.5"
                          title="Remove item"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <div className="flex items-center gap-2 mt-1 text-[11px] text-stone-500">
                        <span className="font-semibold bg-white px-1.5 py-0.5 rounded border border-stone-200">
                          Size: {item.size}
                        </span>
                        {item.color && (
                          <span className="truncate">Color: {item.color}</span>
                        )}
                      </div>
                    </div>

                    <div className="flex items-center justify-between mt-2 pt-2 border-t border-stone-200/60">
                      {/* Quantity Modifier */}
                      <div className="flex items-center gap-2 bg-white rounded-lg border border-stone-200 px-2 py-1">
                        <button
                          onClick={() => updateCartQuantity(item.cartItemId, -1)}
                          className="text-stone-500 hover:text-stone-900 p-0.5"
                        >
                          <Minus className="w-3 h-3" />
                        </button>
                        <span className="text-xs font-bold text-stone-800 w-4 text-center">
                          {item.quantity}
                        </span>
                        <button
                          onClick={() => updateCartQuantity(item.cartItemId, 1)}
                          className="text-stone-500 hover:text-stone-900 p-0.5"
                        >
                          <Plus className="w-3 h-3" />
                        </button>
                      </div>

                      {/* Item Total Price */}
                      <div className="text-right">
                        <span className="text-xs font-bold text-stone-900">
                          ₹{(item.price * item.quantity).toLocaleString()}
                        </span>
                      </div>
                    </div>

                  </div>
                </div>
              ))
            )}
          </div>

          {/* Drawer Footer & Checkout Controls */}
          {cart.length > 0 && (
            <div className="p-5 border-t border-stone-100 bg-stone-50/80 space-y-4">
              
              {/* Coupon Engine Box */}
              <div>
                {appliedCoupon ? (
                  <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-2.5 flex items-center justify-between text-xs">
                    <div className="flex items-center gap-2 text-emerald-800 font-bold">
                      <Tag className="w-3.5 h-3.5 text-emerald-600" />
                      <span>{appliedCoupon.code} applied (-₹{cartDiscount.toLocaleString()})</span>
                    </div>
                    <button 
                      onClick={removeCoupon}
                      className="text-emerald-700 hover:text-rose-600 font-semibold underline text-[11px]"
                    >
                      Remove
                    </button>
                  </div>
                ) : (
                  <form onSubmit={handleApplyCoupon} className="space-y-1">
                    <div className="flex gap-2">
                      <div className="relative flex-1">
                        <input
                          type="text"
                          value={couponInput}
                          onChange={(e) => {
                            setCouponInput(e.target.value);
                            setCouponError('');
                          }}
                          placeholder="Promo Code (e.g. WELCOME10)"
                          className="w-full bg-white border border-stone-300 text-stone-800 text-xs px-3 py-2 rounded-xl focus:outline-none focus:border-orange-500 uppercase font-mono"
                        />
                      </div>
                      <button
                        type="submit"
                        className="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-colors shrink-0"
                      >
                        Apply
                      </button>
                    </div>
                    {couponError && (
                      <p className="text-[11px] text-rose-600 font-medium px-1">{couponError}</p>
                    )}
                  </form>
                )}
              </div>

              {/* Price Calculation Breakup */}
              <div className="space-y-1.5 text-xs text-stone-600">
                <div className="flex justify-between">
                  <span>Bag Subtotal</span>
                  <span className="font-semibold text-stone-900">₹{cartSubtotal.toLocaleString()}</span>
                </div>
                {appliedCoupon && (
                  <div className="flex justify-between text-emerald-600 font-medium">
                    <span>Discount ({appliedCoupon.code})</span>
                    <span>-₹{cartDiscount.toLocaleString()}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span>Express Shipping</span>
                  <span className={shippingFee === 0 ? 'text-emerald-600 font-bold' : 'text-stone-900 font-semibold'}>
                    {shippingFee === 0 ? 'FREE' : `₹${shippingFee}`}
                  </span>
                </div>
                <div className="flex justify-between pt-2 border-t border-stone-200 text-sm font-bold text-stone-900">
                  <span>Estimated Total</span>
                  <span className="text-base text-orange-600">₹{cartGrandTotal.toLocaleString()}</span>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="space-y-2 pt-1">
                <button
                  onClick={() => {
                    setIsCartOpen(false);
                    navigateTo('checkout');
                  }}
                  className="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2 group"
                >
                  <span>Proceed to Secure Checkout</span>
                  <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>

                <button
                  onClick={() => {
                    setIsCartOpen(false);
                    navigateTo('cart');
                  }}
                  className="w-full bg-white hover:bg-stone-100 text-stone-700 font-semibold py-2.5 rounded-xl border border-stone-200 text-xs transition-colors text-center block"
                >
                  View Full Cart &amp; Details
                </button>
              </div>

              <div className="flex items-center justify-center gap-2 text-[10px] text-stone-400 pt-1">
                <ShieldCheck className="w-3.5 h-3.5 text-emerald-600" />
                <span>Guaranteed Safe &amp; Secure Checkout</span>
              </div>

            </div>
          )}

        </div>
      </div>
    </div>
  );
};
