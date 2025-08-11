// Core data types for the transfer booking system

export interface Location {
  id: string;
  name: string;
  type: 'airport' | 'city' | 'hotel' | 'address';
  coordinates: {
    lat: number;
    lng: number;
  };
  country: string;
  timezone: string;
}

export interface VehicleType {
  id: string;
  name: string;
  category: 'economy' | 'standard' | 'premium' | 'luxury';
  capacity: {
    passengers: number;
    luggage: number;
  };
  image: string;
}

export interface Price {
  amount: number;
  currency: string;
  formatted: string;
}

export interface Provider {
  name: string;
  rating: number;
}

export interface Transfer {
  id: string;
  from: Location;
  to: Location;
  vehicleType: VehicleType;
  price: Price;
  duration: number; // minutes
  distance: number; // kilometers
  amenities: string[];
  provider: Provider;
  availability: boolean;
}

export interface SearchParams {
  from: Location | null;
  to: Location | null;
  departureDate: Date;
  departureTime: string;
  returnDate?: Date;
  returnTime?: string;
  passengers: {
    adults: number;
    children: number;
    infants: number;
  };
  tripType: 'oneway' | 'roundtrip';
}

export interface PassengerInfo {
  firstName: string;
  lastName: string;
  email?: string;
  phone?: string;
  type: 'adult' | 'child' | 'infant';
}

export interface ContactInfo {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
}

export interface PaymentMethod {
  type: 'card' | 'paypal';
  details: any; // Will be defined when implementing payment
}

export interface BookingData {
  transfer: Transfer;
  passengers: PassengerInfo[];
  contactInfo: ContactInfo;
  specialRequests?: string;
  paymentMethod: PaymentMethod;
  totalPrice: Price;
}
