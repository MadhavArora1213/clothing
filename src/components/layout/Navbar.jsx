import React, { useState, useEffect } from 'react';
import { 
  Search, 
  ShoppingBag, 
  Heart, 
  User, 
  Menu, 
  X, 
  ChevronDown, 
  Sparkles, 
  LayoutDashboard, 
  Compass, 
  ArrowRight,
  ShieldCheck
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const Navbar = () => {
  const { 
    currentPage, 
    navigateTo, 
    cartItemCount, 
    wishlist, 
    setIsCartOpen, 
    setIsSearchOpen, 
    setIsAuthModalOpen, 
    user, 
    categories,
    isAdminView,
    setIsAdminView
  } = useStore();

  const [isScrolled, setIsScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [activeMegaMenu, setActiveMegaMenu] = useState(null);
  const [userDropdownOpen, setUserDropdownOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { name: 'Home', page: 'home' },
    { 
      name: 'Men', 
      page: 'shop', 
      params: { category: 'men' },
      hasMegaMenu: true,
      categoryId: 'men'
    },
    { 
      name: 'Women', 
      page: 'shop', 
      params: { category: 'women' },
      hasMegaMenu: true,
      categoryId: 'women'
    },
    { 
      name: 'Oversized Tees', 
      page: 'shop', 
      params: { category: 'oversized' }
    },
    { 
      name: 'Co-Ords', 
      page: 'shop', 
      params: { category: 'co-ords' }
    },
    { 
      name: 'Ethnic Fusion', 
      page: 'shop', 
      params: { category: 'ethnic-fusion' },
      badge: 'Arya Chic'
    },
    { 
      name: 'Sale', 
      page: 'shop', 
      params: { filter: 'sale' },
      highlight: true 
    }
  ];

  return (
    <header className={`sticky top-0 z-40 w-full transition-all duration-300 ${
      isScrolled 
        ? 'glass-header shadow-sm border-b border-stone-200/80 py-3' 
        : 'bg-white/95 border-b border-stone-100 py-4'
    }`}>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between gap-4">
          
          {/* Left: Mobile Menu Trigger & Desktop Logo */}
          <div className="flex items-center gap-4">
            <button 
              onClick={() => setMobileMenuOpen(true)}
              className="lg:hidden p-2 -ml-2 text-stone-800 hover:text-orange-600 focus:outline-none"
              aria-label="Open navigation menu"
            >
              <Menu className="w-6 h-6" />
            </button>

            {/* Brand Logo */}
            <button 
              onClick={() => navigateTo('home')}
              className="flex items-baseline gap-1 text-left group focus:outline-none"
            >
              <span className="font-serif text-2xl sm:text-3xl font-black tracking-widest text-[#0F172A] group-hover:text-orange-600 transition-colors">
                AURA
              </span>
              <span className="text-xs font-sans font-bold tracking-widest text-orange-600 uppercase">
                &amp; CO.
              </span>
            </button>
          </div>

          {/* Center: Desktop Navigation Bar */}
          <nav className="hidden lg:flex items-center gap-1 xl:gap-2">
            {navLinks.map((link) => (
              <div 
                key={link.name} 
                className="relative py-2"
                onMouseEnter={() => link.hasMegaMenu && setActiveMegaMenu(link.categoryId)}
                onMouseLeave={() => setActiveMegaMenu(null)}
              >
                <button
                  onClick={() => {
                    navigateTo(link.page, link.params || {});
                    setActiveMegaMenu(null);
                  }}
                  className={`px-3 py-1.5 rounded-full text-sm font-semibold tracking-wide transition-all duration-200 flex items-center gap-1.5 ${
                    link.highlight 
                      ? 'text-orange-600 bg-orange-50 hover:bg-orange-100'
                      : currentPage === link.page && (!link.params || JSON.stringify(link.params) === '{}')
                        ? 'text-[#0F172A] bg-stone-100 font-bold'
                        : 'text-stone-700 hover:text-[#0F172A] hover:bg-stone-50'
                  }`}
                >
                  {link.name}
                  {link.badge && (
                    <span className="text-[10px] font-bold uppercase bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">
                      {link.badge}
                    </span>
                  )}
                  {link.hasMegaMenu && (
                    <ChevronDown className="w-3.5 h-3.5 text-stone-400" />
                  )}
                </button>

                {/* Mega Menu Dropdown */}
                {link.hasMegaMenu && activeMegaMenu === link.categoryId && (
                  <div className="absolute top-full left-0 w-[600px] bg-white rounded-2xl shadow-2xl border border-stone-100 p-6 z-50 animate-scale-in">
                    <div className="grid grid-cols-2 gap-6">
                      <div>
                        <h4 className="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">
                          Categories in {link.name}
                        </h4>
                        <ul className="space-y-2">
                          {categories.find(c => c.id === link.categoryId)?.subcategories?.map(sub => (
                            <li key={sub.id}>
                              <button
                                onClick={() => {
                                  navigateTo('shop', { category: link.categoryId, subcategory: sub.slug });
                                  setActiveMegaMenu(null);
                                }}
                                className="text-sm font-medium text-stone-700 hover:text-orange-600 hover:translate-x-1 transition-all block text-left w-full py-1"
                              >
                                {sub.name}
                              </button>
                            </li>
                          ))}
                          <li className="pt-2 border-t border-stone-100">
                            <button
                              onClick={() => {
                                navigateTo('shop', { category: link.categoryId });
                                setActiveMegaMenu(null);
                              }}
                              className="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1"
                            >
                              Explore All {link.name} <ArrowRight className="w-3 h-3" />
                            </button>
                          </li>
                        </ul>
                      </div>

                      <div className="relative rounded-xl overflow-hidden group/card bg-stone-100">
                        <img 
                          src={categories.find(c => c.id === link.categoryId)?.image} 
                          alt={link.name} 
                          className="w-full h-44 object-cover group-hover/card:scale-105 transition-transform duration-500"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex flex-col justify-end p-4 text-white">
                          <span className="text-xs font-semibold text-orange-300">Featured Drop</span>
                          <p className="text-sm font-bold leading-tight">Trending {link.name}'s Silhouettes</p>
                        </div>
                      </div>
                    </div>
                  </div>
                )}
              </div>
            ))}
          </nav>

          {/* Right: Search, Admin Toggle, Wishlist, User, Cart */}
          <div className="flex items-center gap-2 sm:gap-3">
            {/* Search Trigger */}
            <button
              onClick={() => setIsSearchOpen(true)}
              className="p-2 text-stone-700 hover:text-orange-600 hover:bg-stone-100 rounded-full transition-colors relative"
              title="Search products (Cmd+K)"
            >
              <Search className="w-5 h-5" />
            </button>

            {/* Admin Panel Quick Switch Button */}
            <button
              onClick={() => {
                if (currentPage.startsWith('admin')) {
                  navigateTo('home');
                } else {
                  navigateTo('admin');
                }
              }}
              className={`hidden sm:flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full transition-all ${
                currentPage.startsWith('admin')
                  ? 'bg-orange-600 text-white shadow-md shadow-orange-200'
                  : 'bg-stone-100 hover:bg-stone-200 text-stone-800 border border-stone-200'
              }`}
              title="Toggle Admin Management Portal"
            >
              <LayoutDashboard className="w-3.5 h-3.5 text-orange-500" />
              <span>{currentPage.startsWith('admin') ? 'Storefront' : 'Admin Panel'}</span>
            </button>

            {/* Wishlist */}
            <button
              onClick={() => navigateTo('shop', { filter: 'wishlist' })}
              className="p-2 text-stone-700 hover:text-orange-600 hover:bg-stone-100 rounded-full transition-colors relative"
              title="View Wishlist"
            >
              <Heart className="w-5 h-5" />
              {wishlist.length > 0 && (
                <span className="absolute top-1 right-1 w-4 h-4 bg-orange-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-scale-in">
                  {wishlist.length}
                </span>
              )}
            </button>

            {/* User Account / Auth Dropdown */}
            <div className="relative">
              <button
                onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                className="p-2 text-stone-700 hover:text-orange-600 hover:bg-stone-100 rounded-full transition-colors flex items-center gap-1"
                title="Account Menu"
              >
                <User className="w-5 h-5" />
              </button>

              {userDropdownOpen && (
                <div 
                  className="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-stone-100 py-2 z-50 animate-scale-in"
                  onMouseLeave={() => setUserDropdownOpen(false)}
                >
                  <div className="px-4 py-2 border-b border-stone-100">
                    <p className="text-xs text-stone-400">Signed in as</p>
                    <p className="text-sm font-bold text-stone-900 truncate">{user ? user.name : 'Guest User'}</p>
                  </div>
                  
                  <button
                    onClick={() => {
                      setUserDropdownOpen(false);
                      navigateTo('account');
                    }}
                    className="w-full text-left px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 hover:text-orange-600 transition-colors"
                  >
                    My Account &amp; Orders
                  </button>
                  <button
                    onClick={() => {
                      setUserDropdownOpen(false);
                      navigateTo('track-order');
                    }}
                    className="w-full text-left px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 hover:text-orange-600 transition-colors"
                  >
                    Track Shipment
                  </button>
                  <button
                    onClick={() => {
                      setUserDropdownOpen(false);
                      navigateTo('admin');
                    }}
                    className="w-full text-left px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 hover:text-orange-600 font-semibold text-orange-600 transition-colors flex items-center justify-between"
                  >
                    <span>Admin Dashboard</span>
                    <span className="text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-bold">Portal</span>
                  </button>
                  
                  <div className="border-t border-stone-100 my-1"></div>
                  
                  <button
                    onClick={() => {
                      setUserDropdownOpen(false);
                      setIsAuthModalOpen(true);
                    }}
                    className="w-full text-left px-4 py-2 text-sm text-stone-500 hover:bg-stone-50 transition-colors"
                  >
                    Switch Account / Sign In
                  </button>
                </div>
              )}
            </div>

            {/* Shopping Bag Drawer Trigger */}
            <button
              onClick={() => setIsCartOpen(true)}
              className="flex items-center gap-2 bg-[#0F172A] hover:bg-stone-800 text-white px-3.5 py-2 rounded-full transition-all shadow-sm hover:shadow-md group"
              title="Open Shopping Bag"
            >
              <div className="relative">
                <ShoppingBag className="w-4 h-4 group-hover:scale-110 transition-transform" />
                {cartItemCount > 0 && (
                  <span className="absolute -top-2 -right-2.5 bg-orange-600 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center animate-scale-in shadow">
                    {cartItemCount}
                  </span>
                )}
              </div>
              <span className="text-xs font-bold hidden sm:inline-block">
                Bag
              </span>
            </button>

          </div>

        </div>
      </div>

      {/* Mobile Sidebar Navigation Drawer */}
      {mobileMenuOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div 
            className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
            onClick={() => setMobileMenuOpen(false)}
          ></div>

          <div className="fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-2xl z-50 p-6 flex flex-col justify-between overflow-y-auto animate-slide-in-right">
            <div>
              <div className="flex items-center justify-between pb-4 border-b border-stone-100">
                <div className="flex items-baseline gap-1">
                  <span className="font-serif text-2xl font-black tracking-widest text-[#0F172A]">AURA</span>
                  <span className="text-xs font-bold text-orange-600">&amp; CO.</span>
                </div>
                <button 
                  onClick={() => setMobileMenuOpen(false)}
                  className="p-1 rounded-full text-stone-400 hover:text-stone-900"
                >
                  <X className="w-6 h-6" />
                </button>
              </div>

              {/* Mobile Search bar */}
              <div className="mt-4">
                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    setIsSearchOpen(true);
                  }}
                  className="w-full flex items-center gap-3 bg-stone-100 text-stone-500 px-4 py-2.5 rounded-xl text-sm font-medium"
                >
                  <Search className="w-4 h-4" />
                  <span>Search styles, fabrics, sizes...</span>
                </button>
              </div>

              {/* Mobile Navigation Links */}
              <div className="mt-6 space-y-1">
                {navLinks.map((link) => (
                  <button
                    key={link.name}
                    onClick={() => {
                      navigateTo(link.page, link.params || {});
                      setMobileMenuOpen(false);
                    }}
                    className={`w-full flex items-center justify-between px-3 py-3 rounded-xl text-base font-semibold text-left transition-colors ${
                      link.highlight
                        ? 'text-orange-600 bg-orange-50'
                        : 'text-stone-800 hover:bg-stone-100'
                    }`}
                  >
                    <span>{link.name}</span>
                    {link.badge && (
                      <span className="text-[10px] font-bold uppercase bg-amber-100 text-amber-800 px-2 py-0.5 rounded">
                        {link.badge}
                      </span>
                    )}
                  </button>
                ))}
              </div>

              {/* Admin Portal shortcut for mobile */}
              <div className="mt-6 pt-4 border-t border-stone-100">
                <button
                  onClick={() => {
                    navigateTo('admin');
                    setMobileMenuOpen(false);
                  }}
                  className="w-full flex items-center gap-3 bg-stone-900 text-white px-4 py-3 rounded-xl text-sm font-bold justify-center"
                >
                  <LayoutDashboard className="w-4 h-4 text-orange-400" />
                  <span>Open Admin Management Panel</span>
                </button>
              </div>
            </div>

            <div className="pt-6 border-t border-stone-100 text-xs text-stone-500 space-y-2">
              <p>Need assistance? Contact our 24/7 Concierge</p>
              <p className="font-semibold text-stone-900">+91 (800) 420-AURA</p>
            </div>
          </div>
        </div>
      )}
    </header>
  );
};
