import React, { useState } from 'react';
import { 
  LayoutDashboard, 
  Package, 
  ShoppingBag, 
  FolderTree, 
  Users, 
  MessageSquare, 
  Tag, 
  Store, 
  LogOut, 
  Menu, 
  X, 
  Sparkles, 
  ShieldCheck,
  ChevronRight
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminLayout = ({ activeTab, setActiveTab, children }) => {
  const { navigateTo, enquiries, orders, products } = useStore();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const pendingOrdersCount = orders.filter(o => o.status === 'Pending' || o.status === 'Processing').length;
  const newEnquiriesCount = enquiries.filter(e => e.status === 'New').length;
  const lowStockCount = products.filter(p => p.sizes?.some(s => s.stock > 0 && s.stock <= 3)).length;

  const menuItems = [
    { id: 'dashboard', label: 'Dashboard Overview', icon: LayoutDashboard },
    { 
      id: 'products', 
      label: 'Products & Inventory', 
      icon: Package,
      badge: lowStockCount > 0 ? `${lowStockCount} Low` : null,
      badgeColor: 'bg-amber-100 text-amber-800'
    },
    { 
      id: 'orders', 
      label: 'Orders Pipeline', 
      icon: ShoppingBag,
      badge: pendingOrdersCount > 0 ? pendingOrdersCount : null,
      badgeColor: 'bg-orange-100 text-orange-800'
    },
    { id: 'categories', label: 'Categories & Drops', icon: FolderTree },
    { id: 'customers', label: 'Customer Directory', icon: Users },
    { 
      id: 'enquiries', 
      label: 'Client Enquiries', 
      icon: MessageSquare,
      badge: newEnquiriesCount > 0 ? `${newEnquiriesCount} New` : null,
      badgeColor: 'bg-emerald-100 text-emerald-800'
    },
    { id: 'coupons', label: 'Coupons & Promos', icon: Tag }
  ];

  return (
    <div className="min-h-screen bg-[#F5F5F4] flex flex-col antialiased">
      
      {/* Admin Topbar */}
      <header className="bg-white border-b border-stone-200 sticky top-0 z-30 px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <div className="flex items-center gap-4">
          <button 
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="lg:hidden p-1.5 rounded-lg text-stone-600 hover:bg-stone-100"
          >
            <Menu className="w-5 h-5" />
          </button>

          <div className="flex items-baseline gap-1.5 cursor-pointer" onClick={() => setActiveTab('dashboard')}>
            <span className="font-serif text-xl font-black tracking-widest text-[#0F172A]">AURA</span>
            <span className="text-[10px] font-bold text-orange-600 tracking-wider uppercase bg-orange-50 px-2 py-0.5 rounded">
              ADMIN PORTAL
            </span>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={() => navigateTo('home')}
            className="flex items-center gap-1.5 bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm group"
          >
            <Store className="w-3.5 h-3.5 text-orange-400 group-hover:scale-110 transition-transform" />
            <span>View Live Storefront</span>
          </button>
        </div>
      </header>

      {/* Main Container: Sidebar + Content */}
      <div className="flex-1 flex overflow-hidden">
        
        {/* Desktop Sidebar */}
        <aside className="hidden lg:flex flex-col justify-between w-64 bg-white border-r border-stone-200 p-4 shrink-0">
          <div className="space-y-1">
            <p className="text-[10px] font-bold uppercase tracking-wider text-stone-400 px-3 mb-2">
              Management Modules
            </p>
            {menuItems.map((item) => {
              const Icon = item.icon;
              const isActive = activeTab === item.id;
              return (
                <button
                  key={item.id}
                  onClick={() => setActiveTab(item.id)}
                  className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all ${
                    isActive 
                      ? 'bg-[#0F172A] text-white shadow-md' 
                      : 'text-stone-700 hover:bg-stone-100 hover:text-stone-900'
                  }`}
                >
                  <div className="flex items-center gap-3">
                    <Icon className={`w-4 h-4 ${isActive ? 'text-orange-400' : 'text-stone-500'}`} />
                    <span>{item.label}</span>
                  </div>
                  {item.badge && (
                    <span className={`text-[10px] font-bold px-1.5 py-0.5 rounded-full ${item.badgeColor}`}>
                      {item.badge}
                    </span>
                  )}
                </button>
              );
            })}
          </div>

          <div className="pt-4 border-t border-stone-200 space-y-2">
            <div className="bg-stone-50 p-3 rounded-2xl border border-stone-200/80 text-[11px] text-stone-600 space-y-1">
              <p className="font-bold text-stone-900">Signed In As:</p>
              <p className="truncate">Director (admin@auraco.com)</p>
              <div className="flex items-center gap-1 text-emerald-700 font-bold text-[10px] pt-1">
                <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Store Sync Active</span>
              </div>
            </div>

            <button
              onClick={() => navigateTo('home')}
              className="w-full flex items-center justify-center gap-2 text-xs font-semibold text-stone-500 hover:text-stone-900 py-2"
            >
              <LogOut className="w-3.5 h-3.5" />
              <span>Exit Admin Portal</span>
            </button>
          </div>
        </aside>

        {/* Mobile Drawer */}
        {sidebarOpen && (
          <div className="fixed inset-0 z-50 lg:hidden">
            <div className="fixed inset-0 bg-black/50" onClick={() => setSidebarOpen(false)}></div>
            <div className="fixed inset-y-0 left-0 w-64 bg-white shadow-2xl z-10 p-4 flex flex-col justify-between">
              <div>
                <div className="flex justify-between items-center pb-4 border-b border-stone-100">
                  <span className="font-serif text-lg font-bold">Admin Portal</span>
                  <button onClick={() => setSidebarOpen(false)}><X className="w-5 h-5" /></button>
                </div>
                <div className="space-y-1 mt-4">
                  {menuItems.map((item) => (
                    <button
                      key={item.id}
                      onClick={() => {
                        setActiveTab(item.id);
                        setSidebarOpen(false);
                      }}
                      className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold ${
                        activeTab === item.id ? 'bg-[#0F172A] text-white' : 'text-stone-700'
                      }`}
                    >
                      <span>{item.label}</span>
                      {item.badge && <span className="text-[10px] px-1.5 py-0.5 rounded bg-orange-100 text-orange-800">{item.badge}</span>}
                    </button>
                  ))}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Dynamic Admin Body */}
        <main className="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
          <div className="max-w-7xl mx-auto space-y-6">
            {children}
          </div>
        </main>

      </div>
    </div>
  );
};
