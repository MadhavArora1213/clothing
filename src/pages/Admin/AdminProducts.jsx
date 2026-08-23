import React, { useState } from 'react';
import { 
  Plus, 
  Search, 
  Edit3, 
  Trash2, 
  Eye, 
  Sparkles, 
  Package, 
  X, 
  Check, 
  Image as ImageIcon 
} from 'lucide-react';
import { useStore } from '../../context/StoreContext';

export const AdminProducts = () => {
  const { products, categories, addProduct, updateProduct, deleteProduct, navigateTo } = useStore();

  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCat, setSelectedCat] = useState('all');
  
  // Modal State
  const [modalOpen, setModalOpen] = useState(false);
  const [editingProductId, setEditingProductId] = useState(null);

  // Form Fields
  const [name, setName] = useState('');
  const [category, setCategory] = useState('oversized');
  const [subCategory, setSubCategory] = useState('');
  const [gender, setGender] = useState('unisex');
  const [price, setPrice] = useState(1499);
  const [originalPrice, setOriginalPrice] = useState(2999);
  const [sku, setSku] = useState('');
  const [imageUrl, setImageUrl] = useState('');
  const [imageUrl2, setImageUrl2] = useState('');
  const [description, setDescription] = useState('');
  const [fabricDetails, setFabricDetails] = useState('100% Combed Cotton (260 GSM)');
  const [fit, setFit] = useState('Relaxed Drop-Shoulder Oversized Fit');
  const [careInstructions, setCareInstructions] = useState('Machine wash cold, air dry in shade');
  const [isFeatured, setIsFeatured] = useState(true);
  const [isNewArrival, setIsNewArrival] = useState(true);
  const [isBestseller, setIsBestseller] = useState(false);
  
  // Size stocks
  const [sizeS, setSizeS] = useState(10);
  const [sizeM, setSizeM] = useState(15);
  const [sizeL, setSizeL] = useState(12);
  const [sizeXL, setSizeXL] = useState(6);
  const [sizeXXL, setSizeXXL] = useState(4);

  const filteredProducts = products.filter(p => {
    const matchSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase()) || p.sku.toLowerCase().includes(searchTerm.toLowerCase());
    const matchCat = selectedCat === 'all' || p.category === selectedCat;
    return matchSearch && matchCat;
  });

  const handleOpenAddModal = () => {
    setEditingProductId(null);
    setName('');
    setCategory('oversized');
    setSubCategory('graphic-tees');
    setGender('unisex');
    setPrice(1299);
    setOriginalPrice(2499);
    setSku('AUR-NEW-' + Math.floor(100 + Math.random() * 900));
    setImageUrl('https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&auto=format&fit=crop&q=80');
    setImageUrl2('https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&auto=format&fit=crop&q=80');
    setDescription('Handcrafted premium heavyweight drop-shoulder silhouette with signature garment wash.');
    setFabricDetails('100% Organic French Terry Combed Cotton (260 GSM)');
    setFit('Relaxed Oversized Fit');
    setCareInstructions('Cold machine wash inside out');
    setIsFeatured(true);
    setIsNewArrival(true);
    setIsBestseller(false);
    setSizeS(10);
    setSizeM(15);
    setSizeL(12);
    setSizeXL(6);
    setSizeXXL(3);
    setModalOpen(true);
  };

  const handleOpenEditModal = (prod) => {
    setEditingProductId(prod.id);
    setName(prod.name);
    setCategory(prod.category);
    setSubCategory(prod.subCategory || '');
    setGender(prod.gender || 'unisex');
    setPrice(prod.price);
    setOriginalPrice(prod.originalPrice || prod.price);
    setSku(prod.sku);
    setImageUrl(prod.images?.[0] || '');
    setImageUrl2(prod.images?.[1] || '');
    setDescription(prod.description);
    setFabricDetails(prod.fabricDetails || '');
    setFit(prod.fit || '');
    setCareInstructions(prod.careInstructions || '');
    setIsFeatured(prod.isFeatured || false);
    setIsNewArrival(prod.isNewArrival || false);
    setIsBestseller(prod.isBestseller || false);

    const s = prod.sizes?.find(sz => sz.size === 'S')?.stock ?? 5;
    const m = prod.sizes?.find(sz => sz.size === 'M')?.stock ?? 8;
    const l = prod.sizes?.find(sz => sz.size === 'L')?.stock ?? 6;
    const xl = prod.sizes?.find(sz => sz.size === 'XL')?.stock ?? 4;
    const xxl = prod.sizes?.find(sz => sz.size === 'XXL')?.stock ?? 2;
    setSizeS(s);
    setSizeM(m);
    setSizeL(l);
    setSizeXL(xl);
    setSizeXXL(xxxl => xxl);
    setModalOpen(true);
  };

  const handleSaveProduct = (e) => {
    e.preventDefault();
    const sizesArray = [
      { size: 'S', stock: Number(sizeS) },
      { size: 'M', stock: Number(sizeM) },
      { size: 'L', stock: Number(sizeL) },
      { size: 'XL', stock: Number(sizeXL) },
      { size: 'XXL', stock: Number(sizeXXL) }
    ];

    const imagesArray = [imageUrl, imageUrl2].filter(Boolean);

    const productPayload = {
      name,
      category,
      subCategory,
      gender,
      price: Number(price),
      originalPrice: Number(originalPrice),
      sku,
      images: imagesArray.length > 0 ? imagesArray : ['https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&auto=format&fit=crop&q=80'],
      description,
      fabricDetails,
      fit,
      careInstructions,
      isFeatured,
      isNewArrival,
      isBestseller,
      sizes: sizesArray,
      colors: [
        { name: 'Onyx Charcoal', hex: '#222222' },
        { name: 'Sand Khaki', hex: '#C2B280' }
      ]
    };

    if (editingProductId) {
      updateProduct(editingProductId, productPayload);
    } else {
      addProduct(productPayload);
    }

    setModalOpen(false);
  };

  return (
    <div className="space-y-6 animate-fade-in">
      
      {/* Header & New Product CTA */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900">
            Products &amp; Inventory Management
          </h1>
          <p className="text-xs text-stone-500 mt-1">
            Manage your entire catalog, pricing, variants, and stock counts
          </p>
        </div>

        <button
          onClick={handleOpenAddModal}
          className="bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-lg shadow-orange-200 transition-all flex items-center justify-center gap-2"
        >
          <Plus className="w-4 h-4" />
          <span>Publish New Product</span>
        </button>
      </div>

      {/* Filter & Search Bar */}
      <div className="bg-white rounded-3xl p-4 border border-stone-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div className="relative flex-1 w-full sm:w-auto">
          <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search by Product Title or SKU..."
            className="w-full bg-stone-50 border border-stone-200 text-xs pl-10 pr-4 py-2.5 rounded-xl focus:outline-none focus:border-orange-500"
          />
        </div>

        <div className="flex items-center gap-2 w-full sm:w-auto">
          <select
            value={selectedCat}
            onChange={(e) => setSelectedCat(e.target.value)}
            className="w-full sm:w-auto bg-stone-50 border border-stone-200 text-xs px-3.5 py-2.5 rounded-xl font-medium focus:outline-none cursor-pointer"
          >
            <option value="all">All Departments ({products.length})</option>
            {categories.map(c => (
              <option key={c.id} value={c.id}>{c.name}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Products Table */}
      <div className="bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-stone-50 border-b border-stone-200 text-stone-700 font-bold uppercase tracking-wider">
              <tr>
                <th className="py-3.5 px-4">Item</th>
                <th className="py-3.5 px-4">SKU</th>
                <th className="py-3.5 px-4">Department</th>
                <th className="py-3.5 px-4">Selling Price</th>
                <th className="py-3.5 px-4">MRP (Orig)</th>
                <th className="py-3.5 px-4">Total Stock</th>
                <th className="py-3.5 px-4">Badges</th>
                <th className="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-stone-100 font-medium text-stone-800">
              {filteredProducts.map((prod) => {
                const totalStock = prod.sizes?.reduce((sum, s) => sum + s.stock, 0) || 0;
                return (
                  <tr key={prod.id} className="hover:bg-stone-50/80 transition-colors">
                    <td className="py-3 px-4">
                      <div className="flex items-center gap-3">
                        <img 
                          src={prod.images?.[0]} 
                          alt={prod.name} 
                          className="w-12 h-14 object-cover rounded-xl shrink-0 border border-stone-200" 
                        />
                        <div className="min-w-0 max-w-xs">
                          <p className="font-bold text-stone-900 truncate">{prod.name}</p>
                          <p className="text-[10px] text-stone-400 capitalize">{prod.gender} fit</p>
                        </div>
                      </div>
                    </td>

                    <td className="py-3 px-4 font-mono text-stone-600 font-bold">{prod.sku}</td>

                    <td className="py-3 px-4">
                      <span className="bg-stone-100 text-stone-800 px-2 py-0.5 rounded font-semibold capitalize text-[11px]">
                        {prod.category}
                      </span>
                    </td>

                    <td className="py-3 px-4 font-mono font-bold text-stone-900">₹{prod.price.toLocaleString()}</td>

                    <td className="py-3 px-4 font-mono text-stone-400 line-through">
                      ₹{prod.originalPrice?.toLocaleString()}
                    </td>

                    <td className="py-3 px-4">
                      <span className={`font-mono font-bold px-2 py-0.5 rounded text-[11px] ${
                        totalStock > 10 
                          ? 'bg-emerald-50 text-emerald-700' 
                          : totalStock > 0 
                            ? 'bg-amber-50 text-amber-700' 
                            : 'bg-rose-50 text-rose-700'
                      }`}>
                        {totalStock} units
                      </span>
                    </td>

                    <td className="py-3 px-4">
                      <div className="flex items-center gap-1">
                        {prod.isFeatured && (
                          <span className="text-[9px] font-bold bg-purple-100 text-purple-800 px-1.5 py-0.2 rounded">Featured</span>
                        )}
                        {prod.isBestseller && (
                          <span className="text-[9px] font-bold bg-orange-100 text-orange-800 px-1.5 py-0.2 rounded">Bestseller</span>
                        )}
                      </div>
                    </td>

                    <td className="py-3 px-4 text-right">
                      <div className="flex items-center justify-end gap-1">
                        <button
                          onClick={() => handleOpenEditModal(prod)}
                          className="p-1.5 text-stone-500 hover:text-stone-900 rounded-lg hover:bg-stone-100"
                          title="Edit product"
                        >
                          <Edit3 className="w-4 h-4" />
                        </button>

                        <button
                          onClick={() => deleteProduct(prod.id)}
                          className="p-1.5 text-stone-400 hover:text-rose-600 rounded-lg hover:bg-stone-100"
                          title="Delete product"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>

      {/* Add / Edit Product Modal */}
      {modalOpen && (
        <div className="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-10 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setModalOpen(false)}></div>
          <div className="relative w-full max-w-2xl bg-white rounded-3xl p-6 sm:p-8 shadow-2xl z-10 animate-scale-in max-h-[90vh] overflow-y-auto space-y-5">
            
            <div className="flex items-center justify-between pb-4 border-b border-stone-100">
              <h3 className="font-serif text-xl font-bold text-stone-900">
                {editingProductId ? 'Edit Product Details' : 'Create & Publish New Garment'}
              </h3>
              <button onClick={() => setModalOpen(false)}><X className="w-5 h-5 text-stone-400" /></button>
            </div>

            <form onSubmit={handleSaveProduct} className="space-y-4 text-xs">
              <div>
                <label className="block font-bold text-stone-700 mb-1">Product Title *</label>
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="e.g. Kyoto Mineral Wash Oversized Drop Tee"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-semibold focus:outline-none focus:border-orange-500"
                  required
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Department / Category</label>
                  <select
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl capitalize font-medium"
                  >
                    {categories.map(c => (
                      <option key={c.id} value={c.id}>{c.name}</option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-stone-700 mb-1">Gender / Fit</label>
                  <select
                    value={gender}
                    onChange={(e) => setGender(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl capitalize font-medium"
                  >
                    <option value="unisex">Unisex</option>
                    <option value="men">Men</option>
                    <option value="women">Women</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold text-stone-700 mb-1">SKU Code</label>
                  <input
                    type="text"
                    value={sku}
                    onChange={(e) => setSku(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono font-bold"
                    required
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Selling Price (₹) *</label>
                  <input
                    type="number"
                    value={price}
                    onChange={(e) => setPrice(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono font-bold"
                    required
                  />
                </div>
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Original MRP (₹)</label>
                  <input
                    type="number"
                    value={originalPrice}
                    onChange={(e) => setOriginalPrice(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono"
                    required
                  />
                </div>
              </div>

              {/* Sizes and Inventory Count */}
              <div>
                <label className="block font-bold text-stone-700 mb-2">Variant Stock Counts by Size</label>
                <div className="grid grid-cols-5 gap-2">
                  <div>
                    <span className="block text-[10px] font-bold text-center text-stone-500 mb-0.5">Size S</span>
                    <input type="number" min="0" value={sizeS} onChange={(e) => setSizeS(e.target.value)} className="w-full bg-stone-50 border border-stone-200 p-2 text-center rounded-xl font-bold font-mono" />
                  </div>
                  <div>
                    <span className="block text-[10px] font-bold text-center text-stone-500 mb-0.5">Size M</span>
                    <input type="number" min="0" value={sizeM} onChange={(e) => setSizeM(e.target.value)} className="w-full bg-stone-50 border border-stone-200 p-2 text-center rounded-xl font-bold font-mono" />
                  </div>
                  <div>
                    <span className="block text-[10px] font-bold text-center text-stone-500 mb-0.5">Size L</span>
                    <input type="number" min="0" value={sizeL} onChange={(e) => setSizeL(e.target.value)} className="w-full bg-stone-50 border border-stone-200 p-2 text-center rounded-xl font-bold font-mono" />
                  </div>
                  <div>
                    <span className="block text-[10px] font-bold text-center text-stone-500 mb-0.5">Size XL</span>
                    <input type="number" min="0" value={sizeXL} onChange={(e) => setSizeXL(e.target.value)} className="w-full bg-stone-50 border border-stone-200 p-2 text-center rounded-xl font-bold font-mono" />
                  </div>
                  <div>
                    <span className="block text-[10px] font-bold text-center text-stone-500 mb-0.5">Size XXL</span>
                    <input type="number" min="0" value={sizeXXL} onChange={(e) => setSizeXXL(e.target.value)} className="w-full bg-stone-50 border border-stone-200 p-2 text-center rounded-xl font-bold font-mono" />
                  </div>
                </div>
              </div>

              {/* Image URLs */}
              <div className="space-y-2">
                <label className="block font-bold text-stone-700">Image Showcase URLs</label>
                <input
                  type="url"
                  value={imageUrl}
                  onChange={(e) => setImageUrl(e.target.value)}
                  placeholder="Primary Image URL"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono text-[11px]"
                />
                <input
                  type="url"
                  value={imageUrl2}
                  onChange={(e) => setImageUrl2(e.target.value)}
                  placeholder="Secondary Hover Image URL"
                  className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl font-mono text-[11px]"
                />
              </div>

              {/* Description & Fabric */}
              <div className="space-y-2">
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Product Description</label>
                  <textarea
                    rows={2}
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2.5 rounded-xl"
                  />
                </div>
                <div>
                  <label className="block font-bold text-stone-700 mb-1">Fabric &amp; Care Details</label>
                  <input
                    type="text"
                    value={fabricDetails}
                    onChange={(e) => setFabricDetails(e.target.value)}
                    className="w-full bg-stone-50 border border-stone-200 p-2 rounded-xl"
                  />
                </div>
              </div>

              {/* Badges Toggles */}
              <div className="flex items-center gap-6 pt-2">
                <label className="flex items-center gap-2 cursor-pointer font-semibold text-stone-700">
                  <input type="checkbox" checked={isFeatured} onChange={(e) => setIsFeatured(e.target.checked)} className="accent-orange-600" />
                  <span>Featured Product</span>
                </label>
                <label className="flex items-center gap-2 cursor-pointer font-semibold text-stone-700">
                  <input type="checkbox" checked={isBestseller} onChange={(e) => setIsBestseller(e.target.checked)} className="accent-orange-600" />
                  <span>Bestseller</span>
                </label>
              </div>

              <div className="flex gap-2 pt-3 border-t border-stone-100">
                <button
                  type="submit"
                  className="flex-1 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl text-xs shadow-md"
                >
                  {editingProductId ? 'Update Product' : 'Publish to Storefront'}
                </button>
                <button
                  type="button"
                  onClick={() => setModalOpen(false)}
                  className="px-6 py-3 border border-stone-200 rounded-xl font-semibold text-stone-600 hover:bg-stone-50"
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
