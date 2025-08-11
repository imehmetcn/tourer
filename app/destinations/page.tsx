'use client';

import { useState } from 'react';
import { Search, MapPin, Star } from 'lucide-react';

const destinations = [
  {
    id: 1,
    name: 'İstanbul',
    country: 'Türkiye',
    continent: 'Avrupa',
    transfers: '2,500+',
    price: '€25',
    rating: 4.8,
    airports: ['İstanbul Havalimanı (IST)', 'Sabiha Gökçen (SAW)'],
    popular: true
  },
  {
    id: 2,
    name: 'Antalya',
    country: 'Türkiye',
    continent: 'Avrupa',
    transfers: '1,800+',
    price: '€20',
    rating: 4.7,
    airports: ['Antalya Havalimanı (AYT)'],
    popular: true
  },
  {
    id: 3,
    name: 'Barcelona',
    country: 'İspanya',
    continent: 'Avrupa',
    transfers: '3,200+',
    price: '€35',
    rating: 4.9,
    airports: ['Barcelona-El Prat (BCN)'],
    popular: true
  },
  {
    id: 4,
    name: 'Paris',
    country: 'Fransa',
    continent: 'Avrupa',
    transfers: '2,900+',
    price: '€45',
    rating: 4.8,
    airports: ['Charles de Gaulle (CDG)', 'Orly (ORY)'],
    popular: true
  },
  {
    id: 5,
    name: 'Roma',
    country: 'İtalya',
    continent: 'Avrupa',
    transfers: '2,100+',
    price: '€40',
    rating: 4.6,
    airports: ['Fiumicino (FCO)', 'Ciampino (CIA)'],
    popular: false
  },
  {
    id: 6,
    name: 'Londra',
    country: 'İngiltere',
    continent: 'Avrupa',
    transfers: '3,500+',
    price: '€50',
    rating: 4.7,
    airports: ['Heathrow (LHR)', 'Gatwick (LGW)', 'Stansted (STN)'],
    popular: true
  },
  {
    id: 7,
    name: 'Madrid',
    country: 'İspanya',
    continent: 'Avrupa',
    transfers: '2,200+',
    price: '€38',
    rating: 4.5,
    airports: ['Madrid-Barajas (MAD)'],
    popular: false
  },
  {
    id: 8,
    name: 'Amsterdam',
    country: 'Hollanda',
    continent: 'Avrupa',
    transfers: '1,900+',
    price: '€42',
    rating: 4.8,
    airports: ['Schiphol (AMS)'],
    popular: false
  },
  {
    id: 9,
    name: 'Berlin',
    country: 'Almanya',
    continent: 'Avrupa',
    transfers: '1,600+',
    price: '€36',
    rating: 4.6,
    airports: ['Brandenburg (BER)'],
    popular: false
  },
  {
    id: 10,
    name: 'Viyana',
    country: 'Avusturya',
    continent: 'Avrupa',
    transfers: '1,400+',
    price: '€39',
    rating: 4.7,
    airports: ['Viyana Havalimanı (VIE)'],
    popular: false
  }
];

const continents = ['Tümü', 'Avrupa', 'Asya', 'Amerika', 'Afrika'];

