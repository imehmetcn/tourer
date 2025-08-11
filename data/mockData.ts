import { Location, Transfer, VehicleType } from '@/types';

export const mockLocations: Location[] = [
  {
    id: 'ist-airport',
    name: 'İstanbul Havalimanı',
    type: 'airport',
    coordinates: { lat: 41.2619, lng: 28.7279 },
    country: 'Turkey',
    timezone: 'Europe/Istanbul',
  },
  {
    id: 'saw-airport',
    name: 'Sabiha Gökçen Havalimanı',
    type: 'airport',
    coordinates: { lat: 40.8986, lng: 29.3092 },
    country: 'Turkey',
    timezone: 'Europe/Istanbul',
  },
  {
    id: 'sultanahmet',
    name: 'Sultanahmet',
    type: 'city',
    coordinates: { lat: 41.0058, lng: 28.9784 },
    country: 'Turkey',
    timezone: 'Europe/Istanbul',
  },
  {
    id: 'taksim',
    name: 'Taksim',
    type: 'city',
    coordinates: { lat: 41.0369, lng: 28.9857 },
    country: 'Turkey',
    timezone: 'Europe/Istanbul',
  },
];

export const mockVehicleTypes: VehicleType[] = [
  {
    id: 'economy-sedan',
    name: 'Ekonomi Sedan',
    category: 'economy',
    capacity: { passengers: 4, luggage: 2 },
    image: '/vehicles/economy-sedan.jpg',
  },
  {
    id: 'standard-mpv',
    name: 'Standart MPV',
    category: 'standard',
    capacity: { passengers: 6, luggage: 4 },
    image: '/vehicles/standard-mpv.jpg',
  },
];

// This will be expanded in later tasks
export const mockTransfers: Transfer[] = [];
