'use client';

import { useState } from 'react';
import { Search, Calendar, MapPin, User, Phone, Mail, Download, Edit, X } from 'lucide-react';

interface Booking {
  id: string;
  bookingReference: string;
  customerName: string;
  customerEmail: string;
  customerPhone: string;
  pickup: string;
  dropoff: string;
  pickupDate: string;
  pickupTime: string;
  passengers: {
    adults: number;
    children: number;
    infants: number;
  };
  vehicle: string;
  price: number;
  currency: string;
  status: 'confirmed' | 'pending' | 'cancelled' | 'completed';
  editable: boolean;
  cancelable: boolean;
}

// Mock data - gerçek uygulamada API'den gelecek
const mockBookings: Booking[] = [
  {
    id: '1',
    bookingReference: 'MT2024001',
    customerName: 'Ahmet Yılmaz',
    customerEmail: 'ahmet@example.com',
    customerPhone: '+90 532 123 4567',
    pickup: 'İstanbul Havalimanı (IST)',
    dropoff: 'Sultanahmet, İstanbul',
    pickupDate: '2024-02-15',
    pickupTime: '14:30',
    passengers: { adults: 2, children: 1, infants: 0 },
    vehicle: 'Konfor Sedan',
    price: 45,
    currency: 'EUR',
    status: 'confirmed',
    editable: true,
    cancelable: true
  },
  {
    id: '2',
    bookingReference: 'MT2024002',
    customerName: 'Maria Garcia',
    customerEmail: 'maria@example.com',
    customerPhone: '+34 612 345 678',
    pickup: 'Barcelona-El Prat (BCN)',
    dropoff: 'Park Güell, Barcelona',
    pickupDate: '2024-02-20',
    pickupTime: '10:15',
    passengers: { adults: 1, children: 0, infants: 0 },
    vehicle: 'Ekonomi',
    price: 35,
    currency: 'EUR',
    status: 'pending',
    editable: true,
    cancelable: true
  },
  {
    id: '3',
    bookingReference: 'MT2024003',
    customerName: 'John Smith',
    customerEmail: 'john@example.com',
    customerPhone: '+44 7700 123456',
    pickup: 'Heathrow Airport (LHR)',
    dropoff: 'Central London',
    pickupDate: '2024-01-10',
    pickupTime: '16:45',
    passengers: { adults: 2, children: 0, infants: 0 },
    vehicle: 'Premium',
    price: 65,
    currency: 'EUR',
    status: 'completed',
    editable: false,
    cancelable: false
  }
];

