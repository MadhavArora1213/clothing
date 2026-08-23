import React, { useState } from 'react';
import { 
  ShieldCheck, 
  CreditCard, 
  Truck, 
  MapPin, 
  CheckCircle2, 
  ArrowRight, 
  ArrowLeft, 
  Lock, 
  QrCode, 
  Banknote,
  Building,
  Sparkles
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { useStore } from '../context/StoreContext';

export const CheckoutPage = () => {
  const { 
    cart, 
    cartSubtotal, 
    cartDiscount, 
    shippingFee, 
    estimatedTax, 
    cartGrandTotal, 
    appliedCoupon,
    user, 
    createOrder, 
    navigateTo, 
    addToast 
  } = useStore();

  // Multi-step: 1 = Shipping, 2 = Payment
  const [step, setStep] = useState(1);

  // Form Fields
  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');
  const [phone, setPhone] = useState(user?.phone || '');
  const [address, setAddress] = useState(user?.savedAddresses?.[0]?.street || '');
  const [city, setCity] = useState(user?.savedAddresses?.[0]?.city || 'Bengaluru');
  const [state, setState] = useState(user?.savedAddresses?.[0]?.state || 'Karnataka');
  const [pincode, setPincode] = useState(user?.savedAddresses?.[0]?.pincode || '560034');

  // Shipping & Payment Options
  const [shippingMethod, setShippingMethod] = useState('express'); // 'express' | 'same-day'
  const [paymentMethod, setPaymentMethod] = useState('upi'); // 'upi' | 'card' | 'cod' | 'netbanking'
  const [upiId, setUpiId] = useState('aarav@okhdfcbank');
  const [cardNumber, setCardNumber] = useState('4532 &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 8892');
  const [isProcessing, setIsProcessing] = useState(false);

  if (cart.length === 0) {
    return (
      <div className="py-20 text-center animate-fade-in">
        <h2 className="text-xl font-bold font-serif">No items to checkout</h2>
        <button onClick={() => navigateTo('shop')} className="mt-4 text-orange-600 underline text-xs font-bold">
          Return to Shop
        </button>
      </div>
    );
  }

  const handleSelectSavedAddress = (addr) => {
    setName(addr.name);
    setPhone(addr.phone);
    setAddress(addr.street);
    setCity(addr.city);
    setState(addr.state);
    setPincode(addr.pincode);
    addToast(`Selected "${addr.title}" address`, 'info');
  };

  const handleContinueToPayment = (e) => {
    e.preventDefault();
    if (!name || !email || !phone || !address || !pincode) {
      addToast('Please complete all required shipping fields', 'error');
      return;
    }
    setStep(2);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handlePlaceOrder = () => {
    setIsProcessing(true);

    const paymentLabel = 
      paymentMethod === 'upi' ? `UPI (${upiId || 'Direct QR'})` :
      paymentMethod === 'card' ? 'Credit / Debit Card' :
      paymentMethod === 'cod' ? 'Cash On Delivery (COD)' : 'Net Banking';

    setTimeout(() => {
      const order = createOrder({
        customer: { name, email, phone, address, city, state, pincode },
        items: cart,
        subtotal: cartSubtotal,
        discount: cartDiscount,
        shippingFee: shippingMethod === 'same-day' ? 199 : shippingFee,
        tax: estimatedTax,
        total: cartGrandTotal + (shippingMethod === 'same-day' ? 199 : 0),
        paymentMethod: paymentLabel
      });

      // Confetti burst
      try {
        confetti({
          particleCount: 120,
          spread: 80,
          origin: { y: 0.6 }
        });
      } catch (err) {
        // silent fallback
      }

      setIsProcessing(false);
      navigateTo('order-success', { orderId: order.id });
    }, 1200);
  };

  return (
    <div className="py-10 sm:py-16 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Step Indicator Header */}
        <div className="max-w-xl mx-auto mb-10">
          <div className="flex items-center justify-between relative">
            <div className="absolute left-0 top-1/2 -translate-y-1/2 w-full h-0.5 bg-stone-200 -z-10"></div>
            <div className={`flex items-center gap-2 bg-[#FAFAF9] px-3 ${step >= 1 ? 'text-orange-600 font-bold' : 'text-stone-400'}`}>
              <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${
                step >= 1 ? 'bg-orange-600 text-white' : 'bg-stone-200 text-stone-600'
              }`}>
                1
              </div>
              <span className="text-xs hidden sm:inline">Shipping Address</span>
            </div>

            <div className={`flex items-center gap-2 bg-[#FAFAF9] px-3 ${step === 2 ? 'text-orange-600 font-bold' : 'text-stone-400'}`}>
              <div className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold ${
                step === 2 ? 'bg-orange-600 text-white' : 'bg-stone-200 text-stone-600'
              }`}>
                2
              </div>
              <span className="text-xs hidden sm:inline">Payment &amp; Review</span>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Main Form Area */}
          <div className="lg:col-span-7 space-y-6">
            
            {step === 1 && (
              <div className="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-sm space-y-6">
                
                {/* Saved Address Quick Select */}
                {user?.savedAddresses?.length > 0 && (
                  <div>
                    <div className="flex items-center justify-between mb-3">
                      <span className="text-xs font-bold text-stone-900 uppercase tracking-wider">
                        Select from Saved Addresses
                      </span>
                      <span className="text-xs text-orange-600 font-medium">Logged in as {user.name}</span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      {user.savedAddresses.map((addr) => (
                        <div
                          key={addr.id}
                          onClick={() => handleSelectSavedAddress(addr)}
                          className={`p-3.5 rounded-2xl border-2 cursor-pointer transition-all ${
                            address === addr.street 
                              ? 'border-orange-600 bg-orange-50/40 shadow-sm' 
                              : 'border-stone-200 hover:border-stone-300'
                          }`}
                        >
                          <div className="flex items-center justify-between text-xs font-bold mb-1">
                            <span>{addr.title}</span>
                            {addr.isDefault && <span className="text-[10px] bg-stone-100 text-stone-700 px-1.5 py-0.2 rounded">Default</span>}
                          </div>
                          <p className="text-[11px] text-stone-600 line-clamp-2">{addr.street}</p>
                          <p className="text-[11px] text-stone-400 mt-1">{addr.city}, {addr.pincode}</p>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                <form onSubmit={handleContinueToPayment} className="space-y-4 pt-2">
                  <h3 className="font-serif text-lg font-bold text-stone-900">
                    Contact &amp; Delivery Destination
                  </h3>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Full Recipient Name *</label>
                      <input
                        type="text"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="e.g. Aarav Mehta"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                        required
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Phone Number (For OTP &amp; Delivery) *</label>
                      <input
                        type="tel"
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        placeholder="+91 98765 43210"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white font-mono"
                        required
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-stone-700 mb-1">Email Address (For Tax Invoice &amp; Tracking) *</label>
                    <input
                      type="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="aarav@example.com"
                      className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                      required
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-stone-700 mb-1">Flat / House No. / Building / Street *</label>
                    <input
                      type="text"
                      value={address}
                      onChange={(e) => setAddress(e.target.value)}
                      placeholder="e.g. Flat 402, Lotus Towers, 100ft Road"
                      className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                      required
                    />
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">City *</label>
                      <input
                        type="text"
                        value={city}
                        onChange={(e) => setCity(e.target.value)}
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                        required
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">State *</label>
                      <input
                        type="text"
                        value={state}
                        onChange={(e) => setState(e.target.value)}
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                        required
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">6-Digit Pincode *</label>
                      <input
                        type="text"
                        maxLength={6}
                        value={pincode}
                        onChange={(e) => setPincode(e.target.value.replace(/\D/g, ''))}
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white font-mono font-bold"
                        required
                      />
                    </div>
                  </div>

                  {/* Delivery Speed Options */}
                  <div className="pt-4 border-t border-stone-100">
                    <label className="block text-xs font-bold text-stone-700 mb-2">Select Shipping Speed</label>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <label 
                        onClick={() => setShippingMethod('express')}
                        className={`flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all ${
                          shippingMethod === 'express' ? 'border-orange-600 bg-orange-50/40' : 'border-stone-200'
                        }`}
                      >
                        <input type="radio" checked={shippingMethod === 'express'} onChange={() => {}} className="accent-orange-600 mt-1" />
                        <div>
                          <div className="flex items-center gap-1.5 text-xs font-bold text-stone-900">
                            <span>Standard Express Air</span>
                            <span className="text-emerald-700 font-extrabold">(FREE)</span>
                          </div>
                          <p className="text-[11px] text-stone-500 mt-0.5">Delivery in 2-3 business days</p>
                        </div>
                      </label>

                      <label 
                        onClick={() => setShippingMethod('same-day')}
                        className={`flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all ${
                          shippingMethod === 'same-day' ? 'border-orange-600 bg-orange-50/40' : 'border-stone-200'
                        }`}
                      >
                        <input type="radio" checked={shippingMethod === 'same-day'} onChange={() => {}} className="accent-orange-600 mt-1" />
                        <div>
                          <div className="flex items-center gap-1.5 text-xs font-bold text-stone-900">
                            <span>Priority Same-Day Rush</span>
                            <span className="text-orange-600 font-mono font-bold">+₹199</span>
                          </div>
                          <p className="text-[11px] text-stone-500 mt-0.5">Guaranteed dispatch in 4 hours</p>
                        </div>
                      </label>
                    </div>
                  </div>

                  <div className="pt-4">
                    <button
                      type="submit"
                      className="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2 group"
                    >
                      <span>Continue to Payment</span>
                      <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </button>
                  </div>
                </form>

              </div>
            )}

            {step === 2 && (
              <div className="bg-white p-6 sm:p-8 rounded-3xl border border-stone-200/80 shadow-sm space-y-6">
                <div className="flex items-center justify-between pb-4 border-b border-stone-100">
                  <div>
                    <h3 className="font-serif text-lg font-bold text-stone-900">Payment Gateway</h3>
                    <p className="text-xs text-stone-500">Select your preferred secure payment method</p>
                  </div>
                  <button
                    onClick={() => setStep(1)}
                    className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1"
                  >
                    <ArrowLeft className="w-3.5 h-3.5" />
                    <span>Edit Shipping</span>
                  </button>
                </div>

                {/* Delivery Summary Banner */}
                <div className="bg-stone-50 p-3.5 rounded-2xl border border-stone-200 text-xs text-stone-700 flex justify-between items-center">
                  <div>
                    <span className="font-bold">Shipping To:</span> {name} &bull; {address}, {city} ({pincode})
                  </div>
                  <span className="text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded text-[10px]">Verified</span>
                </div>

                {/* Payment Mode Options */}
                <div className="space-y-3">
                  {/* UPI */}
                  <div 
                    onClick={() => setPaymentMethod('upi')}
                    className={`p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                      paymentMethod === 'upi' ? 'border-orange-600 bg-orange-50/30' : 'border-stone-200 hover:border-stone-300'
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <input type="radio" checked={paymentMethod === 'upi'} onChange={() => {}} className="accent-orange-600" />
                        <div className="flex items-center gap-2">
                          <QrCode className="w-5 h-5 text-orange-600" />
                          <span className="text-xs font-bold text-stone-900">Instant UPI (Google Pay, PhonePe, Paytm QR)</span>
                        </div>
                      </div>
                      <span className="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Fastest</span>
                    </div>

                    {paymentMethod === 'upi' && (
                      <div className="mt-3 pt-3 border-t border-orange-200/60 flex flex-col sm:flex-row gap-3 items-center">
                        <input
                          type="text"
                          value={upiId}
                          onChange={(e) => setUpiId(e.target.value)}
                          placeholder="yourname@upi"
                          className="flex-1 bg-white border border-stone-200 text-xs px-3 py-2 rounded-xl focus:outline-none focus:border-orange-500 font-mono"
                        />
                        <span className="text-xs text-stone-500">or scan dynamic QR on next screen</span>
                      </div>
                    )}
                  </div>

                  {/* Cards */}
                  <div 
                    onClick={() => setPaymentMethod('card')}
                    className={`p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                      paymentMethod === 'card' ? 'border-orange-600 bg-orange-50/30' : 'border-stone-200 hover:border-stone-300'
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <input type="radio" checked={paymentMethod === 'card'} onChange={() => {}} className="accent-orange-600" />
                        <div className="flex items-center gap-2">
                          <CreditCard className="w-5 h-5 text-orange-600" />
                          <span className="text-xs font-bold text-stone-900">Credit / Debit Card (Visa, Mastercard, RuPay)</span>
                        </div>
                      </div>
                      <Lock className="w-3.5 h-3.5 text-stone-400" />
                    </div>

                    {paymentMethod === 'card' && (
                      <div className="mt-3 pt-3 border-t border-orange-200/60 space-y-2">
                        <input
                          type="text"
                          value={cardNumber}
                          onChange={(e) => setCardNumber(e.target.value)}
                          placeholder="Card Number"
                          className="w-full bg-white border border-stone-200 text-xs px-3 py-2 rounded-xl font-mono focus:outline-none focus:border-orange-500"
                        />
                        <div className="grid grid-cols-2 gap-2">
                          <input
                            type="text"
                            placeholder="MM / YY"
                            defaultValue="08/29"
                            className="bg-white border border-stone-200 text-xs px-3 py-2 rounded-xl font-mono focus:outline-none focus:border-orange-500"
                          />
                          <input
                            type="password"
                            maxLength={4}
                            placeholder="CVV"
                            defaultValue="782"
                            className="bg-white border border-stone-200 text-xs px-3 py-2 rounded-xl font-mono focus:outline-none focus:border-orange-500"
                          />
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Cash On Delivery */}
                  <div 
                    onClick={() => setPaymentMethod('cod')}
                    className={`p-4 rounded-2xl border-2 cursor-pointer transition-all ${
                      paymentMethod === 'cod' ? 'border-orange-600 bg-orange-50/30' : 'border-stone-200 hover:border-stone-300'
                    }`}
                  >
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <input type="radio" checked={paymentMethod === 'cod'} onChange={() => {}} className="accent-orange-600" />
                        <div className="flex items-center gap-2">
                          <Banknote className="w-5 h-5 text-emerald-600" />
                          <span className="text-xs font-bold text-stone-900">Cash On Delivery (COD)</span>
                        </div>
                      </div>
                      <span className="text-[10px] text-stone-500">Pay cash or UPI to delivery agent</span>
                    </div>
                  </div>
                </div>

                {/* Final Order CTA Button */}
                <div className="pt-4">
                  <button
                    onClick={handlePlaceOrder}
                    disabled={isProcessing}
                    className="w-full bg-orange-600 hover:bg-orange-500 disabled:bg-stone-400 text-white font-bold py-4 rounded-2xl shadow-xl shadow-orange-200 transition-all flex items-center justify-center gap-2 group text-sm"
                  >
                    {isProcessing ? (
                      <div className="flex items-center gap-2">
                        <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span>Securing Order &amp; Generating Invoice...</span>
                      </div>
                    ) : (
                      <>
                        <Sparkles className="w-4 h-4" />
                        <span>Place Order &bull; Pay ₹{(cartGrandTotal + (shippingMethod === 'same-day' ? 199 : 0)).toLocaleString()}</span>
                      </>
                    )}
                  </button>
                </div>

              </div>
            )}

          </div>

          {/* Right: Itemized Order Summary */}
          <div className="lg:col-span-5 space-y-6">
            <div className="bg-white p-6 rounded-3xl border border-stone-200/80 shadow-sm space-y-4">
              <h3 className="font-serif text-lg font-bold text-stone-900">Bag Review ({cart.length} items)</h3>
              
              <div className="max-h-64 overflow-y-auto space-y-3 pr-1 divide-y divide-stone-100">
                {cart.map((item) => (
                  <div key={item.cartItemId} className="flex gap-3 pt-3 first:pt-0">
                    <img src={item.image} alt={item.name} className="w-14 h-16 object-cover rounded-xl shrink-0" />
                    <div className="flex-1 min-w-0">
                      <h4 className="text-xs font-bold text-stone-900 truncate">{item.name}</h4>
                      <p className="text-[11px] text-stone-500 mt-0.5">Size: {item.size} &bull; Qty: {item.quantity}</p>
                      <p className="text-xs font-extrabold text-stone-900 font-mono mt-1">₹{(item.price * item.quantity).toLocaleString()}</p>
                    </div>
                  </div>
                ))}
              </div>

              {/* Price Calculation */}
              <div className="space-y-2 pt-4 border-t border-stone-200 text-xs text-stone-600">
                <div className="flex justify-between">
                  <span>Subtotal</span>
                  <span className="font-semibold font-mono text-stone-900">₹{cartSubtotal.toLocaleString()}</span>
                </div>
                {appliedCoupon && (
                  <div className="flex justify-between text-emerald-600 font-semibold">
                    <span>Coupon ({appliedCoupon.code})</span>
                    <span className="font-mono">-₹{cartDiscount.toLocaleString()}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span>Shipping</span>
                  <span className="font-semibold font-mono text-emerald-600">
                    {shippingMethod === 'same-day' ? '+₹199' : (shippingFee === 0 ? 'FREE' : `₹${shippingFee}`)}
                  </span>
                </div>
                <div className="flex justify-between">
                  <span>GST (5%)</span>
                  <span className="font-semibold font-mono text-stone-900">₹{estimatedTax.toLocaleString()}</span>
                </div>
                <div className="flex justify-between pt-3 border-t border-stone-200 text-base font-bold text-stone-900">
                  <span>Total Amount</span>
                  <span className="text-xl text-orange-600 font-mono">
                    ₹{(cartGrandTotal + (shippingMethod === 'same-day' ? 199 : 0)).toLocaleString()}
                  </span>
                </div>
              </div>

              <div className="bg-stone-50 p-3 rounded-xl border border-stone-200/80 text-[11px] text-stone-500 flex items-center gap-2">
                <ShieldCheck className="w-4 h-4 text-emerald-600 shrink-0" />
                <span>256-Bit Bank Grade SSL Encryption &bull; Tax invoice generated automatically</span>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  );
};
