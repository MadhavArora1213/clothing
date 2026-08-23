import React, { useState } from 'react';
import { 
  Search, 
  Filter, 
  Printer, 
  Eye, 
  Clock, 
  CheckCircle2, 
  Truck, 
  X, 
  ShoppingBag, 
  MapPin, 
  CreditCard, 
  User 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminOrders = () => {
  const { orders, updateOrderStatus, setActiveInvoiceOrder } = useStore();

  const [searchTerm, setSearchTerm] = useState('');
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [selectedOrderDetails, setSelectedOrderDetails] = useState(null);

  const statuses = ['all', 'Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

  const filteredOrders = orders.filter(o => {
    const matchSearch = 
      o.orderNumber?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      o.customer?.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      o.customer?.phone?.includes(searchTerm);
    const matchStatus = selectedStatus === 'all' || o.status === selectedStatus;
    return matchSearch && matchStatus;
  });

  return (
    <div className="space-y-6 animate-fade-in">
      
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Orders Pipeline &amp; Fulfillment
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Track, update, and manage customer shipments across India
          </p>
        </div>

        <div className="flex items-center gap-2">
          <span className="text-xs font-bold text-stone-600 bg-white border border-stone-200 px-3 py-2 rounded-xl shadow-sm">
            Total Orders: {orders.length}
          </span>
        </div>
      </div>

      {/* Filter & Search Bar */}
      <div className="bg-white rounded-3xl p-4 border border-stone-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div className="relative flex-1 w-full sm:w-auto">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search by Order ID (e.g. ATL-9842), customer name or phone..."
            className="w-full bg-stone-50 border border-stone-200 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500"
          />
        </div>

        {/* Status Filter Tabs */}
        <div className="flex flex-wrap gap-1.5 overflow-x-auto hide-scrollbar w-full sm:w-auto">
          {statuses.map(st => (
            <button
              key={st}
              onClick={() => setSelectedStatus(st)}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all ${
                selectedStatus === st 
                  ? 'bg-[#0F172A] text-white shadow-sm' 
                  : 'bg-stone-100 text-stone-600 hover:bg-stone-200'
              }`}
            >
              {st}
            </button>
          ))}
        </div>
      </div>

      {/* Orders List Table */}
      <div className="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-b border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3.5 px-4">Order Ref</th>
                <th className="py-3.5 px-4">Customer Details</th>
                <th className="py-3.5 px-4">Items &amp; Sizes</th>
                <th className="py-3.5 px-4">Grand Total</th>
                <th className="py-3.5 px-4">Payment</th>
                <th className="py-3.5 px-4">Fulfillment Status</th>
                <th className="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {filteredOrders.map((order) => (
                <tr key={order.id} className="hover:bg-stone-50/80 transition-colors">
                  
                  <td className="py-3.5 px-4">
                    <p className="font-mono font-bold text-stone-900">{order.orderNumber}</p>
                    <p className="text-[10px] text-stone-400 mt-0.5">{new Date(order.createdAt).toLocaleDateString()}</p>
                  </td>

                  <td className="py-3.5 px-4">
                    <p className="font-bold text-stone-900">{order.customer?.name}</p>
                    <p className="text-[11px] text-stone-500">{order.customer?.city} ({order.customer?.pincode})</p>
                    <p className="text-[10px] text-stone-400 font-mono">{order.customer?.phone}</p>
                  </td>

                  <td className="py-3.5 px-4">
                    <div className="space-y-1 max-w-xs">
                      {order.items?.map((item, idx) => (
                        <div key={idx} className="flex items-center gap-1.5 text-[11px]">
                          <span className="font-bold text-stone-700">{item.quantity}x</span>
                          <span className="truncate text-stone-900">{item.name}</span>
                          <span className="text-[10px] text-stone-400">({item.size})</span>
                        </div>
                      ))}
                    </div>
                  </td>

                  <td className="py-3.5 px-4 font-mono font-bold text-stone-900">
                    ₹{order.total?.toLocaleString()}
                  </td>

                  <td className="py-3.5 px-4">
                    <span className="text-[11px] text-stone-700 block">{order.paymentMethod}</span>
                    <span className={`text-[9px] font-bold px-1.5 py-0.2 rounded inline-block mt-0.5 ${
                      order.paymentStatus === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                    }`}>
                      {order.paymentStatus || 'Pending'}
                    </span>
                  </td>

                  <td className="py-3.5 px-4">
                    <select
                      value={order.status}
                      onChange={(e) => updateOrderStatus(order.id, e.target.value)}
                      className={`text-xs font-bold px-2.5 py-1.5 rounded-xl border focus:outline-none cursor-pointer ${
                        order.status === 'Delivered'
                          ? 'bg-emerald-50 border-emerald-300 text-emerald-800'
                          : order.status === 'Shipped'
                            ? 'bg-sky-50 border-sky-300 text-sky-800'
                            : 'bg-amber-50 border-amber-300 text-amber-800'
                      }`}
                    >
                      <option value="Pending">Pending</option>
                      <option value="Confirmed">Confirmed</option>
                      <option value="Processing">Processing</option>
                      <option value="Shipped">Shipped</option>
                      <option value="Delivered">Delivered</option>
                      <option value="Cancelled">Cancelled</option>
                    </select>
                  </td>

                  <td className="py-3.5 px-4 text-right">
                    <div className="flex items-center justify-end gap-1.5">
                      <button
                        onClick={() => setSelectedOrderDetails(order)}
                        className="p-1.5 text-stone-600 hover:text-orange-600 rounded-lg hover:bg-stone-100"
                        title="View Complete Order"
                      >
                        <Eye className="w-4 h-4" />
                      </button>

                      <button
                        onClick={() => setActiveInvoiceOrder(order)}
                        className="p-1.5 text-stone-600 hover:text-stone-900 rounded-lg hover:bg-stone-100"
                        title="Tax Invoice"
                      >
                        <Printer className="w-4 h-4" />
                      </button>
                    </div>
                  </td>

                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Order Details Modal */}
      {selectedOrderDetails && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-10 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setSelectedOrderDetails(null)}></div>
          <div className="relative w-full max-w-2xl bg-white rounded-3xl p-6 sm:p-8 shadow-2xl z-10 animate-scale-in space-y-6">
            
            <div className="flex items-center justify-between pb-4 border-b border-stone-100">
              <div>
                <h3 className="font-serif text-xl font-bold text-stone-900">
                  Order Details: #{selectedOrderDetails.orderNumber}
                </h3>
                <p className="text-xs text-stone-500 mt-0.5">
                  Placed on {new Date(selectedOrderDetails.createdAt).toLocaleString()}
                </p>
              </div>
              <button onClick={() => setSelectedOrderDetails(null)}>
                <X className="w-5 h-5 text-stone-400" />
              </button>
            </div>

            {/* Customer & Address */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200/80 space-y-1">
                <h4 className="font-bold text-stone-900 uppercase tracking-wider text-[10px] text-orange-600">
                  Customer &amp; Shipping
                </h4>
                <p className="font-bold text-stone-900">{selectedOrderDetails.customer?.name}</p>
                <p className="text-stone-600">{selectedOrderDetails.customer?.address}</p>
                <p className="text-stone-600">{selectedOrderDetails.customer?.city}, {selectedOrderDetails.customer?.state} - {selectedOrderDetails.customer?.pincode}</p>
                <p className="text-stone-500 pt-1">Phone: {selectedOrderDetails.customer?.phone}</p>
                <p className="text-stone-500">Email: {selectedOrderDetails.customer?.email}</p>
              </div>

              <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200/80 space-y-1">
                <h4 className="font-bold text-stone-900 uppercase tracking-wider text-[10px] text-orange-600">
                  Logistics &amp; Status
                </h4>
                <p className="text-stone-700"><strong>Status:</strong> {selectedOrderDetails.status}</p>
                <p className="text-stone-700"><strong>Payment:</strong> {selectedOrderDetails.paymentMethod}</p>
                <p className="text-stone-700"><strong>Airway Bill (AWB):</strong> {selectedOrderDetails.trackingNumber}</p>
                <p className="text-stone-700"><strong>Estimated Arrival:</strong> {selectedOrderDetails.estimatedDelivery}</p>
              </div>
            </div>

            {/* Items */}
            <div>
              <h4 className="font-bold text-xs text-stone-900 mb-2">Order Items</h4>
              <div className="space-y-2 max-h-48 overflow-y-auto divide-y divide-stone-100">
                {selectedOrderDetails.items?.map((item, idx) => (
                  <div key={idx} className="pt-2 first:pt-0 flex items-center justify-between gap-3 text-xs">
                    <div className="flex items-center gap-3">
                      <img src={item.image} alt={item.name} className="w-12 h-14 object-cover rounded-xl shrink-0" />
                      <div>
                        <p className="font-bold text-stone-900">{item.name}</p>
                        <p className="text-[11px] text-stone-500">Size: {item.size} &bull; Qty: {item.quantity}</p>
                      </div>
                    </div>
                    <span className="font-mono font-bold text-stone-900">
                      ₹{(item.price * item.quantity).toLocaleString()}
                    </span>
                  </div>
                ))}
              </div>
            </div>

            {/* Actions */}
            <div className="flex gap-3 pt-3 border-t border-stone-100">
              <button
                onClick={() => {
                  setActiveInvoiceOrder(selectedOrderDetails);
                  setSelectedOrderDetails(null);
                }}
                className="flex-1 bg-[#0F172A] hover:bg-stone-800 text-white font-bold py-3 rounded-xl text-xs transition-colors flex items-center justify-center gap-1.5"
              >
                <Printer className="w-4 h-4" />
                <span>Download / Print Tax Invoice</span>
              </button>

              <button
                onClick={() => setSelectedOrderDetails(null)}
                className="px-6 py-3 border border-stone-200 rounded-xl font-semibold text-stone-600 hover:bg-stone-50 text-xs"
              >
                Close
              </button>
            </div>

          </div>
        </div>
      )}

    </div>
  );
};
