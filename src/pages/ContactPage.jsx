import React, { useState } from 'react';
import { 
  Mail, 
  Phone, 
  MapPin, 
  Clock, 
  Send, 
  CheckCircle2, 
  MessageSquare, 
  Sparkles, 
  HelpCircle 
} from 'lucide-react';
import { useStore } from '../context/StoreContext';

export const ContactPage = () => {
  const { submitEnquiry, navigateTo } = useStore();

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [orderNumber, setOrderNumber] = useState('');
  const [category, setCategory] = useState('Exchange & Return');
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [submittedTicket, setSubmittedTicket] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!name || !email || !message) return;

    const enquiry = submitEnquiry({
      name,
      email,
      phone,
      orderNumber,
      category,
      subject: subject || `${category} Query from ${name}`,
      message
    });

    setSubmittedTicket(enquiry);
    setName('');
    setEmail('');
    setPhone('');
    setOrderNumber('');
    setSubject('');
    setMessage('');
  };

  return (
    <div className="py-12 sm:py-20 bg-[#FAFAF9] min-h-screen animate-fade-in">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto mb-14 space-y-2">
          <div className="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 uppercase tracking-widest bg-orange-50 px-3 py-1 rounded-full">
            <MessageSquare className="w-3.5 h-3.5" />
            <span>24/7 Concierge &amp; Customer Care</span>
          </div>
          <h1 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-black text-[#0F172A]">
            Get In Touch With AURA
          </h1>
          <p className="text-xs sm:text-sm text-stone-500">
            Have a question regarding sizes, exchanges, wedding bulk orders, or your current delivery? Our team responds within 2 hours.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
          
          {/* Left: Contact Info & Support Channels */}
          <div className="lg:col-span-5 space-y-6">
            <div className="bg-[#0F172A] text-white rounded-3xl p-8 shadow-xl space-y-6">
              <h3 className="font-serif text-2xl font-bold">Client Concierge</h3>
              <p className="text-xs text-stone-300 leading-relaxed">
                We take immense pride in our personalized shopping experience. Reach out anytime via WhatsApp, phone, or visit our flagship design studio.
              </p>

              <div className="space-y-4 pt-4 border-t border-stone-800 text-xs">
                <div className="flex items-start gap-3">
                  <div className="w-8 h-8 rounded-xl bg-stone-800 flex items-center justify-center text-orange-400 shrink-0">
                    <Phone className="w-4 h-4" />
                  </div>
                  <div>
                    <h5 className="font-bold text-stone-100">WhatsApp &amp; Voice Call</h5>
                    <p className="text-stone-400 mt-0.5">+91 (800) 420-AURA &bull; +91 98765 43210</p>
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-8 h-8 rounded-xl bg-stone-800 flex items-center justify-center text-orange-400 shrink-0">
                    <Mail className="w-4 h-4" />
                  </div>
                  <div>
                    <h5 className="font-bold text-stone-100">Email Support</h5>
                    <p className="text-stone-400 mt-0.5">concierge@auraco.com &bull; orders@auraco.com</p>
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-8 h-8 rounded-xl bg-stone-800 flex items-center justify-center text-orange-400 shrink-0">
                    <MapPin className="w-4 h-4" />
                  </div>
                  <div>
                    <h5 className="font-bold text-stone-100">Design Studio Flagship</h5>
                    <p className="text-stone-400 mt-0.5">Plot 42, 100ft Road, Indiranagar, Bengaluru, KA 560038</p>
                  </div>
                </div>

                <div className="flex items-start gap-3">
                  <div className="w-8 h-8 rounded-xl bg-stone-800 flex items-center justify-center text-orange-400 shrink-0">
                    <Clock className="w-4 h-4" />
                  </div>
                  <div>
                    <h5 className="font-bold text-stone-100">Operating Hours</h5>
                    <p className="text-stone-400 mt-0.5">Mon - Sat: 9:00 AM to 8:00 PM IST</p>
                  </div>
                </div>
              </div>

              {/* Admin note hint */}
              <div className="bg-stone-900 p-4 rounded-2xl border border-stone-800 text-[11px] text-stone-400">
                <span className="text-orange-400 font-bold">Pro Tip for Demo Review:</span> Messages sent via this form automatically appear in the <strong>Admin Panel &rarr; Enquiries</strong> inbox in real time!
              </div>
            </div>
          </div>

          {/* Right: Contact Form */}
          <div className="lg:col-span-7">
            <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-xl space-y-6">
              
              {submittedTicket ? (
                <div className="text-center py-10 space-y-4 animate-scale-in">
                  <div className="w-16 h-16 rounded-full bg-emerald-50 border-4 border-emerald-100 flex items-center justify-center mx-auto text-emerald-600">
                    <CheckCircle2 className="w-8 h-8" />
                  </div>
                  <h3 className="font-serif text-2xl font-bold text-stone-900">Enquiry Submitted!</h3>
                  <p className="text-xs text-stone-600 max-w-sm mx-auto">
                    Your inquiry ticket <strong className="font-mono text-stone-900">#{submittedTicket.id}</strong> has been created. Our team has received your message and will respond shortly.
                  </p>
                  <div className="pt-2 flex justify-center gap-3">
                    <button
                      onClick={() => setSubmittedTicket(null)}
                      className="bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold px-6 py-2.5 rounded-xl"
                    >
                      Send Another Message
                    </button>
                    <button
                      onClick={() => navigateTo('admin')}
                      className="bg-orange-50 text-orange-700 font-bold text-xs px-6 py-2.5 rounded-xl border border-orange-200"
                    >
                      View in Admin Panel &rarr;
                    </button>
                  </div>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-4">
                  <h3 className="font-serif text-xl font-bold text-stone-900">Send an Enquiry Message</h3>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Your Full Name *</label>
                      <input
                        type="text"
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        placeholder="e.g. Pooja Hegde"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                        required
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Email Address *</label>
                      <input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="pooja@example.com"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                        required
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Mobile Phone (Optional)</label>
                      <input
                        type="tel"
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        placeholder="+91 98765 00000"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white font-mono"
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Order ID (If applicable)</label>
                      <input
                        type="text"
                        value={orderNumber}
                        onChange={(e) => setOrderNumber(e.target.value)}
                        placeholder="e.g. ATL-9842"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white font-mono"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Inquiry Category *</label>
                      <select
                        value={category}
                        onChange={(e) => setCategory(e.target.value)}
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white font-medium cursor-pointer"
                      >
                        <option value="Exchange & Return">Size Exchange &amp; Returns</option>
                        <option value="Order Tracking">Order Tracking &amp; Delivery</option>
                        <option value="Custom & Bulk Orders">Arya Custom / Wedding Bulk Orders</option>
                        <option value="Fabric & Sizing">Fabric Details &amp; Fit Query</option>
                        <option value="General Inquiry">General Feedback / Inquiry</option>
                      </select>
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-stone-700 mb-1">Subject</label>
                      <input
                        type="text"
                        value={subject}
                        onChange={(e) => setSubject(e.target.value)}
                        placeholder="Brief summary of issue"
                        className="w-full bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-stone-700 mb-1">Your Detailed Message *</label>
                    <textarea
                      rows={4}
                      value={message}
                      onChange={(e) => setMessage(e.target.value)}
                      placeholder="Please provide specifics so our concierge can assist you accurately..."
                      className="w-full bg-stone-50 border border-stone-200 text-xs p-3.5 rounded-xl focus:outline-none focus:border-orange-500 focus:bg-white"
                      required
                    />
                  </div>

                  <button
                    type="submit"
                    className="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3.5 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
                  >
                    <Send className="w-4 h-4" />
                    <span>Submit Enquiry Ticket</span>
                  </button>
                </form>
              )}

            </div>
          </div>

        </div>

      </div>
    </div>
  );
};
