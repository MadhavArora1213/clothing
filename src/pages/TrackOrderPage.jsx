import React, { useState } from 'react';
import { 
  Search, 
  Truck, 
  Package, 
  CheckCircle2, 
  Clock, 
  MapPin, 
  Printer, 
  ArrowRight, 
  ShieldCheck,
  AlertCircle
} from 'lucide-react';
import { useStore } from '../context/StoreContext';

export const TrackOrderPage = () => {
  const { orders, pageParams, setActiveInvoiceOrder, navigateTo } = useStore();
  const [searchInput, setSearchInput] = useState(pageParams?.orderId || '');
  const [searchedOrder, setSearchedOrder] = useState(() => {
    if (pageParams?.orderId) {
      return orders.find(o => o.orderNumber === pageParams.orderId || o.id === pageParams.orderId) || orders[0];
    }
    return orders[0];
  });
  const [errorMessage, setErrorMessage] = useState('');

  const handleSearch = (e) => {
    e.preventDefault();
    setErrorMessage('');
    if (!searchInput.trim()) return;

    const found = orders.find(o => 
      o.orderNumber?.toLowerCase() === searchInput.trim().toLowerCase() ||
      o.id?.toLowerCase() === searchInput.trim().toLowerCase() ||
      o.customer?.phone?.includes(searchInput.trim())
    );

    if (found) {
      setSearchedOrder(found);
    } else {
      setErrorMessage(`No shipment found matching "${searchInput}". Please verify your Order ID (e.g. ATL-9842).`);
    }
  };

  return (
    <div className="py-12 sm:py-20 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-xl mx-auto mb-10 space-y-2">
          <div className="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest bg-orange-50 px-3 py-1 rounded-full">
            <Truck className="w-3.5 h-3.5" />
            <span>Real-Time Shipment Logistics</span>
          </div>
          <h1 className="font-serif text-3xl sm:text-4xl font-bold text-[#0F172A]">
            Track Your Order
          </h1>
          <p className="text-xs sm:text-sm text-stone-500">
            Enter your Order ID or registered mobile number to see live status updates.
          </p>
        </div>

        {/* Search Bar */}
        <div className="bg-white p-4 sm:p-6 rounded-3xl border border-stone-200/80 shadow-sm mb-10">
          <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-3">
            <div className="relative flex-1">
              <Search className="w-4 h-4 text-stone-400 absolute left-4 top-3.5" />
              <input
                type="text"
                value={searchInput}
                onChange={(e) => setSearchInput(e.target.value)}
                placeholder="Enter Order ID (e.g. ATL-9842 or ATL-9731)..."
                className="w-full bg-stone-50 border border-stone-200 text-xs sm:text-sm pl-11 pr-4 py-3 rounded-2xl focus:outline-none focus:border-orange-500 focus:bg-white font-mono"
              />
            </div>
            <button
              type="submit"
              className="bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-8 py-3 rounded-2xl shadow transition-all flex items-center justify-center gap-2 shrink-0"
            >
              <span>Track Shipment</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </form>

          {errorMessage && (
            <div className="mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-center gap-2">
              <AlertCircle className="w-4 h-4 text-rose-600 shrink-0" />
              <span>{errorMessage}</span>
            </div>
          )}

          {/* Quick Demo Order Links */}
          <div className="mt-4 pt-3 border-t border-stone-100 flex flex-wrap items-center gap-2 text-xs text-stone-500">
            <span className="font-semibold">Try sample orders:</span>
            {orders.slice(0, 3).map((o) => (
              <button
                key={o.id}
                type="button"
                onClick={() => {
                  setSearchInput(o.orderNumber);
                  setSearchedOrder(o);
                  setErrorMessage('');
                }}
                className="font-mono text-orange-600 hover:underline bg-stone-100 px-2 py-0.5 rounded text-[11px]"
              >
                {o.orderNumber} ({o.status})
              </button>
            ))}
          </div>
        </div>

        {/* Order Tracking Dashboard Result */}
        {searchedOrder && (
          <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-xl space-y-8 animate-scale-in">
            
            {/* Top Bar with Status Pill & Actions */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-stone-200">
              <div>
                <div className="flex items-center gap-3">
                  <h2 className="font-serif text-xl sm:text-2xl font-bold text-stone-900">
                    Order #{searchedOrder.orderNumber}
                  </h2>
                  <span className={`text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider ${
                    searchedOrder.status === 'Delivered' 
                      ? 'bg-emerald-100 text-emerald-800' 
                      : searchedOrder.status === 'Shipped'
                        ? 'bg-sky-100 text-sky-800'
                        : 'bg-amber-100 text-amber-800'
                  }`}>
                    {searchedOrder.status}
                  </span>
                </div>
                <p className="text-xs text-stone-500 mt-1">
                  Placed on {new Date(searchedOrder.createdAt).toLocaleString()} &bull; AWB: <strong className="font-mono text-stone-800">{searchedOrder.trackingNumber}</strong>
                </p>
              </div>

              <button
                onClick={() => setActiveInvoiceOrder(searchedOrder)}
                className="inline-flex items-center gap-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold px-4 py-2.5 rounded-xl border border-stone-200 transition-colors"
              >
                <Printer className="w-3.5 h-3.5" />
                <span>Download Tax Invoice</span>
              </button>
            </div>

            {/* Visual 5-Stage Step Progress Pipeline */}
            <div>
              <h3 className="text-xs font-bold text-stone-400 uppercase tracking-wider mb-6">
                Live Shipment Progress Timeline
              </h3>

              <div className="relative">
                <div className="space-y-6">
                  {searchedOrder.timeline?.map((step, idx) => (
                    <div key={idx} className="flex items-start gap-4 relative">
                      {/* Line connector */}
                      {idx < searchedOrder.timeline.length - 1 && (
                        <div className={`absolute left-4 top-8 -bottom-4 w-0.5 ${
                          step.done ? 'bg-emerald-500' : 'bg-stone-200'
                        }`}></div>
                      )}

                      {/* Icon */}
                      <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 z-10 ${
                        step.done 
                          ? 'bg-emerald-600 text-white shadow-md' 
                          : 'bg-stone-100 text-stone-400 border border-stone-200'
                      }`}>
                        {step.done ? <CheckCircle2 className="w-4 h-4" /> : <Clock className="w-4 h-4" />}
                      </div>

                      {/* Details */}
                      <div className="flex-1 min-w-0 pt-0.5">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                          <h4 className={`text-sm font-bold ${step.done ? 'text-stone-900' : 'text-stone-400'}`}>
                            {step.stage}
                          </h4>
                          <span className="text-xs font-mono text-stone-400">{step.time}</span>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Destination & Ordered Items List */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-stone-200 text-xs">
              <div className="space-y-2">
                <h4 className="font-bold uppercase tracking-wider text-stone-400">Delivery Address</h4>
                <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200 text-stone-700 space-y-1">
                  <p className="font-bold text-stone-900">{searchedOrder.customer?.name}</p>
                  <p>{searchedOrder.customer?.address}</p>
                  <p>{searchedOrder.customer?.city}, {searchedOrder.customer?.state} - {searchedOrder.customer?.pincode}</p>
                  <p className="pt-1 text-stone-500">Phone: {searchedOrder.customer?.phone}</p>
                </div>
              </div>

              <div className="space-y-2">
                <h4 className="font-bold uppercase tracking-wider text-stone-400">Ordered Items ({searchedOrder.items?.length})</h4>
                <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200 divide-y divide-stone-200 max-h-40 overflow-y-auto">
                  {searchedOrder.items?.map((item, i) => (
                    <div key={i} className="py-2 first:pt-0 last:pb-0 flex items-center justify-between gap-2">
                      <div className="flex items-center gap-2 min-w-0">
                        <img src={item.image} alt={item.name} className="w-10 h-12 object-cover rounded-lg shrink-0" />
                        <div className="min-w-0">
                          <p className="font-bold text-stone-900 truncate text-[11px]">{item.name}</p>
                          <p className="text-[10px] text-stone-500">Size: {item.size} &bull; Qty: {item.quantity}</p>
                        </div>
                      </div>
                      <span className="font-bold font-mono text-stone-900">₹{(item.price * item.quantity).toLocaleString()}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>

          </div>
        )}

      </div>
    </div>
  );
};