export default function BookingsPage() {
  const [bookings, setBookings] = useState<Booking[]>(mockBookings);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedStatus, setSelectedStatus] = useState<string>('all');

  const filteredBookings = bookings.filter(booking => {
    const matchesSearch = 
      booking.bookingReference.toLowerCase().includes(searchTerm.toLowerCase()) ||
      booking.customerName.toLowerCase().includes(searchTerm.toLowerCase()) ||
      booking.customerEmail.toLowerCase().includes(searchTerm.toLowerCase()) ||
      booking.pickup.toLowerCase().includes(searchTerm.toLowerCase()) ||
      booking.dropoff.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = selectedStatus === 'all' || booking.status === selectedStatus;
    
    return matchesSearch && matchesStatus;
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed':
        return 'bg-green-100 text-green-800';
      case 'pending':
        return 'bg-yellow-100 text-yellow-800';
      case 'cancelled':
        return 'bg-red-100 text-red-800';
      case 'completed':
        return 'bg-blue-100 text-blue-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'confirmed':
        return 'Onaylandı';
      case 'pending':
        return 'Beklemede';
      case 'cancelled':
        return 'İptal Edildi';
      case 'completed':
        return 'Tamamlandı';
      default:
        return status;
    }
  };

  const handleEdit = (bookingId: string) => {
    console.log('Edit booking:', bookingId);
    // TODO: Implement edit functionality
  };

  const handleCancel = (bookingId: string) => {
    if (confirm('Bu rezervasyonu iptal etmek istediğinizden emin misiniz?')) {
      setBookings(prev => 
        prev.map(booking => 
          booking.id === bookingId 
            ? { ...booking, status: 'cancelled' as const, editable: false, cancelable: false }
            : booking
        )
      );
    }
  };

  const handleDownloadVoucher = (bookingId: string) => {
    console.log('Download voucher for booking:', bookingId);
    // TODO: Implement voucher download
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            Rezervasyonlarım
          </h1>
          <p className="text-lg text-gray-600">
            Transfer rezervasyonlarınızı görüntüleyin ve yönetin
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Search and Filters */}
        <div className="bg-white p-6 rounded-lg shadow-sm mb-8">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Search */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Rezervasyon kodu, isim veya email ara..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            {/* Status Filter */}
            <div>
              <select
                value={selectedStatus}
                onChange={(e) => setSelectedStatus(e.target.value)}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                <option value="all">Tüm Durumlar</option>
                <option value="confirmed">Onaylandı</option>
                <option value="pending">Beklemede</option>
                <option value="completed">Tamamlandı</option>
                <option value="cancelled">İptal Edildi</option>
              </select>
            </div>
          </div>
        </div>

        {/* Results Count */}
        <div className="mb-6">
          <p className="text-gray-600">
            <span className="font-semibold">{filteredBookings.length}</span> rezervasyon bulundu
          </p>
        </div>

        {/* Bookings List */}
        <div className="space-y-6">
          {filteredBookings.map((booking) => (
            <div
              key={booking.id}
              className="bg-white rounded-lg shadow-md overflow-hidden"
            >
              <div className="p-6">
                {/* Header */}
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center space-x-4">
                    <h3 className="text-xl font-bold text-gray-900">
                      {booking.bookingReference}
                    </h3>
                    <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(booking.status)}`}>
                      {getStatusText(booking.status)}
                    </span>
                  </div>
                  <div className="text-right">
                    <p className="text-2xl font-bold text-primary">
                      {booking.price} {booking.currency}
                    </p>
                  </div>
                </div>

                {/* Trip Details */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                  {/* Route */}
                  <div className="space-y-3">
                    <div className="flex items-start">
                      <MapPin className="w-5 h-5 text-green-600 mr-3 mt-1" />
                      <div>
                        <p className="font-medium text-gray-900">Kalkış</p>
                        <p className="text-gray-600">{booking.pickup}</p>
                      </div>
                    </div>
                    <div className="flex items-start">
                      <MapPin className="w-5 h-5 text-red-600 mr-3 mt-1" />
                      <div>
                        <p className="font-medium text-gray-900">Varış</p>
                        <p className="text-gray-600">{booking.dropoff}</p>
                      </div>
                    </div>
                  </div>

                  {/* Date & Time */}
                  <div className="space-y-3">
                    <div className="flex items-center">
                      <Calendar className="w-5 h-5 text-primary mr-3" />
                      <div>
                        <p className="font-medium text-gray-900">Tarih & Saat</p>
                        <p className="text-gray-600">
                          {new Date(booking.pickupDate).toLocaleDateString('tr-TR')} - {booking.pickupTime}
                        </p>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <User className="w-5 h-5 text-primary mr-3" />
                      <div>
                        <p className="font-medium text-gray-900">Yolcular</p>
                        <p className="text-gray-600">
                          {booking.passengers.adults} Yetişkin
                          {booking.passengers.children > 0 && `, ${booking.passengers.children} Çocuk`}
                          {booking.passengers.infants > 0 && `, ${booking.passengers.infants} Bebek`}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Customer Details */}
                <div className="bg-gray-50 p-4 rounded-lg mb-6">
                  <h4 className="font-semibold text-gray-900 mb-3">Müşteri Bilgileri</h4>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="flex items-center">
                      <User className="w-4 h-4 text-gray-400 mr-2" />
                      <span className="text-gray-600">{booking.customerName}</span>
                    </div>
                    <div className="flex items-center">
                      <Mail className="w-4 h-4 text-gray-400 mr-2" />
                      <span className="text-gray-600">{booking.customerEmail}</span>
                    </div>
                    <div className="flex items-center">
                      <Phone className="w-4 h-4 text-gray-400 mr-2" />
                      <span className="text-gray-600">{booking.customerPhone}</span>
                    </div>
                  </div>
                </div>

                {/* Vehicle Info */}
                <div className="mb-6">
                  <p className="text-sm text-gray-600 mb-1">Araç Tipi</p>
                  <p className="font-medium text-gray-900">{booking.vehicle}</p>
                </div>

                {/* Actions */}
                <div className="flex flex-wrap gap-3">
                  <button
                    onClick={() => handleDownloadVoucher(booking.id)}
                    className="flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors"
                  >
                    <Download className="w-4 h-4 mr-2" />
                    Voucher İndir
                  </button>
                  
                  {booking.editable && (
                    <button
                      onClick={() => handleEdit(booking.id)}
                      className="flex items-center px-4 py-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors"
                    >
                      <Edit className="w-4 h-4 mr-2" />
                      Düzenle
                    </button>
                  )}
                  
                  {booking.cancelable && (
                    <button
                      onClick={() => handleCancel(booking.id)}
                      className="flex items-center px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors"
                    >
                      <X className="w-4 h-4 mr-2" />
                      İptal Et
                    </button>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* No Results */}
        {filteredBookings.length === 0 && (
          <div className="text-center py-12">
            <div className="text-gray-400 mb-4">
              <Calendar className="w-16 h-16 mx-auto" />
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">
              Rezervasyon Bulunamadı
            </h3>
            <p className="text-gray-600 mb-4">
              Arama kriterlerinize uygun rezervasyon bulunamadı.
            </p>
            <button
              onClick={() => {
                setSearchTerm('');
                setSelectedStatus('all');
              }}
              className="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
            >
              Filtreleri Temizle
            </button>
          </div>
        )}
      </div>
    </div>
  );
}