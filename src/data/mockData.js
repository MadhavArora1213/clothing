export const initialCoupons = [
  {
    code: 'WELCOME10',
    type: 'percentage',
    discount: 10,
    minSpend: 999,
    description: '10% OFF on your first luxury order (Min. ₹999)',
    isActive: true,
    expiresAt: '2027-12-31'
  },
  {
    code: 'AURA20',
    type: 'percentage',
    discount: 20,
    minSpend: 2499,
    description: '20% OFF on orders above ₹2,499',
    isActive: true,
    expiresAt: '2027-12-31'
  },
  {
    code: 'FREESHIP',
    type: 'fixed',
    discount: 99,
    minSpend: 499,
    description: 'Free Express Shipping Voucher',
    isActive: true,
    expiresAt: '2027-12-31'
  },
  {
    code: 'FESTIVE500',
    type: 'fixed',
    discount: 500,
    minSpend: 3999,
    description: 'Flat ₹500 OFF on orders above ₹3,999',
    isActive: true,
    expiresAt: '2027-12-31'
  }
];

export const initialOrders = [
  {
    id: 'ATL-9842',
    orderNumber: 'ATL-9842',
    createdAt: '2026-08-19T14:32:00Z',
    customer: {
      name: 'Rohan Sharma',
      email: 'rohan.sharma@example.com',
      phone: '+91 98765 43210',
      address: 'Flat 402, Lotus Towers, Indiranagar',
      city: 'Bengaluru',
      state: 'Karnataka',
      pincode: '560038'
    },
    items: [
      {
        productId: 'prod-1',
        name: 'Vintage Nomad Acid-Wash Oversized Tee',
        price: 1299,
        size: 'L',
        color: 'Washed Charcoal',
        quantity: 2,
        image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=300&auto=format&fit=crop&q=80'
      },
      {
        productId: 'prod-7',
        name: 'AURA Monogram Embossed Heavy Canvas Tote',
        price: 899,
        size: 'FREE SIZE',
        color: 'Natural Canvas',
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=300&auto=format&fit=crop&q=80'
      }
    ],
    subtotal: 3497,
    discount: 349.70,
    shippingFee: 0,
    tax: 157.36,
    total: 3304.66,
    paymentMethod: 'UPI (Google Pay)',
    paymentStatus: 'Paid',
    status: 'Shipped', // Pending | Confirmed | Processing | Shipped | Delivered | Cancelled
    trackingNumber: 'DEL-IN-8839219',
    estimatedDelivery: '2026-08-23',
    timeline: [
      { stage: 'Order Placed', time: 'Aug 19, 2026, 02:32 PM', done: true },
      { stage: 'Confirmed', time: 'Aug 19, 2026, 02:40 PM', done: true },
      { stage: 'Processing & Packed', time: 'Aug 19, 2026, 06:15 PM', done: true },
      { stage: 'Shipped via Express Air', time: 'Aug 20, 2026, 09:30 AM', done: true },
      { stage: 'Out for Delivery', time: 'Expected Aug 23, 2026', done: false },
      { stage: 'Delivered', time: 'Pending', done: false }
    ]
  },
  {
    id: 'ATL-9731',
    orderNumber: 'ATL-9731',
    createdAt: '2026-08-18T10:15:00Z',
    customer: {
      name: 'Ananya Verma',
      email: 'ananya.v@example.com',
      phone: '+91 99887 76655',
      address: 'Villa 14, Palm Meadows, Whitefield',
      city: 'Bengaluru',
      state: 'Karnataka',
      pincode: '560066'
    },
    items: [
      {
        productId: 'prod-8',
        name: 'Aura Modern Chikankari Embroidered Short Kurti',
        price: 1799,
        size: 'M',
        color: 'Powder Blue',
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=300&auto=format&fit=crop&q=80'
      }
    ],
    subtotal: 1799,
    discount: 179.90,
    shippingFee: 0,
    tax: 80.95,
    total: 1700.05,
    paymentMethod: 'Cash On Delivery (COD)',
    paymentStatus: 'Pending',
    status: 'Processing',
    trackingNumber: 'DEL-IN-7749102',
    estimatedDelivery: '2026-08-22',
    timeline: [
      { stage: 'Order Placed', time: 'Aug 18, 2026, 10:15 AM', done: true },
      { stage: 'Confirmed', time: 'Aug 18, 2026, 10:30 AM', done: true },
      { stage: 'Processing & Packed', time: 'Aug 19, 2026, 11:00 AM', done: true },
      { stage: 'Shipped via Express Air', time: 'Pending', done: false },
      { stage: 'Out for Delivery', time: 'Pending', done: false },
      { stage: 'Delivered', time: 'Pending', done: false }
    ]
  },
  {
    id: 'ATL-9610',
    orderNumber: 'ATL-9610',
    createdAt: '2026-08-15T18:45:00Z',
    customer: {
      name: 'Vikramaditya Roy',
      email: 'vikram.roy@example.com',
      phone: '+91 97112 33445',
      address: '22 Park Street, 3rd Floor',
      city: 'Kolkata',
      state: 'West Bengal',
      pincode: '700016'
    },
    items: [
      {
        productId: 'prod-2',
        name: 'Artisanal Hand-Block Indigo Linen Kurta Set',
        price: 2899,
        size: 'L',
        color: 'Royal Indigo',
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=300&auto=format&fit=crop&q=80'
      }
    ],
    subtotal: 2899,
    discount: 500,
    shippingFee: 0,
    tax: 119.95,
    total: 2518.95,
    paymentMethod: 'Credit Card',
    paymentStatus: 'Paid',
    status: 'Delivered',
    trackingNumber: 'DEL-IN-6628109',
    estimatedDelivery: '2026-08-18',
    timeline: [
      { stage: 'Order Placed', time: 'Aug 15, 2026, 06:45 PM', done: true },
      { stage: 'Confirmed', time: 'Aug 15, 2026, 07:00 PM', done: true },
      { stage: 'Processing & Packed', time: 'Aug 16, 2026, 09:00 AM', done: true },
      { stage: 'Shipped via Express Air', time: 'Aug 16, 2026, 03:30 PM', done: true },
      { stage: 'Out for Delivery', time: 'Aug 18, 2026, 11:20 AM', done: true },
      { stage: 'Delivered', time: 'Aug 18, 2026, 02:45 PM', done: true }
    ]
  }
];

