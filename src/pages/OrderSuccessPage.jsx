import React from 'react';
import { 
  CheckCircle2, 
  Package, 
  Printer, 
  ArrowRight, 
  Truck, 
  Calendar, 
  MapPin, 
  ShieldCheck 
} from 'lucide-react';
import { useStore } from '../context/StoreContext';

export const OrderSuccessPage = () => {
  const { pageParams, getOrderById, setActiveInvoiceOrder, navigateTo } = useStore();

  const order = getOrderById(pageParams?.orderId) || {
    id: 'ATL-DEMO',
    orderNumber: 'ATL-DEMO',
    createdAt: new Date().toISOString(),
    customer: { name: 'Customer', address: 'Bangalore' },
    items: [],
    total: 2499,
    estimatedDelivery: '3 days'
  };

  return (
    <div className="py-16 sm:py-24 bg-[#FAFAF9] min-h-[80vh] flex items-center justify-center animate-fade-in">
      <div className="max-w-2xl w-full mx-auto px-4 sm:px-6">
        
        <div className="bg-white rounded-3xl p-8 sm:p-12 border border-stone-200/80 shadow-xl text-center space-y-6">
          
          {/* Green Check Icon */}
          <div className="w-20 h-20 rounded-full bg-emerald-50 border-4 border-emerald-100 flex items-center justify-center mx-auto text-emerald-600 animate-scale-in">
            <CheckCircle2 className="w-10 h-10" />
          </div>

          <div className="space-y-2">
            <span className="text-xs font-bold uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
              Order Confirmed &amp; Dispatched for Packaging
            </span>
            <h1 className="font-serif text-3xl sm:text-4xl font-black text-[#0F172A]">
              Thank You for Your Order!
            </h1>
            <p className="text-xs sm:text-sm text-stone-500 max-w-md mx-auto">
              We've received your order and our fulfillment team is preparing your custom garment packaging.
            </p>
          </div>

          {/* Order Details Card */}
          <div className="bg-stone-50 rounded-2xl p-5 border border-stone-200 text-left space-y-3 text-xs">
            <div className="flex justify-between items-center pb-2 border-b border-stone-200">
              <span className="text-stone-500">Order Reference:</span>
              <span className="font-mono font-bold text-stone-900 text-sm">{order.orderNumber}</span>
            </div>

            <div className="flex justify-between items-center">
              <span className="text-stone-500 flex items-center gap-1.5">
                <Calendar className="w-3.5 h-3.5 text-orange-600" />
                <span>Estimated Arrival:</span>
              </span>
              <span className="font-bold text-stone-900">{order.estimatedDelivery}</span>
            </div>

            <div className="flex justify-between items-center">
              <span className="text-stone-500 flex items-center gap-1.5">
                <MapPin className="w-3.5 h-3.5 text-orange-600" />
                <span>Delivery Address:</span>
              </span>
              <span className="font-medium text-stone-900 truncate max-w-[240px]">
                {order.customer?.address}, {order.customer?.city}
              </span>
            </div>

            <div className="flex justify-between items-center pt-2 border-t border-stone-200">
              <span className="text-stone-500">Total Paid:</span>
              <span className="font-mono font-extrabold text-orange-600 text-base">₹{order.total?.toLocaleString()}</span>
            </div>
          </div>

          {/* Action CTAs */}
          <div className="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <button
              onClick={() => setActiveInvoiceOrder(order)}
              className="w-full sm:w-auto bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-6 py-3.5 rounded-2xl shadow transition-all flex items-center justify-center gap-2"
            >
              <Printer className="w-4 h-4" />
              <span>Download / Print Tax Invoice</span>
            </button>

            <button
              onClick={() => navigateTo('track-order', { orderId: order.orderNumber })}
              className="w-full sm:w-auto bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-6 py-3.5 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
            >
              <Truck className="w-4 h-4" />
              <span>Track Live Delivery</span>
            </button>
          </div>

          <div className="pt-4 border-t border-stone-100">
            <button
              onClick={() => navigateTo('shop')}
              className="text-xs font-bold text-stone-600 hover:text-orange-600 transition-colors"
            >
              &larr; Continue Exploring Collections
            </button>
          </div>

        </div>

      </div>
    </div>
  );
};
