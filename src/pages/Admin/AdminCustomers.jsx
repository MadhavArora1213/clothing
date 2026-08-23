import React, { useState } from 'react';
import { Users, Search, Mail, Phone, ShoppingBag, ShieldCheck } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminCustomers = () => {
  const { customers, orders } = useStore();
  const [searchTerm, setSearchTerm] = useState('');

  const filteredCustomers = customers.filter(c => 
    c.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    c.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Customer Directory
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            View registered shoppers, total lifetime value (LTV), and purchase history
          </p>
        </div>

        <span className="text-xs font-bold text-stone-700 bg-white border border-stone-200 px-4 py-2.5 rounded-xl shadow-sm">
          Total Registered: {customers.length}
        </span>
      </div>

      <div className="bg-white rounded-3xl p-4 border border-stone-200 shadow-sm">
        <div className="relative">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search customers by name or email..."
            className="w-full bg-stone-50 border border-stone-200 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500"
          />
        </div>
      </div>

      <div className="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-b border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3.5 px-4">Customer</th>
                <th className="py-3.5 px-4">Contact</th>
                <th className="py-3.5 px-4">Orders Placed</th>
                <th className="py-3.5 px-4">Lifetime Spend</th>
                <th className="py-3.5 px-4">Member Status</th>
                <th className="py-3.5 px-4">Joined Date</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {filteredCustomers.map((cust) => (
                <tr key={cust.id} className="hover:bg-stone-50/80 transition-colors">
                  <td className="py-3.5 px-4">
                    <div className="flex items-center gap-3">
                      <img src={cust.avatar} alt={cust.name} className="w-10 h-10 rounded-full object-cover border border-stone-200" />
                      <div>
                        <p className="font-bold text-stone-900">{cust.name}</p>
                        <p className="text-[10px] text-stone-400">ID: #{cust.id}</p>
                      </div>
                    </div>
                  </td>

                  <td className="py-3.5 px-4">
                    <p className="text-stone-800">{cust.email}</p>
                    <p className="text-[10px] text-stone-400 font-mono">{cust.phone}</p>
                  </td>

                  <td className="py-3.5 px-4">
                    <span className="bg-stone-100 px-2.5 py-1 rounded-full font-bold text-stone-800 font-mono">
                      {cust.ordersCount} orders
                    </span>
                  </td>

                  <td className="py-3.5 px-4 font-mono font-bold text-orange-600 text-sm">
                    ₹{cust.totalSpend?.toLocaleString()}
                  </td>

                  <td className="py-3.5 px-4">
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider ${
                      cust.status === 'VIP' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800'
                    }`}>
                      {cust.status}
                    </span>
                  </td>

                  <td className="py-3.5 px-4 text-stone-500 font-mono text-[11px]">
                    {cust.joinedDate}
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