export const initialCustomers = [
  {
    id: 'cust-1',
    name: 'Rohan Sharma',
    email: 'rohan.sharma@example.com',
    phone: '+91 98765 43210',
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
    ordersCount: 4,
    totalSpend: 12450,
    status: 'Active',
    joinedDate: '2025-11-12'
  },
  {
    id: 'cust-2',
    name: 'Ananya Verma',
    email: 'ananya.v@example.com',
    phone: '+91 99887 76655',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80',
    ordersCount: 2,
    totalSpend: 5490,
    status: 'Active',
    joinedDate: '2026-02-05'
  },
  {
    id: 'cust-3',
    name: 'Vikramaditya Roy',
    email: 'vikram.roy@example.com',
    phone: '+91 97112 33445',
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
    ordersCount: 5,
    totalSpend: 18900,
    status: 'VIP',
    joinedDate: '2025-08-20'
  }
];

export const initialEnquiries = [
  {
    id: 'enq-101',
    name: 'Pooja Hegde',
    email: 'pooja.h@example.com',
    phone: '+91 91234 56789',
    subject: 'Size exchange for Chikankari Short Kurti',
    orderNumber: 'ATL-9731',
    category: 'Exchange & Return',
    message: 'Hello team, I received the Powder Blue Kurti yesterday in Size M, but I need Size S for a better fit. Could you please initiate a reverse pickup and exchange?',
    status: 'New', // New | In Progress | Resolved
    createdAt: '2026-08-20T11:15:00Z',
    adminNotes: ''
  },
  {
    id: 'enq-102',
    name: 'Sameer Khan',
    email: 'sameer.k@example.com',
    phone: '+91 98334 11223',
    subject: 'Bulk wedding order inquiry for Linen Kurta Sets',
    orderNumber: '',
    category: 'Custom & Bulk Orders',
    message: 'Hi Aura team, I am looking to place a bulk order of 25 Artisanal Indigo Linen Kurta Sets for an upcoming sangeet event in November. Do you offer custom sizing and bulk discounts?',
    status: 'In Progress',
    createdAt: '2026-08-19T16:40:00Z',
    adminNotes: 'Contacted over WhatsApp, sent fabric swatch catalog.'
  }
];

