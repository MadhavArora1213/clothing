import React, { useState } from 'react';
import { StoreProvider, useStore } from './context/StoreContext';

// Layout Components
import { AnnouncementBar } from './components/layout/AnnouncementBar';
import { Navbar } from './components/layout/Navbar';
import { Footer } from './components/layout/Footer';
import { CartDrawer } from './components/layout/CartDrawer';
import { SearchModal } from './components/layout/SearchModal';
import { AuthModal } from './components/layout/AuthModal';
import { ToastContainer } from './components/layout/Toast';

// Modals
import { SizeChartModal } from './components/common/SizeChartModal';
import { QuickViewModal } from './components/common/QuickViewModal';
import { InvoiceModal } from './components/common/InvoiceModal';

// Customer Pages
import { HomePage } from './pages/HomePage';
import { ShopPage } from './pages/ShopPage';
import { ProductDetailPage } from './pages/ProductDetailPage';
import { CartPage } from './pages/CartPage';
import { CheckoutPage } from './pages/CheckoutPage';
import { OrderSuccessPage } from './pages/OrderSuccessPage';
import { TrackOrderPage } from './pages/TrackOrderPage';
import { AccountPage } from './pages/AccountPage';
import { ContactPage } from './pages/ContactPage';
import { PolicyPages } from './pages/PolicyPages';

// Admin Pages
import { AdminLayout } from './pages/Admin/AdminLayout';
import { AdminDashboard } from './pages/Admin/AdminDashboard';
import { AdminProducts } from './pages/Admin/AdminProducts';
import { AdminOrders } from './pages/Admin/AdminOrders';
import { AdminCategories } from './pages/Admin/AdminCategories';
import { AdminCustomers } from './pages/Admin/AdminCustomers';
import { AdminEnquiries } from './pages/Admin/AdminEnquiries';
import { AdminCoupons } from './pages/Admin/AdminCoupons';

const MainApp = () => {
  const { currentPage } = useStore();
  const [adminTab, setAdminTab] = useState('dashboard');

  const isAdminRoute = currentPage.startsWith('admin');

  return (
    <div className="min-h-screen flex flex-col bg-[#FAFAF9] text-[#1C1917] font-sans">
      
      {/* If we're on the Admin Portal */}
      {isAdminRoute ? (
        <AdminLayout activeTab={adminTab} setActiveTab={setAdminTab}>
          {adminTab === 'dashboard' && <AdminDashboard setActiveTab={setAdminTab} />}
          {adminTab === 'products' && <AdminProducts />}
          {adminTab === 'orders' && <AdminOrders />}
          {adminTab === 'categories' && <AdminCategories />}
          {adminTab === 'customers' && <AdminCustomers />}
          {adminTab === 'enquiries' && <AdminEnquiries />}
          {adminTab === 'coupons' && <AdminCoupons />}
        </AdminLayout>
      ) : (
        /* Customer Storefront */
        <>
          <AnnouncementBar />
          <Navbar />
          
          <main className="flex-1">
            {currentPage === 'home' && <HomePage />}
            {currentPage === 'shop' && <ShopPage />}
            {currentPage === 'product' && <ProductDetailPage />}
            {currentPage === 'cart' && <CartPage />}
            {currentPage === 'checkout' && <CheckoutPage />}
            {currentPage === 'order-success' && <OrderSuccessPage />}
            {currentPage === 'track-order' && <TrackOrderPage />}
            {currentPage === 'account' && <AccountPage />}
            {currentPage === 'contact' && <ContactPage />}
            {currentPage === 'shipping-policy' && <PolicyPages type="shipping" />}
            {currentPage === 'returns-policy' && <PolicyPages type="returns" />}
            {currentPage === 'faqs' && <PolicyPages type="faqs" />}
            {currentPage === 'privacy-policy' && <PolicyPages type="privacy" />}
            {currentPage === 'terms' && <PolicyPages type="terms" />}
          </main>

          <Footer />
        </>
      )}

      {/* Global Drawers & Modals */}
      <CartDrawer />
      <SearchModal />
      <AuthModal />
      <SizeChartModal />
      <QuickViewModal />
      <InvoiceModal />
      <ToastContainer />

    </div>
  );
};

export default function App() {
  return (
    <StoreProvider>
      <MainApp />
    </StoreProvider>
  );
}
