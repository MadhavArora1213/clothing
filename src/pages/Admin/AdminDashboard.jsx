import React from 'react';
import { 
  DollarSign, 
  ShoppingBag, 
  Package, 
  Users, 
  TrendingUp, 
  AlertTriangle, 
  ArrowRight, 
  Clock, 
  CheckCircle2, 
  Printer, 
  Sparkles 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminDashboard = ({ setActiveTab }) => {
  const { 
    orders, 
    products, 
    customers, 
    enquiries, 
    updateOrderStatus, 
    setActiveInvoiceOrder 
  } = useStore();

  const totalRevenue = orders.reduce((sum, o) => sum + (o.total || 0), 0);
  const totalOrdersCount = orders.length;
  const totalProductsCount = products.length;
  const totalCustomersCount = customers.length;
  const lowStockProducts = products.filter(p => p.sizes?.some(s => s.stock > 0 && s.stock <= 4));

  const stats = [
    {
      title: 'Total Revenue',
      value: `₹${totalRevenue.toLocaleString()}`,
      change: '+24.8% vs last month',
      isPositive: true,
      icon: DollarSign,
      iconBg: 'bg-emerald-50 text-emerald-600'
    },
    {
      title: 'Total Orders',
      value: totalOrdersCount,
      change: '+18.2% new sales',
      isPositive: true,
      icon: ShoppingBag,
      iconBg: 'bg-orange-50 text-orange-600'
    },
    {
      title: 'Active Products',
      value: totalProductsCount,
      change: '8 Active Collections',
      isPositive: true,
      icon: Package,
      iconBg: 'bg-indigo-50 text-indigo-600'
    },
    {
      title: 'Total Customers',
      value: totalCustomersCount,
      change: '+12% repeat retention',
      isPositive: true,
      icon: Users,
      iconBg: 'bg-purple-50 text-purple-600'
    }
  ];

  const salesTrend = [
    { day: 'Mon', revenue: 18400, height: '45%' },
    { day: 'Tue', revenue: 24200, height: '60%' },
    { day: 'Wed', revenue: 31000, height: '75%' },
    { day: 'Thu', revenue: 22800, height: '55%' },
    { day: 'Fri', revenue: 38900, height: '90%' },
    { day: 'Sat', revenue: 45000, height: '100%' },
    { day: 'Sun', revenue: 29500, height: '70%' }
  ];

  return (
    <div className="space-y-6 animate-fade-in">
      
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Store Performance Overview
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Real-time analytics for AURA &amp; CO. luxury e-commerce operations
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => setActiveTab('products')}
            className="bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all"
          >
            + Add New Product
          </button>
        </div>
      </div>

      {/* KPI Cards Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((st) => {
          const Icon = st.icon;
          return (
            <div
              key={st.title}
              className="bg-white rounded-3xl p-5 border border-stone-200 shadow-sm space-y-3"
            >
              <div className="flex items-center justify-between">
                <span className="text-xs font-bold text-stone-500 uppercase tracking-wider">{st.title}</span>
                <div className={`w-9 h-9 rounded-2xl flex items-center justify-center ${st.iconBg}`}>
                  <Icon className="w-5 h-5" />
                </div>
              </div>
              <p className="font-serif text-2xl font-black text-stone-900">{st.value}</p>
              <div className="flex items-center gap-1.5 text-xs text-emerald-700 font-semibold">
                <TrendingUp className="w-3.5 h-3.5" />
                <span>{st.change}</span>
              </div>
            </div>
          );
        })}
      </div>

      {/* Analytics Chart & Low Stock Alerts */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {/* Sales Trend Bar Chart */}
        <div className="lg:col-span-8 bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-6">
          <div className="flex items-center justify-between">
            <div>
              <h3 className="font-serif text-base font-bold text-stone-900">Revenue &amp; Sales Trajectory</h3>
              <p className="text-xs text-stone-400">Weekly sales distribution in INR</p>
            </div>
            <span className="text-xs font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">
              This Week: ₹2,09,800
            </span>
          </div>

          <div className="h-56 flex items-end justify-between gap-3 pt-6 px-2">
            {salesTrend.map((col) => (
              <div key={col.day} className="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                <div className="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold text-stone-700 bg-stone-100 px-1.5 py-0.5 rounded font-mono">
                  ₹{col.revenue.toLocaleString()}
                </div>
                <div 
                  className="w-full bg-stone-100 hover:bg-orange-500 rounded-t-xl transition-all duration-300 relative"
                  style={{ height: col.height }}
                >
                  <div className="absolute inset-0 bg-gradient-to-t from-orange-600 to-amber-400 rounded-t-xl opacity-80 group-hover:opacity-100"></div>
                </div>
                <span className="text-xs font-bold text-stone-500">{col.day}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Low Stock Alerts & Quick Enquiries */}
        <div className="lg:col-span-4 space-y-6">
          {/* Low Stock Widget */}
          <div className="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2 text-amber-700 font-bold text-xs uppercase tracking-wider">
                <AlertTriangle className="w-4 h-4 text-amber-600" />
                <span>Low Inventory Watch</span>
              </div>
              <button 
                onClick={() => setActiveTab('products')}
                className="text-[11px] font-bold text-orange-600 hover:underline"
              >
                Manage
              </button>
            </div>

            <div className="space-y-2.5 text-xs">
              {lowStockProducts.slice(0, 3).map((prod) => (
                <div key={prod.id} className="p-3 rounded-2xl bg-stone-50 border border-stone-100 flex items-center justify-between gap-2">
                  <div className="min-w-0">
                    <p className="font-bold text-stone-900 truncate">{prod.name}</p>
                    <p className="text-[10px] text-stone-500">SKU: {prod.sku}</p>
                  </div>
                  <span className="text-[10px] font-extrabold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full shrink-0">
                    {prod.sizes?.find(s => s.stock > 0 && s.stock <= 4)?.stock} Left in {prod.sizes?.find(s => s.stock > 0 && s.stock <= 4)?.size}
                  </span>
                </div>
              ))}
            </div>
          </div>

          {/* Quick Enquiries Alert */}
          <div className="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-3">
            <div className="flex items-center justify-between">
              <h4 className="font-serif text-sm font-bold text-stone-900">Recent Customer Tickets</h4>
              <button onClick={() => setActiveTab('enquiries')} className="text-xs text-orange-600 font-bold hover:underline">
                View All ({enquiries.length})
              </button>
            </div>
            <div className="space-y-2 text-xs">
              {enquiries.slice(0, 2).map((enq) => (
                <div key={enq.id} className="p-2.5 rounded-xl bg-stone-50 border border-stone-100 space-y-1">
                  <div className="flex items-center justify-between">
                    <span className="font-bold text-stone-900">{enq.name}</span>
                    <span className={`text-[9px] font-bold px-1.5 py-0.2 rounded ${
                      enq.status === 'New' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-700'
                    }`}>
                      {enq.status}
                    </span>
                  </div>
                  <p className="text-[11px] text-stone-500 line-clamp-1">{enq.subject}</p>
                </div>
              ))}
            </div>
          </div>
        </div>

      </div>

      {/* Recent Orders Pipeline Table */}
      <div className="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm space-y-4">
        <div className="flex items-center justify-between">
          <div>
            <h3 className="font-serif text-lg font-bold text-stone-900">Recent Store Orders</h3>
            <p className="text-xs text-stone-400">Manage order confirmation and shipping status in real-time</p>
          </div>
          <button 
            onClick={() => setActiveTab('orders')}
            className="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1"
          >
            <span>View Full Pipeline</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-y border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3 px-4">Order ID</th>
                <th className="py-3 px-4">Customer</th>
                <th className="py-3 px-4">Items</th>
                <th className="py-3 px-4">Total</th>
                <th className="py-3 px-4">Payment</th>
                <th className="py-3 px-4">Current Status</th>
                <th className="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {orders.slice(0, 5).map((order) => (
                <tr key={order.id} className="hover:bg-stone-50/80 transition-colors">
                  <td className="py-3 px-4 font-mono font-bold text-stone-900">{order.orderNumber}</td>
                  <td className="py-3 px-4">
                    <p className="font-bold text-stone-900">{order.customer?.name}</p>
                    <p className="text-[10px] text-stone-400">{order.customer?.city}</p>
                  </td>
                  <td className="py-3 px-4">
                    <span className="bg-stone-100 px-2 py-0.5 rounded font-semibold text-stone-700">
                      {order.items?.length} items
                    </span>
                  </td>
                  <td className="py-3 px-4 font-mono font-bold text-stone-900">₹{order.total?.toLocaleString()}</td>
                  <td className="py-3 px-4 text-[11px] text-stone-500">{order.paymentMethod}</td>
                  <td className="py-3 px-4">
                    <select
                      value={order.status}
                      onChange={(e) => updateOrderStatus(order.id, e.target.value)}
                      className={`text-[11px] font-bold px-2 py-1 rounded-lg border focus:outline-none cursor-pointer ${
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
                  <td className="py-3 px-4 text-right">
                    <button
                      onClick={() => setActiveInvoiceOrder(order)}
                      className="p-1.5 text-stone-400 hover:text-stone-900 transition-colors"
                      title="Print Invoice"
                    >
                      <Printer className="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

    </div>
  );
};
