import React, { useState } from 'react';
import { 
  User, 
  Package, 
  MapPin, 
  LogOut, 
  Plus, 
  Trash2, 
  Edit3, 
  Printer, 
  Truck, 
  ShieldCheck,
  CheckCircle2
} from 'lucide-react';
import { useStore } from '../context/StoreContext';

export const AccountPage = () => {
  const { 
    user, 
    setUser, 
    orders, 
    setActiveInvoiceOrder, 
    navigateTo, 
    setIsAuthModalOpen, 
    addToast 
  } = useStore();

  const [activeTab, setActiveTab] = useState('orders'); // 'orders' | 'addresses' | 'profile'

  // Address Modal
  const [showAddressModal, setShowAddressModal] = useState(false);
  const [newTitle, setNewTitle] = useState('Home');
  const [newStreet, setNewStreet] = useState('');
  const [newCity, setNewCity] = useState('');
  const [newState, setNewState] = useState('');
  const [newPincode, setNewPincode] = useState('');

  const userOrders = orders; // All active demo orders for user

  const handleAddAddress = (e) => {
    e.preventDefault();
    if (!newStreet || !newCity || !newPincode) return;

    const newAddr = {
      id: 'addr-' + Date.now(),
      title: newTitle,
      name: user.name,
      phone: user.phone,
      street: newStreet,
      city: newCity,
      state: newState || 'Karnataka',
      pincode: newPincode,
      isDefault: user.savedAddresses?.length === 0
    };

    setUser({
      ...user,
      savedAddresses: [...(user.savedAddresses || []), newAddr]
    });

    setShowAddressModal(false);
    setNewStreet('');
    setNewCity('');
    setNewPincode('');
    addToast('New address saved to your profile', 'success');
  };

  const handleDeleteAddress = (id) => {
    setUser({
      ...user,
      savedAddresses: user.savedAddresses.filter(a => a.id !== id)
    });
    addToast('Address deleted', 'info');
  };

  return (
    <div className="py-12 sm:py-16 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Profile Header Card */}
        <div className="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-sm mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 rounded-full bg-[#0F172A] text-white flex items-center justify-center font-serif text-2xl font-bold">
              {user.name ? user.name.charAt(0).toUpperCase() : 'A'}
            </div>
            <div>
              <div className="flex items-center gap-2">
                <h1 className="font-serif text-xl sm:text-2xl font-bold text-stone-900">{user.name}</h1>
                <span className="text-[10px] font-bold uppercase tracking-wider bg-orange-100 text-orange-800 px-2 py-0.5 rounded-full">
                  VIP Club Member
                </span>
              </div>
              <p className="text-xs text-stone-500 mt-0.5">{user.email} &bull; {user.phone}</p>
            </div>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={() => setIsAuthModalOpen(true)}
              className="text-xs font-bold text-stone-700 bg-stone-100 hover:bg-stone-200 px-4 py-2.5 rounded-xl transition-colors"
            >
              Switch Account
            </button>
            <button
              onClick={() => navigateTo('admin')}
              className="text-xs font-bold text-white bg-orange-600 hover:bg-orange-500 px-4 py-2.5 rounded-xl transition-colors shadow-sm"
            >
              Admin Portal
            </button>
          </div>
        </div>

        {/* Account Tabs */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          {/* Tabs Menu Sidebar */}
          <aside className="lg:col-span-3 space-y-2">
            <button
              onClick={() => setActiveTab('orders')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all ${
                activeTab === 'orders'
                  ? 'bg-[#0F172A] text-white shadow-md'
                  : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200/80'
              }`}
            >
              <Package className="w-4 h-4" />
              <span>Order History ({userOrders.length})</span>
            </button>

            <button
              onClick={() => setActiveTab('addresses')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all ${
                activeTab === 'addresses'
                  ? 'bg-[#0F172A] text-white shadow-md'
                  : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200/80'
              }`}
            >
              <MapPin className="w-4 h-4" />
              <span>Saved Shipping Addresses</span>
            </button>

            <button
              onClick={() => setActiveTab('profile')}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all ${
                activeTab === 'profile'
                  ? 'bg-[#0F172A] text-white shadow-md'
                  : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200/80'
              }`}
            >
              <User className="w-4 h-4" />
              <span>Personal Details</span>
            </button>
          </aside>

          {/* Tab Content Area */}
          <div className="lg:col-span-9 space-y-6">
            
            {/* Orders Tab */}
            {activeTab === 'orders' && (
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="font-serif text-xl font-bold text-stone-900">Your Orders</h3>
                  <span className="text-xs text-stone-500">{userOrders.length} placed</span>
                </div>

                {userOrders.map((order) => (
                  <div 
                    key={order.id}
                    className="bg-white rounded-3xl p-6 border border-stone-200/80 shadow-sm space-y-4"
                  >
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-stone-100 text-xs">
                      <div>
                        <span className="text-stone-500">Order Ref:</span>{' '}
                        <strong className="font-mono text-stone-900 font-bold">{order.orderNumber}</strong>
                        <span className="text-stone-400 ml-2">&bull; {new Date(order.createdAt).toLocaleDateString()}</span>
                      </div>

                      <div className="flex items-center gap-2">
                        <span className={`text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider ${
                          order.status === 'Delivered' 
                            ? 'bg-emerald-100 text-emerald-800'
                            : order.status === 'Shipped'
                              ? 'bg-sky-100 text-sky-800'
                              : 'bg-amber-100 text-amber-800'
                        }`}>
                          {order.status}
                        </span>
                        <span className="font-mono font-bold text-stone-900 text-sm">₹{order.total?.toLocaleString()}</span>
                      </div>
                    </div>

                    {/* Items */}
                    <div className="space-y-3">
                      {order.items?.map((item, idx) => (
                        <div key={idx} className="flex items-center justify-between gap-4">
                          <div className="flex items-center gap-3">
                            <img src={item.image} alt={item.name} className="w-12 h-14 object-cover rounded-xl shrink-0" />
                            <div>
                              <h4 className="text-xs font-bold text-stone-900 line-clamp-1">{item.name}</h4>
                              <p className="text-[11px] text-stone-500 mt-0.5">Size: {item.size} &bull; Qty: {item.quantity}</p>
                            </div>
                          </div>
                          <span className="text-xs font-bold font-mono text-stone-900">
                            ₹{(item.price * item.quantity).toLocaleString()}
                          </span>
                        </div>
                      ))}
                    </div>

                    {/* Actions */}
                    <div className="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-stone-100 text-xs">
                      <p className="text-stone-500">
                        AWB: <strong className="font-mono text-stone-800">{order.trackingNumber}</strong>
                      </p>

                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => setActiveInvoiceOrder(order)}
                          className="px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold transition-colors flex items-center gap-1.5"
                        >
                          <Printer className="w-3.5 h-3.5" />
                          <span>Invoice</span>
                        </button>

                        <button
                          onClick={() => navigateTo('track-order', { orderId: order.orderNumber })}
                          className="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold transition-colors flex items-center gap-1.5 shadow"
                        >
                          <Truck className="w-3.5 h-3.5" />
                          <span>Track Delivery</span>
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {/* Saved Addresses Tab */}
            {activeTab === 'addresses' && (
              <div className="space-y-6">
                <div className="flex items-center justify-between">
                  <h3 className="font-serif text-xl font-bold text-stone-900">Saved Addresses</h3>
                  <button
                    onClick={() => setShowAddressModal(true)}
                    className="bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-colors flex items-center gap-1.5"
                  >
                    <Plus className="w-3.5 h-3.5" />
                    <span>Add New Address</span>
                  </button>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  {user.savedAddresses?.map((addr) => (
                    <div
                      key={addr.id}
                      className="bg-white rounded-3xl p-6 border border-stone-200/80 shadow-sm flex flex-col justify-between space-y-4"
                    >
                      <div className="space-y-1 text-xs">
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-sm text-stone-900">{addr.title}</span>
                          {addr.isDefault && (
                            <span className="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                              Default Address
                            </span>
                          )}
                        </div>
                        <p className="font-semibold text-stone-800 pt-1">{addr.name}</p>
                        <p className="text-stone-600">{addr.street}</p>
                        <p className="text-stone-600">{addr.city}, {addr.state} - {addr.pincode}</p>
                        <p className="text-stone-400 pt-1">Phone: {addr.phone}</p>
                      </div>

                      <div className="flex items-center justify-end pt-3 border-t border-stone-100">
                        <button
                          onClick={() => handleDeleteAddress(addr.id)}
                          className="text-stone-400 hover:text-rose-600 text-xs font-semibold flex items-center gap-1 p-1"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                          <span>Delete</span>
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Profile Tab */}
            {activeTab === 'profile' && (
              <div className="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-sm space-y-6">
                <h3 className="font-serif text-xl font-bold text-stone-900">Personal Information</h3>
                
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                  <div>
                    <label className="block text-stone-500 font-bold mb-1">Full Name</label>
                    <input
                      type="text"
                      value={user.name}
                      onChange={(e) => setUser({ ...user, name: e.target.value })}
                      className="w-full bg-stone-50 border border-stone-200 p-3 rounded-xl font-semibold"
                    />
                  </div>

                  <div>
                    <label className="block text-stone-500 font-bold mb-1">Email Address</label>
                    <input
                      type="email"
                      value={user.email}
                      onChange={(e) => setUser({ ...user, email: e.target.value })}
                      className="w-full bg-stone-50 border border-stone-200 p-3 rounded-xl font-semibold"
                    />
                  </div>

                  <div>
                    <label className="block text-stone-500 font-bold mb-1">Phone Number</label>
                    <input
                      type="tel"
                      value={user.phone}
                      onChange={(e) => setUser({ ...user, phone: e.target.value })}
                      className="w-full bg-stone-50 border border-stone-200 p-3 rounded-xl font-semibold"
                    />
                  </div>
                </div>

                <div className="pt-2">
                  <button
                    onClick={() => addToast('Profile updated successfully!', 'success')}
                    className="bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-6 py-3 rounded-xl transition-colors shadow"
                  >
                    Save Changes
                  </button>
                </div>
              </div>
            )}

          </div>

        </div>

      </div>

      {/* Add Address Modal */}
      {showAddressModal && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center">
          <div className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" onClick={() => setShowAddressModal(false)}></div>
          <div className="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl z-10 animate-scale-in space-y-4">
            <h3 className="font-serif text-lg font-bold text-stone-900">Add New Shipping Address</h3>

            <form onSubmit={handleAddAddress} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold text-stone-700 mb-1">Address Label</label>
                <input
                  type="text"
                  value={newTitle}
                  onChange={(e) => setNewTitle(e.target.value)}
                  placeholder="e.g. Home, Office, Studio"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                  required
                />
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Street Address</label>
                <input
                  type="text"
                  value={newStreet}
                  onChange={(e) => setNewStreet(e.target.value)}
                  placeholder="Flat, building, street..."
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                  required
                />
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">City</label>
                  <input
                    type="text"
                    value={newCity}
                    onChange={(e) => setNewCity(e.target.value)}
                    placeholder="City"
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                    required
                  />
                </div>
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Pincode</label>
                  <input
                    type="text"
                    maxLength={6}
                    value={newPincode}
                    onChange={(e) => setNewPincode(e.target.value)}
                    placeholder="6 digits"
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono"
                    required
                  />
                </div>
              </div>

              <div className="flex gap-2 pt-3">
                <button
                  type="submit"
                  className="flex-1 bg-orange-600 text-white font-bold py-2.5 rounded-xl"
                >
                  Save Address
                </button>
                <button
                  type="button"
                  onClick={() => setShowAddressModal(false)}
                  className="px-4 py-2.5 border rounded-xl"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
