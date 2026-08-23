import React, { useState } from 'react';
import { X, Lock, Mail, User as UserIcon, ArrowRight, ShieldCheck, Sparkles } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AuthModal = () => {
  const { isAuthModalOpen, setIsAuthModalOpen, user, setUser, addToast, navigateTo, setIsAdminLoggedIn } = useStore();
  const [activeTab, setActiveTab] = useState('login'); // 'login' | 'register'
  
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [name, setName] = useState('');

  if (!isAuthModalOpen) return null;

  const handleSubmit = (e) => {
    e.preventDefault();
    if (activeTab === 'login') {
      if (email === 'admin@auraco.com') {
        setIsAdminLoggedIn(true);
        addToast('Welcome Admin! Switched to management mode.', 'success');
        setIsAuthModalOpen(false);
        navigateTo('admin');
        return;
      }
      
      setUser({
        name: email.split('@')[0].toUpperCase(),
        email: email,
        phone: '+91 98765 00000',
        savedAddresses: user.savedAddresses
      });
      addToast(`Welcome back, ${email.split('@')[0]}!`, 'success');
    } else {
      setUser({
        name: name || 'New Customer',
        email: email,
        phone: '+91 98765 00000',
        savedAddresses: []
      });
      addToast('Account created successfully! Welcome to AURA & CO.', 'success');
    }
    setIsAuthModalOpen(false);
  };

  const handleDemoFill = (type) => {
    if (type === 'customer') {
      setEmail('aarav.mehta@example.com');
      setPassword('password123');
      setName('Aarav Mehta');
    } else {
      setEmail('admin@auraco.com');
      setPassword('admin123');
      setName('Admin Director');
    }
  };

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-center justify-center">
      {/* Backdrop */}
      <div 
        className="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
        onClick={() => setIsAuthModalOpen(false)}
      ></div>

      <div className="relative w-full max-w-md bg-white rounded-3xl shadow-2xl border border-stone-100 overflow-hidden animate-scale-in p-6 sm:p-8">
        
        {/* Close Button */}
        <button 
          onClick={() => setIsAuthModalOpen(false)}
          className="absolute top-5 right-5 p-1.5 rounded-full text-stone-400 hover:text-stone-900 hover:bg-stone-100 transition-colors"
        >
          <X className="w-5 h-5" />
        </button>

        {/* Modal Header */}
        <div className="text-center mb-6">
          <div className="flex items-baseline justify-center gap-1 mb-2">
            <span className="font-serif text-2xl font-black tracking-widest text-[#0F172A]">AURA</span>
            <span className="text-xs font-bold text-orange-600 tracking-wider">&amp; CO.</span>
          </div>
          <h3 className="font-serif text-xl font-bold text-stone-900">
            {activeTab === 'login' ? 'Welcome Back' : 'Create Your Account'}
          </h3>
          <p className="text-xs text-stone-500 mt-1">
            {activeTab === 'login' 
              ? 'Access saved addresses, track shipments, and view order history'
              : 'Join the AURA VIP club and unlock member-exclusive drops'}
          </p>
        </div>

        {/* Tabs */}
        <div className="flex bg-stone-100 p-1 rounded-2xl mb-6">
          <button
            type="button"
            onClick={() => setActiveTab('login')}
            className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
              activeTab === 'login' 
                ? 'bg-white text-stone-900 shadow-sm' 
                : 'text-stone-500 hover:text-stone-900'
            }`}
          >
            Sign In
          </button>
          <button
            type="button"
            onClick={() => setActiveTab('register')}
            className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
              activeTab === 'register' 
                ? 'bg-white text-stone-900 shadow-sm' 
                : 'text-stone-500 hover:text-stone-900'
            }`}
          >
            Create Account
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="space-y-4">
          {activeTab === 'register' && (
            <div>
              <label className="block text-xs font-bold text-stone-700 mb-1">Full Name</label>
              <div className="relative">
                <UserIcon className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="e.g. Priya Sharma"
                  className="w-full bg-stone-50 border border-stone-200 text-stone-900 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white transition-all"
                  required
                />
              </div>
            </div>
          )}

          <div>
            <label className="block text-xs font-bold text-stone-700 mb-1">Email Address</label>
            <div className="relative">
              <Mail className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="name@example.com"
                className="w-full bg-stone-50 border border-stone-200 text-stone-900 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white transition-all"
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-bold text-stone-700 mb-1">Password</label>
            <div className="relative">
              <Lock className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                className="w-full bg-stone-50 border border-stone-200 text-stone-900 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white transition-all"
                required
              />
            </div>
          </div>

          <button
            type="submit"
            className="w-full bg-[#0F172A] hover:bg-stone-800 text-white text-xs font-bold py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 mt-2"
          >
            <span>{activeTab === 'login' ? 'Sign In to Account' : 'Complete Registration'}</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </form>

        {/* 1-Click Demo Fillers */}
        <div className="mt-6 pt-5 border-t border-stone-100">
          <p className="text-[11px] font-bold text-stone-400 uppercase tracking-wider text-center mb-2">
            Instant 1-Click Demo Logins
          </p>
          <div className="grid grid-cols-2 gap-2">
            <button
              type="button"
              onClick={() => handleDemoFill('customer')}
              className="px-3 py-2 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-700 text-xs font-semibold border border-stone-200 transition-colors text-center"
            >
              Demo Customer
            </button>
            <button
              type="button"
              onClick={() => handleDemoFill('admin')}
              className="px-3 py-2 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-semibold border border-orange-200 transition-colors text-center"
            >
              Demo Admin (Portal)
            </button>
          </div>
        </div>

      </div>
    </div>
  );
};