export default function DestinationsPage() {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedContinent, setSelectedContinent] = useState('Tümü');
  const [showPopularOnly, setShowPopularOnly] = useState(false);

  const filteredDestinations = destinations.filter(destination => {
    const matchesSearch = destination.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         destination.country.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesContinent = selectedContinent === 'Tümü' || destination.continent === selectedContinent;
    const matchesPopular = !showPopularOnly || destination.popular;
    
    return matchesSearch && matchesContinent && matchesPopular;
  });

  const renderStars = (rating: number) => {
    return Array.from({ length: 5 }, (_, index) => (
      <Star
        key={index}
        className={`w-4 h-4 ${
          index < Math.floor(rating)
            ? 'text-yellow-400 fill-current'
            : 'text-gray-300'
        }`}
      />
    ));
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            Transfer Destinasyonları
          </h1>
          <p className="text-lg text-gray-600">
            Dünya çapında {destinations.length}+ destinasyona güvenli transfer hizmetleri
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Filters */}
        <div className="bg-white p-6 rounded-lg shadow-sm mb-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Search */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Şehir veya ülke ara..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              />
            </div>

            {/* Continent Filter */}
            <div>
              <select
                value={selectedContinent}
                onChange={(e) => setSelectedContinent(e.target.value)}
                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
                {continents.map(continent => (
                  <option key={continent} value={continent}>
                    {continent}
                  </option>
                ))}
              </select>
            </div>

            {/* Popular Filter */}
            <div className="flex items-center">
              <label className="flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  checked={showPopularOnly}
                  onChange={(e) => setShowPopularOnly(e.target.checked)}
                  className="sr-only"
                />
                <div className={`relative w-12 h-6 rounded-full transition-colors ${
                  showPopularOnly ? 'bg-primary' : 'bg-gray-300'
                }`}>
                  <div className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform ${
                    showPopularOnly ? 'translate-x-6' : 'translate-x-0'
                  }`} />
                </div>
                <span className="ml-3 text-gray-700">Sadece Popüler Destinasyonlar</span>
              </label>
            </div>
          </div>
        </div>

        {/* Results Count */}
        <div className="mb-6">
          <p className="text-gray-600">
            <span className="font-semibold">{filteredDestinations.length}</span> destinasyon bulundu
          </p>
        </div>

        {/* Destinations Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredDestinations.map((destination) => (
            <div
              key={destination.id}
              className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300"
            >
              {/* Image Placeholder */}
              <div className="relative h-48 bg-gray-200 flex items-center justify-center">
                {destination.popular && (
                  <div className="absolute top-4 left-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold flex items-center">
                    <Star className="w-4 h-4 mr-1 fill-current" />
                    Popüler
                  </div>
                )}
                <div className="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-sm font-semibold">
                  {destination.price}&apos;den başlayan
                </div>
                <span className="text-gray-400 text-sm">
                  {destination.name} Görseli
                </span>
              </div>

              {/* Content */}
              <div className="p-6">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-xl font-bold text-gray-900">
                    {destination.name}
                  </h3>
                  <div className="flex items-center">
                    {renderStars(destination.rating)}
                    <span className="ml-1 text-sm text-gray-600">
                      {destination.rating}
                    </span>
                  </div>
                </div>

                <p className="text-gray-600 mb-4 flex items-center">
                  <MapPin className="w-4 h-4 mr-1" />
                  {destination.country}
                </p>

                {/* Airports */}
                <div className="mb-4">
                  <h4 className="text-sm font-semibold text-gray-900 mb-2">
                    Havalimanları:
                  </h4>
                  <div className="space-y-1">
                    {destination.airports.map((airport, index) => (
                      <span
                        key={index}
                        className="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-2 mb-1"
                      >
                        {airport}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Stats */}
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <p className="text-sm text-gray-600">Toplam Transfer</p>
                    <p className="font-semibold text-gray-900">{destination.transfers}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm text-gray-600">Başlangıç Fiyatı</p>
                    <p className="font-semibold text-primary text-lg">{destination.price}</p>
                  </div>
                </div>

                {/* Action Button */}
                <button className="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-lg font-semibold transition-colors">
                  Transfer Seçeneklerini Gör
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* No Results */}
        {filteredDestinations.length === 0 && (
          <div className="text-center py-12">
            <div className="text-gray-400 mb-4">
              <MapPin className="w-16 h-16 mx-auto" />
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">
              Destinasyon Bulunamadı
            </h3>
            <p className="text-gray-600 mb-4">
              Arama kriterlerinize uygun destinasyon bulunamadı.
            </p>
            <button
              onClick={() => {
                setSearchTerm('');
                setSelectedContinent('Tümü');
                setShowPopularOnly(false);
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