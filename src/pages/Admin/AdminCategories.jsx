import React, { useState } from 'react';
import { Plus, Edit3, Trash2, X, FolderTree, Check } from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminCategories = () => {
  const { categories, addCategory, updateCategory, deleteCategory } = useStore();

  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState(null);

  const [name, setName] = useState('');
  const [slug, setSlug] = useState('');
  const [description, setDescription] = useState('');
  const [image, setImage] = useState('');
  const [subcats, setSubcats] = useState('');

  const handleOpenAdd = () => {
    setEditingId(null);
    setName('');
    setSlug('');
    setDescription('');
    setImage('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&auto=format&fit=crop&q=80');
    setSubcats('Luxe Fits, Graphic Tees, Casual Sets');
    setModalOpen(true);
  };

  const handleOpenEdit = (cat) => {
    setEditingId(cat.id);
    setName(cat.name);
    setSlug(cat.slug);
    setDescription(cat.description || '');
    setImage(cat.image);
    setSubcats(cat.subcategories?.map(s => s.name).join(', ') || '');
    setModalOpen(true);
  };

  const handleSave = (e) => {
    e.preventDefault();
    const subcategoryList = subcats.split(',').map(s => s.trim()).filter(Boolean).map(s => ({
      id: s.toLowerCase().replace(/\s+/g, '-'),
      name: s,
      slug: s.toLowerCase().replace(/\s+/g, '-')
    }));

    const catData = {
      name,
      slug: slug || name.toLowerCase().replace(/\s+/g, '-'),
      description,
      image,
      subcategories: subcategoryList
    };

    if (editingId) {
      updateCategory(editingId, catData);
    } else {
      addCategory(catData);
    }

    setModalOpen(false);
  };

  return (
    <div className="space-y-6 animate-fade-in">
      
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Departments &amp; Categories
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Organize collections, drop lines, and navigation mega-menus
          </p>
        </div>

        <button
          onClick={handleOpenAdd}
          className="bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
        >
          <Plus className="w-4 h-4" />
          <span>Add New Category</span>
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {categories.map((cat) => (
          <div
            key={cat.id}
            className="bg-white rounded-3xl overflow-hidden border border-stone-200 shadow-sm flex flex-col justify-between"
          >
            <div>
              <div className="h-40 relative bg-stone-100">
                <img src={cat.image} alt={cat.name} className="w-full h-full object-cover" />
                <div className="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-[10px] font-bold text-stone-800 shadow">
                  Active
                </div>
              </div>

              <div className="p-5 space-y-3">
                <div className="flex items-center justify-between">
                  <h3 className="font-serif text-lg font-bold text-stone-900">{cat.name}</h3>
                  <span className="font-mono text-[11px] text-stone-400">/{cat.slug}</span>
                </div>

                <p className="text-xs text-stone-600 line-clamp-2 leading-relaxed">
                  {cat.description}
                </p>

                {cat.subcategories?.length > 0 && (
                  <div className="pt-2">
                    <span className="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1.5">
                      Sub-Collections ({cat.subcategories.length})
                    </span>
                    <div className="flex flex-wrap gap-1.5">
                      {cat.subcategories.map((sub) => (
                        <span key={sub.id} className="text-[10px] font-semibold bg-stone-100 text-stone-700 px-2 py-0.5 rounded-md">
                          {sub.name}
                        </span>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            </div>

            <div className="p-5 pt-0 flex items-center justify-end gap-2 border-t border-stone-100 mt-2">
              <button
                onClick={() => handleOpenEdit(cat)}
                className="p-2 text-stone-600 hover:text-stone-900 rounded-xl hover:bg-stone-100 text-xs font-semibold flex items-center gap-1"
              >
                <Edit3 className="w-3.5 h-3.5" />
                <span>Edit</span>
              </button>
              <button
                onClick={() => deleteCategory(cat.id)}
                className="p-2 text-stone-400 hover:text-rose-600 rounded-xl hover:bg-stone-100 text-xs font-semibold flex items-center gap-1"
              >
                <Trash2 className="w-3.5 h-3.5" />
                <span>Delete</span>
              </button>
            </div>
          </div>
        ))}
      </div>

      {modalOpen && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setModalOpen(false)}></div>
          <div className="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl z-10 animate-scale-in space-y-4">
            <h3 className="font-serif text-lg font-bold text-stone-900">
              {editingId ? 'Edit Category' : 'Add New Category'}
            </h3>

            <form onSubmit={handleSave} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold text-stone-700 mb-1">Category Name</label>
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="e.g. Resort Wear"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-semibold"
                  required
                />
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Slug (URL)</label>
                <input
                  type="text"
                  value={slug}
                  onChange={(e) => setSlug(e.target.value)}
                  placeholder="e.g. resort-wear"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono"
                />
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Banner Image URL</label>
                <input
                  type="url"
                  value={image}
                  onChange={(e) => setImage(e.target.value)}
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono text-[11px]"
                />
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Description</label>
                <textarea
                  rows={2}
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                />
              </div>

              <div>
                <label className="block font-bold text-stone-700 mb-1">Subcategories (Comma-separated)</label>
                <input
                  type="text"
                  value={subcats}
                  onChange={(e) => setSubcats(e.target.value)}
                  placeholder="e.g. Linen Sets, Short Kurtas, Cargo Pants"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                />
              </div>

              <div className="flex gap-2 pt-3 border-t border-stone-100">
                <button type="submit" className="flex-1 bg-orange-600 text-white font-bold py-3 rounded-xl">
                  Save Category
                </button>
                <button type="button" onClick={() => setModalOpen(false)} className="px-4 py-3 border rounded-xl font-semibold">
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
