import React, { createContext, useContext, useState, useEffect } from 'react';
import { initialProducts } from '../data/initialProducts';
import { initialCategories } from '../data/initialCategories';
import { initialCoupons, initialOrders, initialCustomers, initialEnquiries } from '../data/mockData';

const StoreContext = createContext();

export const useStore = () => useContext(StoreContext);

export const StoreProvider = ({ children }) => {
  // Navigation Router state
  const [currentPage, setCurrentPage] = useState('home');
  const [pageParams, setPageParams] = useState({});

  const navigateTo = (page, params = {}) => {
    setCurrentPage(page);
    setPageParams(params);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  // 1. Products State with LocalStorage
  const [products, setProducts] = useState(() => {
    const saved = localStorage.getItem('aura_products');
    return saved ? JSON.parse(saved) : initialProducts;
  });

  useEffect(() => {
    localStorage.setItem('aura_products', JSON.stringify(products));
  }, [products]);

  const addProduct = (newProduct) => {
    const productWithId = {
      ...newProduct,
      id: 'prod-' + Date.now(),
      slug: newProduct.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''),
      sku: newProduct.sku || ('AUR-' + Math.random().toString(36).substring(2, 7).toUpperCase()),
      rating: 5.0,
      reviewCount: 0,
      discountPercent: newProduct.originalPrice > newProduct.price 
        ? Math.round(((newProduct.originalPrice - newProduct.price) / newProduct.originalPrice) * 100)
        : 0
    };
    setProducts(prev => [productWithId, ...prev]);
    addToast('Product published successfully!', 'success');
    return productWithId;
  };

  const updateProduct = (id, updatedFields) => {
    setProducts(prev => prev.map(p => {
      if (p.id === id) {
        const updated = { ...p, ...updatedFields };
        if (updatedFields.price || updatedFields.originalPrice) {
          const orig = updatedFields.originalPrice || p.originalPrice;
          const pr = updatedFields.price || p.price;
          updated.discountPercent = orig > pr ? Math.round(((orig - pr) / orig) * 100) : 0;
        }
        return updated;
      }
      return p;
    }));
    addToast('Product updated successfully!', 'success');
  };

  const deleteProduct = (id) => {
    setProducts(prev => prev.filter(p => p.id !== id));
    addToast('Product deleted from inventory', 'info');
  };

  const getProductBySlug = (slug) => products.find(p => p.slug === slug);
  const getProductById = (id) => products.find(p => p.id === id);

  // 2. Categories State
  const [categories, setCategories] = useState(() => {
    const saved = localStorage.getItem('aura_categories');
    return saved ? JSON.parse(saved) : initialCategories;
  });

  useEffect(() => {
    localStorage.setItem('aura_categories', JSON.stringify(categories));
  }, [categories]);

  const addCategory = (cat) => {
    const newCat = {
      ...cat,
      id: cat.slug || cat.name.toLowerCase().replace(/\s+/g, '-'),
      itemCount: 0,
      isActive: true,
      subcategories: cat.subcategories || []
    };
    setCategories(prev => [...prev, newCat]);
    addToast('Category added', 'success');
  };

  const updateCategory = (id, fields) => {
    setCategories(prev => prev.map(c => c.id === id ? { ...c, ...fields } : c));
    addToast('Category updated', 'success');
  };

  const deleteCategory = (id) => {
    setCategories(prev => prev.filter(c => c.id !== id));
    addToast('Category removed', 'info');
  };

  // 3. Cart State
  const [cart, setCart] = useState(() => {
    const saved = localStorage.getItem('aura_cart');
    return saved ? JSON.parse(saved) : [];
  });

  useEffect(() => {
    localStorage.setItem('aura_cart', JSON.stringify(cart));
  }, [cart]);

  const addToCart = (product, size = 'M', color = null, quantity = 1) => {
    const chosenColor = color || (product.colors && product.colors[0]?.name) || 'Default';
    setCart(prev => {
      const existingIndex = prev.findIndex(item => 
        item.productId === product.id && item.size === size && item.color === chosenColor
      );

      if (existingIndex > -1) {
        const updated = [...prev];
        updated[existingIndex].quantity += quantity;
        return updated;
      } else {
        return [...prev, {
          cartItemId: `${product.id}-${size}-${chosenColor}-${Date.now()}`,
          productId: product.id,
          name: product.name,
          slug: product.slug,
          price: product.price,
          originalPrice: product.originalPrice,
          image: product.images[0],
          size,
          color: chosenColor,
          quantity
        }];
      }
    });

    addToast(`Added "${product.name}" (${size}) to bag!`, 'success');
    setIsCartOpen(true);
  };

  const updateCartQuantity = (cartItemId, delta) => {
    setCart(prev => prev.map(item => {
      if (item.cartItemId === cartItemId) {
        const newQty = item.quantity + delta;
        return newQty > 0 ? { ...item, quantity: newQty } : null;
      }
      return item;
    }).filter(Boolean));
  };

  const removeFromCart = (cartItemId) => {
    setCart(prev => prev.filter(item => item.cartItemId !== cartItemId));
    addToast('Item removed from bag', 'info');
  };

  const clearCart = () => {
    setCart([]);
  };

  // Cart Calculations
  const cartSubtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const cartItemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
  const freeShippingThreshold = 999;
  const isFreeShipping = cartSubtotal >= freeShippingThreshold || cartSubtotal === 0;
  const freeShippingRemaining = Math.max(0, freeShippingThreshold - cartSubtotal);

  // 4. Coupons State
  const [coupons, setCoupons] = useState(() => {
    const saved = localStorage.getItem('aura_coupons');
    return saved ? JSON.parse(saved) : initialCoupons;
  });
  const [appliedCoupon, setAppliedCoupon] = useState(null);

  useEffect(() => {
    localStorage.setItem('aura_coupons', JSON.stringify(coupons));
  }, [coupons]);

  const applyCouponCode = (code) => {
    if (!code) return { success: false, message: 'Please enter a coupon code' };
    const found = coupons.find(c => c.code.toUpperCase() === code.toUpperCase() && c.isActive);
    if (!found) {
      return { success: false, message: 'Invalid or expired coupon code.' };
    }
    if (cartSubtotal < found.minSpend) {
      return { success: false, message: `Min. cart value for ${found.code} is ₹${found.minSpend}` };
    }

    setAppliedCoupon(found);
    addToast(`Coupon "${found.code}" applied successfully!`, 'success');
    return { success: true, coupon: found };
  };

  const removeCoupon = () => {
    setAppliedCoupon(null);
    addToast('Coupon removed', 'info');
  };

  let cartDiscount = 0;
  if (appliedCoupon && cartSubtotal > 0) {
    if (appliedCoupon.type === 'percentage') {
      cartDiscount = (cartSubtotal * appliedCoupon.discount) / 100;
    } else {
      cartDiscount = Math.min(appliedCoupon.discount, cartSubtotal);
    }
  }

  const shippingFee = cartSubtotal > 0 && !isFreeShipping ? 99 : 0;
  const estimatedTax = Math.round((cartSubtotal - cartDiscount) * 0.05); // 5% GST
  const cartGrandTotal = Math.max(0, cartSubtotal - cartDiscount + shippingFee + estimatedTax);

  // 5. Wishlist State
  const [wishlist, setWishlist] = useState(() => {
    const saved = localStorage.getItem('aura_wishlist');
    return saved ? JSON.parse(saved) : ['prod-1', 'prod-5'];
  });

  useEffect(() => {
    localStorage.setItem('aura_wishlist', JSON.stringify(wishlist));
  }, [wishlist]);

  const toggleWishlist = (productId) => {
    setWishlist(prev => {
      const exists = prev.includes(productId);
      if (exists) {
        addToast('Removed from wishlist', 'info');
        return prev.filter(id => id !== productId);
      } else {
        addToast('Added to wishlist!', 'success');
        return [...prev, productId];
      }
    });
  };

  const isInWishlist = (productId) => wishlist.includes(productId);

  // 6. Orders State
  const [orders, setOrders] = useState(() => {
    const saved = localStorage.getItem('aura_orders');
    return saved ? JSON.parse(saved) : initialOrders;
  });

  useEffect(() => {
    localStorage.setItem('aura_orders', JSON.stringify(orders));
  }, [orders]);

  const createOrder = (orderData) => {
    const orderId = 'ATL-' + Math.floor(1000 + Math.random() * 9000);
    const trackingNo = 'DEL-IN-' + Math.floor(1000000 + Math.random() * 9000000);
    const newOrder = {
      id: orderId,
      orderNumber: orderId,
      createdAt: new Date().toISOString(),
      customer: orderData.customer,
      items: orderData.items || [...cart],
      subtotal: orderData.subtotal || cartSubtotal,
      discount: orderData.discount || cartDiscount,
      shippingFee: orderData.shippingFee ?? shippingFee,
      tax: orderData.tax || estimatedTax,
      total: orderData.total || cartGrandTotal,
      paymentMethod: orderData.paymentMethod || 'Credit Card',
      paymentStatus: orderData.paymentMethod === 'Cash On Delivery (COD)' ? 'Pending' : 'Paid',
      status: 'Confirmed',
      trackingNumber: trackingNo,
      estimatedDelivery: new Date(Date.now() + 4 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      timeline: [
        { stage: 'Order Placed', time: new Date().toLocaleString(), done: true },
        { stage: 'Confirmed', time: new Date().toLocaleString(), done: true },
        { stage: 'Processing & Packed', time: 'Pending', done: false },
        { stage: 'Shipped via Express Air', time: 'Pending', done: false },
        { stage: 'Out for Delivery', time: 'Pending', done: false },
        { stage: 'Delivered', time: 'Pending', done: false }
      ]
    };

    setOrders(prev => [newOrder, ...prev]);
    clearCart();
    setAppliedCoupon(null);
    return newOrder;
  };

  const updateOrderStatus = (orderId, newStatus) => {
    setOrders(prev => prev.map(order => {
      if (order.id === orderId || order.orderNumber === orderId) {
        const timeline = [...order.timeline];
        const statusMap = {
          'Pending': 0,
          'Confirmed': 1,
          'Processing': 2,
          'Shipped': 3,
          'Delivered': 5,
          'Cancelled': -1
        };

        const targetIndex = statusMap[newStatus] ?? 1;
        if (targetIndex >= 0) {
          timeline.forEach((step, idx) => {
            if (idx <= targetIndex) {
              step.done = true;
              if (step.time === 'Pending') step.time = new Date().toLocaleString();
            } else {
              step.done = false;
            }
          });
        }

        return {
          ...order,
          status: newStatus,
          timeline,
          paymentStatus: newStatus === 'Delivered' ? 'Paid' : order.paymentStatus
        };
      }
      return order;
    }));
    addToast(`Order ${orderId} updated to ${newStatus}`, 'success');
  };

  const getOrderById = (id) => orders.find(o => o.id === id || o.orderNumber === id);

  // 7. Customers State
  const [customers, setCustomers] = useState(() => {
    const saved = localStorage.getItem('aura_customers');
    return saved ? JSON.parse(saved) : initialCustomers;
  });

  useEffect(() => {
    localStorage.setItem('aura_customers', JSON.stringify(customers));
  }, [customers]);

  // 8. Enquiries State (Contact Form <-> Admin)
  const [enquiries, setEnquiries] = useState(() => {
    const saved = localStorage.getItem('aura_enquiries');
    return saved ? JSON.parse(saved) : initialEnquiries;
  });

  useEffect(() => {
    localStorage.setItem('aura_enquiries', JSON.stringify(enquiries));
  }, [enquiries]);

  const submitEnquiry = (enquiryData) => {
    const newEnquiry = {
      id: 'enq-' + (100 + enquiries.length + 1),
      ...enquiryData,
      status: 'New',
      createdAt: new Date().toISOString(),
      adminNotes: ''
    };
    setEnquiries(prev => [newEnquiry, ...prev]);
    addToast('Your enquiry has been received! Ticket #' + newEnquiry.id, 'success');
    return newEnquiry;
  };

  const updateEnquiryStatus = (id, newStatus, notes = null) => {
    setEnquiries(prev => prev.map(enq => {
      if (enq.id === id) {
        return {
          ...enq,
          status: newStatus,
          adminNotes: notes !== null ? notes : enq.adminNotes
        };
      }
      return enq;
    }));
    addToast('Enquiry ticket updated', 'success');
  };

  // 9. Current User & Demo Auth
  const [user, setUser] = useState({
    name: 'Aarav Mehta',
    email: 'aarav.mehta@example.com',
    phone: '+91 98765 12345',
    savedAddresses: [
      {
        id: 'addr-1',
        title: 'Home',
        name: 'Aarav Mehta',
        phone: '+91 98765 12345',
        street: 'Penthouse 12, Skywalk Residency, Koramangala 4th Block',
        city: 'Bengaluru',
        state: 'Karnataka',
        pincode: '560034',
        isDefault: true
      },
      {
        id: 'addr-2',
        title: 'Studio Office',
        name: 'Aarav Mehta',
        phone: '+91 98765 12345',
        street: 'Suite 304, Design Hub, Indiranagar 100ft Road',
        city: 'Bengaluru',
        state: 'Karnataka',
        pincode: '560038',
        isDefault: false
      }
    ]
  });

  const [isAdminLoggedIn, setIsAdminLoggedIn] = useState(false);
  const [isAdminView, setIsAdminView] = useState(false);

  // 10. Toasts Notification
  const [toasts, setToasts] = useState([]);

  const addToast = (message, type = 'success') => {
    const id = Date.now() + Math.random();
    setToasts(prev => [...prev, { id, message, type }]);
    setTimeout(() => {
      setToasts(prev => prev.filter(t => t.id !== id));
    }, 4000);
  };

  const removeToast = (id) => {
    setToasts(prev => prev.filter(t => t.id !== id));
  };

  // 11. Modal Controls
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [quickViewProduct, setQuickViewProduct] = useState(null);
  const [isSizeChartOpen, setIsSizeChartOpen] = useState(false);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [activeInvoiceOrder, setActiveInvoiceOrder] = useState(null);

  const value = {
    // Router
    currentPage,
    pageParams,
    navigateTo,
    // Products
    products,
    addProduct,
    updateProduct,
    deleteProduct,
    getProductBySlug,
    getProductById,
    // Categories
    categories,
    addCategory,
    updateCategory,
    deleteCategory,
    // Cart
    cart,
    cartItemCount,
    addToCart,
    updateCartQuantity,
    removeFromCart,
    clearCart,
    cartSubtotal,
    cartDiscount,
    shippingFee,
    estimatedTax,
    cartGrandTotal,
    freeShippingThreshold,
    isFreeShipping,
    freeShippingRemaining,
    // Coupons
    coupons,
    appliedCoupon,
    applyCouponCode,
    removeCoupon,
    setCoupons,
    // Wishlist
    wishlist,
    toggleWishlist,
    isInWishlist,
    // Orders
    orders,
    createOrder,
    updateOrderStatus,
    getOrderById,
    // Customers
    customers,
    setCustomers,
    // Enquiries
    enquiries,
    submitEnquiry,
    updateEnquiryStatus,
    // User
    user,
    setUser,
    isAdminLoggedIn,
    setIsAdminLoggedIn,
    isAdminView,
    setIsAdminView,
    // Modals
    isCartOpen,
    setIsCartOpen,
    quickViewProduct,
    setQuickViewProduct,
    isSizeChartOpen,
    setIsSizeChartOpen,
    isAuthModalOpen,
    setIsAuthModalOpen,
    isSearchOpen,
    setIsSearchOpen,
    searchQuery,
    setSearchQuery,
    activeInvoiceOrder,
    setActiveInvoiceOrder,
    // Toasts
    toasts,
    addToast,
    removeToast
  };

  return (
    <StoreContext.Provider value={value}>
      {children}
    </StoreContext.Provider>
  );
};
