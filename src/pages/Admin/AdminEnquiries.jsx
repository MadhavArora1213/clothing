import React, { useState } from 'react';
import { 
  MessageSquare, 
  Search, 
  Eye, 
  Clock, 
  CheckCircle2, 
  AlertCircle, 
  X, 
  Send 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminEnquiries = () => {
  const { enquiries, updateEnquiryStatus } = useStore();

  const [searchTerm, setSearchTerm] = useState('');
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [activeTicket, setActiveTicket] = useState(null);
  const [internalNote, setInternalNote] = useState('');

  const filtered = enquiries.filter(e => {
    const matchSearch = 
      e.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
      e.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
      e.subject.toLowerCase().includes(searchTerm.toLowerCase());
    const matchStatus = selectedStatus === 'all' || e.status === selectedStatus;
    return matchSearch && matchStatus;
  });

  const handleOpenTicket = (ticket) => {
    setActiveTicket(ticket);
    setInternalNote(ticket.adminNotes || '');
  };

  const handleSaveNotes = () => {
    if (activeTicket) {
      updateEnquiryStatus(activeTicket.id, activeTicket.status, internalNote);
      setActiveTicket({ ...activeTicket, adminNotes: internalNote });
    }
  };

  const handleStatusChange = (newStatus) => {
    if (activeTicket) {
      updateEnquiryStatus(activeTicket.id, newStatus, internalNote);
      setActiveTicket({ ...activeTicket, status: newStatus });
    }
  };

  return (
    <div className="space-y-6 animate-fade-in">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Client Enquiries &amp; Support Inbox
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Manage inquiries submitted from the website Contact Us page
          </p>
        </div>

        <div className="flex gap-2">
          <span className="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl">
            {enquiries.filter(e => e.status === 'New').length} New Inquiries
          </span>
        </div>
      </div>

      {/* Search & Status Filters */}
      <div className="bg-white rounded-3xl p-4 border border-stone-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div className="relative flex-1 w-full sm:w-auto">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search enquiries by sender name, email or keyword..."
            className="w-full bg-stone-50 border border-stone-200 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500"
          />
        </div>

        <div className="flex gap-1.5 w-full sm:w-auto">
          {['all', 'New', 'In Progress', 'Resolved'].map(st => (
            <button
              key={st}
              onClick={() => setSelectedStatus(st)}
              className={`px-3 py-1.5 rounded-xl text-xs font-bold capitalize transition-all ${
                selectedStatus === st ? 'bg-[#0F172A] text-white shadow-sm' : 'bg-stone-100 text-stone-600'
              }`}
            >
              {st}
            </button>
          ))}
        </div>
      </div>

      {/* Enquiries Table */}
      <div className="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-b border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3.5 px-4">Ticket</th>
                <th className="py-3.5 px-4">Sender</th>
                <th className="py-3.5 px-4">Category &amp; Subject</th>
                <th className="py-3.5 px-4">Order ID</th>
                <th className="py-3.5 px-4">Status</th>
                <th className="py-3.5 px-4">Submitted At</th>
                <th className="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {filtered.map((enq) => (
                <tr key={enq.id} className="hover:bg-stone-50/80 transition-colors">
                  <td className="py-3.5 px-4 font-mono font-bold text-stone-900">#{enq.id}</td>
                  
                  <td className="py-3.5 px-4">
                    <p className="font-bold text-stone-900">{enq.name}</p>
                    <p className="text-[10px] text-stone-400">{enq.email}</p>
                  </td>

                  <td className="py-3.5 px-4 max-w-xs">
                    <span className="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded mr-1">
                      {enq.category}
                    </span>
                    <p className="font-semibold text-stone-900 truncate mt-1">{enq.subject}</p>
                  </td>

                  <td className="py-3.5 px-4 font-mono text-stone-500">
                    {enq.orderNumber || '-'}
                  </td>

                  <td className="py-3.5 px-4">
                    <select
                      value={enq.status}
                      onChange={(e) => updateEnquiryStatus(enq.id, e.target.value)}
                      className={`text-xs font-bold px-2.5 py-1 rounded-xl border focus:outline-none cursor-pointer ${
                        enq.status === 'New'
                          ? 'bg-emerald-50 border-emerald-300 text-emerald-800'
                          : enq.status === 'In Progress'
                            ? 'bg-sky-50 border-sky-300 text-sky-800'
                            : 'bg-stone-100 border-stone-300 text-stone-600'
                      }`}
                    >
                      <option value="New">New</option>
                      <option value="In Progress">In Progress</option>
                      <option value="Resolved">Resolved</option>
                    </select>
                  </td>

                  <td className="py-3.5 px-4 text-stone-400 font-mono text-[11px]">
                    {new Date(enq.createdAt).toLocaleDateString()}
                  </td>

                  <td className="py-3.5 px-4 text-right">
                    <button
                      onClick={() => handleOpenTicket(enq)}
                      className="p-1.5 text-stone-600 hover:text-stone-900 rounded-lg hover:bg-stone-100"
                      title="Inspect message"
                    >
                      <Eye className="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Ticket Details & Response Modal */}
      {activeTicket && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setActiveTicket(null)}></div>
          <div className="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl z-10 animate-scale-in space-y-4">
            
            <div className="flex items-center justify-between pb-4 border-b border-stone-100">
              <div>
                <span className="text-[10px] font-bold text-orange-600 uppercase">Support Ticket</span>
                <h3 className="font-serif text-lg font-bold text-stone-900">#{activeTicket.id}</h3>
              </div>
              <button onClick={() => setActiveTicket(null)}><X className="w-5 h-5 text-stone-400" /></button>
            </div>

            <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200/80 text-xs space-y-1">
              <p><strong>From:</strong> {activeTicket.name} ({activeTicket.email})</p>
              {activeTicket.phone && <p><strong>Phone:</strong> {activeTicket.phone}</p>}
              {activeTicket.orderNumber && <p><strong>Associated Order ID:</strong> {activeTicket.orderNumber}</p>}
              <p><strong>Category:</strong> {activeTicket.category}</p>
            </div>

            <div>
              <h4 className="font-bold text-xs text-stone-900 mb-1">Customer Message:</h4>
              <div className="bg-stone-50 p-4 rounded-2xl border border-stone-200 text-xs text-stone-700 leading-relaxed italic">
                "{activeTicket.message}"
              </div>
            </div>

            <div>
              <label className="block font-bold text-xs text-stone-900 mb-1">Internal Concierge Notes</label>
              <textarea
                rows={2}
                value={internalNote}
                onChange={(e) => setInternalNote(e.target.value)}
                placeholder="Log internal action taken (e.g. Reverse pickup booked, WhatsApp response sent)..."
                className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl text-xs focus:outline-none focus:border-orange-500"
              />
            </div>

            <div className="flex items-center justify-between pt-2 border-t border-stone-100">
              <div className="flex gap-1.5">
                <button
                  type="button"
                  onClick={() => handleStatusChange('In Progress')}
                  className="px-3 py-1.5 rounded-xl bg-sky-50 text-sky-700 text-xs font-bold border border-sky-200"
                >
                  Mark In Progress
                </button>
                <button
                  type="button"
                  onClick={() => handleStatusChange('Resolved')}
                  className="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200"
                >
                  Mark Resolved
                </button>
              </div>

              <button
                type="button"
                onClick={handleSaveNotes}
                className="bg-[#0F172A] text-white text-xs font-bold px-4 py-2 rounded-xl"
              >
                Save Notes
              </button>
            </div>

          </div>
        </div>
      )}

    </div>
  );
};