export const sizeChartData = {
  inches: [
    { size: 'XS', chest: '36"', length: '27"', shoulder: '17.5"', sleeve: '8.5"' },
    { size: 'S', chest: '38"', length: '28"', shoulder: '18.5"', sleeve: '9.0"' },
    { size: 'M', chest: '40"', length: '29"', shoulder: '19.5"', sleeve: '9.5"' },
    { size: 'L', chest: '42"', length: '30"', shoulder: '20.5"', sleeve: '10.0"' },
    { size: 'XL', chest: '44"', length: '31"', shoulder: '21.5"', sleeve: '10.5"' },
    { size: 'XXL', chest: '46"', length: '32"', shoulder: '22.5"', sleeve: '11.0"' }
  ],
  cm: [
    { size: 'XS', chest: '91 cm', length: '68 cm', shoulder: '44 cm', sleeve: '21.5 cm' },
    { size: 'S', chest: '96 cm', length: '71 cm', shoulder: '47 cm', sleeve: '23 cm' },
    { size: 'M', chest: '101 cm', length: '73 cm', shoulder: '49 cm', sleeve: '24 cm' },
    { size: 'L', chest: '106 cm', length: '76 cm', shoulder: '52 cm', sleeve: '25.5 cm' },
    { size: 'XL', chest: '111 cm', length: '78 cm', shoulder: '54 cm', sleeve: '26.5 cm' },
    { size: 'XXL', chest: '116 cm', length: '81 cm', shoulder: '57 cm', sleeve: '28 cm' }
  ]
};

export const testimonials = [
  {
    id: 1,
    name: 'Arjun Mehra',
    location: 'Mumbai',
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80',
    rating: 5,
    title: 'The fabric quality blew me away!',
    comment: 'The 260 GSM acid-wash drop tee feels like luxury designer streetwear without the insane markup. The collar holds shape even after 10 washes.',
    verified: true,
    product: 'Vintage Nomad Acid-Wash Tee'
  },
  {
    id: 2,
    name: 'Tanya Sengupta',
    location: 'New Delhi',
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&auto=format&fit=crop&q=80',
    rating: 5,
    title: 'Modern ethnic done right',
    comment: 'Reminds me of Arya Creation artisanal embroidery, but the modern cut and breathable modal makes it so wearable for day-to-night events!',
    verified: true,
    product: 'Chikankari Short Kurti'
  },
  {
    id: 3,
    name: 'Kabir Singhania',
    location: 'Bengaluru',
    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&auto=format&fit=crop&q=80',
    rating: 5,
    title: 'Super fast delivery and great packaging',
    comment: 'Ordered on Wednesday, delivered in eco-friendly custom matte box by Friday. The linen co-ord is my new go-to vacation fit.',
    verified: true,
    product: 'Sorrento Resort Linen Co-Ord'
  }
];

export const faqs = [
  {
    category: 'Orders & Shipping',
    q: 'How long does delivery take?',
    a: 'Metro cities receive delivery in 2-3 business days. Rest of India takes 4-5 business days. Express same-day shipping is available in selected metro hubs.'
  },
  {
    category: 'Orders & Shipping',
    q: 'What are the shipping charges?',
    a: 'We offer Free Express Shipping on all prepaid and COD orders above ₹999. For orders below ₹999, a standard flat fee of ₹99 applies.'
  },
  {
    category: 'Returns & Exchanges',
    q: 'What is your exchange and return policy?',
    a: 'We offer a 7-day hassle-free doorstep return & exchange policy from the date of delivery. Items must be unworn with original tags attached.'
  },
  {
    category: 'Returns & Exchanges',
    q: 'How do I initiate a return or size exchange?',
    a: 'Simply visit our Contact / Return portal or WhatsApp our support team with your Order ID. Our courier partner will arrange doorstep pickup.'
  },
  {
    category: 'Payments & Security',
    q: 'What payment modes are accepted?',
    a: 'We accept UPI (Google Pay, PhonePe, Paytm), Credit/Debit Cards (Visa, Mastercard, RuPay, Amex), Net Banking, and Cash On Delivery (COD).'
  }
];
