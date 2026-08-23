import React from 'react';
import { X, Printer, Download, CheckCircle2, ShieldCheck } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const InvoiceModal = () => {
  const { activeInvoiceOrder, setActiveInvoiceOrder } = useStore();

  if (!activeInvoiceOrder) return null;

  const order = activeInvoiceOrder;

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-10 flex items-center justify-center print:p-0 print:m-0">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity print:hidden"
        onClick={() => setActiveInvoiceOrder(null)}
      ></div>

      <div className="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-stone-200 overflow-hidden animate-scale-in p-6 sm:p-10 print:shadow-none print:border-none print:p-8 print:w-full">
        
        {/* Controls Bar (Hidden in Print) */}
        <div className="flex items-center justify-between pb-6 mb-6 border-b border-stone-200 print:hidden">
          <div className="flex items-center gap-2">
            <span className="text-xs font-bold text-stone-500 uppercase tracking-wider">Tax Invoice</span>
            <span className="text-xs font-mono font-bold text-stone-900 bg-stone-100 px-2 py-0.5 rounded">
              #{order.orderNumber}
            </span>
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={handlePrint}
              className="flex items-center gap-1.5 bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow"
            >
              <Printer className="w-3.5 h-3.5" />
              <span>Print / Save PDF</span>
            </button>

            <button 
              onClick={() => setActiveInvoiceOrder(null)}
              className="p-2 rounded-full text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Printable Document Header */}
        <div className="flex flex-col sm:flex-row justify-between gap-6 pb-6 border-b border-stone-200">
          <div>
            <div className="flex items-baseline gap-1 mb-1">
              <span className="font-serif text-3xl font-black tracking-widest text-[#0F172A]">AURA</span>
              <span className="text-xs font-bold text-orange-600 tracking-wider">&amp; CO.</span>
            </div>
            <p className="text-xs text-stone-500">AURA Luxury Apparel India Pvt Ltd</p>
            <p className="text-xs text-stone-500">GSTIN: 29AAACA1234F1Z8 &bull; CIN: U18101KA2024PTC123456</p>
            <p className="text-xs text-stone-500">Koramangala 4th Block, Bengaluru, KA 560034</p>
          </div>

          <div className="sm:text-right space-y-1">
            <h2 className="font-serif text-lg font-bold text-stone-900">ORIGINAL TAX INVOICE</h2>
            <p className="text-xs text-stone-600"><strong>Invoice No:</strong> INV-{order.orderNumber}</p>
            <p className="text-xs text-stone-600"><strong>Order Date:</strong> {new Date(order.createdAt).toLocaleDateString()}</p>
            <p className="text-xs text-stone-600"><strong>Payment Method:</strong> {order.paymentMethod}</p>
            <p className="text-xs text-stone-600">
              <strong>Status:</strong>{' '}
              <span className="text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">
                {order.paymentStatus || 'Paid'}
              </span>
            </p>
          </div>
        </div>

        {/* Customer & Shipping Details */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-stone-200 text-xs">
          <div>
            <h4 className="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-2 text-orange-600">
              Billed &amp; Shipped To:
            </h4>
            <p className="font-bold text-stone-900 text-sm">{order.customer?.name}</p>
            <p className="text-stone-600 mt-1">{order.customer?.address}</p>
            <p className="text-stone-600">{order.customer?.city}, {order.customer?.state} - {order.customer?.pincode}</p>
            <p className="text-stone-600 mt-1">Phone: {order.customer?.phone}</p>
            <p className="text-stone-600">Email: {order.customer?.email}</p>
          </div>

          <div>
            <h4 className="font-bold text-stone-900 uppercase tracking-wider text-[11px] mb-2 text-orange-600">
              Logistics &amp; Dispatch:
            </h4>
            <p className="text-stone-600"><strong>Carrier:</strong> BlueDart / Delhivery Air Express</p>
            <p className="text-stone-600"><strong>AWB / Tracking No:</strong> {order.trackingNumber}</p>
            <p className="text-stone-600"><strong>Estimated Delivery:</strong> {order.estimatedDelivery}</p>
            <p className="text-stone-600"><strong>Dispatch Hub:</strong> Bengaluru South Fulfillment Center</p>
          </div>
        </div>

        {/* Itemized Table */}
        <div className="py-6 border-b border-stone-200 overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-y border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-2.5 px-3">#</th>
                <th className="py-2.5 px-3">Item Description</th>
                <th className="py-2.5 px-3">HSN</th>
                <th className="py-2.5 px-3">Size / Color</th>
                <th className="py-2.5 px-3 text-center">Qty</th>
                <th className="py-2.5 px-3 text-right">Unit Price</th>
                <th className="py-2.5 px-3 text-right">Total</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {order.items?.map((item, idx) => (
                <tr key={idx}>
                  <td className="py-3 px-3 text-stone-400">{idx + 1}</td>
                  <td className="py-3 px-3 font-semibold text-stone-900 max-w-xs">{item.name}</td>
                  <td className="py-3 px-3 text-stone-500 font-mono">620520</td>
                  <td className="py-3 px-3">{item.size} / {item.color || 'Standard'}</td>
                  <td className="py-3 px-3 text-center font-bold">{item.quantity}</td>
                  <td className="py-3 px-3 text-right font-mono">₹{item.price.toLocaleString()}</td>
                  <td className="py-3 px-3 text-right font-mono font-bold">
                    ₹{(item.price * item.quantity).toLocaleString()}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Invoice Summary Calculations */}
        <div className="flex flex-col sm:flex-row justify-between items-start gap-6 pt-6">
          <div className="text-xs text-stone-500 space-y-1 max-w-xs">
            <p className="font-bold text-stone-800">Terms &amp; Conditions:</p>
            <p>1. 7-day return &amp; exchange policy applies with intact tags.</p>
            <p>2. Computer generated invoice; no physical signature required.</p>
            <div className="flex items-center gap-1 text-emerald-700 font-semibold pt-2">
              <ShieldCheck className="w-4 h-4 text-emerald-600" />
              <span>Certified Tax Compliance</span>
            </div>
          </div>

          <div className="w-full sm:w-64 space-y-2 text-xs text-stone-700">
            <div className="flex justify-between">
              <span>Subtotal:</span>
              <span className="font-semibold font-mono">₹{order.subtotal?.toLocaleString()}</span>
            </div>
            {order.discount > 0 && (
              <div className="flex justify-between text-emerald-700 font-semibold">
                <span>Coupon Discount:</span>
                <span className="font-mono">-₹{order.discount?.toLocaleString()}</span>
              </div>
            )}
            <div className="flex justify-between">
              <span>Shipping Fee:</span>
              <span className="font-semibold font-mono">
                {order.shippingFee === 0 ? 'FREE' : `₹${order.shippingFee}`}
              </span>
            </div>
            <div className="flex justify-between">
              <span>Integrated GST (5%):</span>
              <span className="font-semibold font-mono">₹{order.tax?.toLocaleString()}</span>
            </div>
            <div className="flex justify-between pt-2 border-t border-stone-300 text-sm font-bold text-stone-900">
              <span>Grand Total:</span>
              <span className="font-mono text-base text-orange-600">₹{order.total?.toLocaleString()}</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  );
};
