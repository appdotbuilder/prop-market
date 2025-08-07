import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { 
    Search, 
    Home, 
    Building2, 
    Warehouse, 
    Store, 
    MapPin, 
    Bed,
    Bath,

    TrendingUp,
    Users,
    Eye
} from 'lucide-react';

interface Property {
    id: number;
    type: string;
    title: string;
    address: string;
    price: string;
    listing_type: string;
    rent_period?: string;
    land_area?: string;
    building_area?: string;
    bedrooms?: number;
    bathrooms?: number;
    description: string;
    photos?: string[];
    status: string;
    owner: {
        id: number;
        name: string;
    };
    agents: Array<{
        id: number;
        name: string;
    }>;
    formatted_price: string;
    type_display: string;
    status_display: {
        label: string;
        color: string;
    };
}

interface Props {
    auth?: {
        user?: {
            id: number;
            name: string;
            email: string;
            role: string;
        };
    };
    properties: {
        data: Property[];
        links: unknown;
        meta: {
            total?: number;
        };
    };
    featuredProperties: Property[];
    stats: {
        totalProperties: number;
        forSale: number;
        forRent: number;
        propertyTypes: Record<string, number>;
    };
    filters: {
        search?: string;
        type?: string;
        listing_type?: string;
        min_price?: string;
        max_price?: string;
    };
    [key: string]: unknown;
}

export default function Welcome({ 
    auth, 
    properties, 
    featuredProperties, 
    stats, 
    filters 
}: Props) {
    const [searchForm, setSearchForm] = React.useState({
        search: filters.search || '',
        type: filters.type || '',
        listing_type: filters.listing_type || '',
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
    });

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        const params = new URLSearchParams();
        
        Object.entries(searchForm).forEach(([key, value]) => {
            if (value) {
                params.set(key, value);
            }
        });
        
        router.get(route('home'), Object.fromEntries(params));
    };

    const clearFilters = () => {
        setSearchForm({
            search: '',
            type: '',
            listing_type: '',
            min_price: '',
            max_price: '',
        });
        router.get(route('home'));
    };

    const propertyTypes = [
        { value: 'house', label: 'House', icon: Home },
        { value: 'apartment', label: 'Apartment', icon: Building2 },
        { value: 'land', label: 'Land', icon: MapPin },
        { value: 'warehouse', label: 'Warehouse', icon: Warehouse },
        { value: 'shop_house', label: 'Shop House', icon: Store },
        { value: 'kiosk', label: 'Kiosk', icon: Store },
        { value: 'boarding_house', label: 'Boarding House', icon: Building2 },
        { value: 'building', label: 'Building', icon: Building2 },
    ];

    const getPropertyTypeIcon = (type: string) => {
        const propertyType = propertyTypes.find(pt => pt.value === type);
        return propertyType?.icon || Home;
    };

    return (
        <>
            <Head title="Property Marketplace - Find Your Dream Property" />
            
            <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
                {/* Header */}
                <header className="bg-white shadow-sm border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center h-16">
                            <div className="flex items-center space-x-2">
                                <Building2 className="h-8 w-8 text-blue-600" />
                                <h1 className="text-xl font-bold text-gray-900">PropertyMarket</h1>
                            </div>
                            
                            <div className="flex items-center space-x-4">
                                {auth?.user ? (
                                    <div className="flex items-center space-x-4">
                                        <span className="text-sm text-gray-600">
                                            Welcome, {auth.user.name}
                                        </span>
                                        <Link href={route('dashboard')}>
                                            <Button>Dashboard</Button>
                                        </Link>
                                    </div>
                                ) : (
                                    <div className="flex items-center space-x-2">
                                        <Link href={route('login')}>
                                            <Button variant="ghost">Login</Button>
                                        </Link>
                                        <Link href={route('register')}>
                                            <Button>Register</Button>
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="py-12 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-7xl mx-auto text-center">
                        <h2 className="text-4xl font-bold text-gray-900 mb-4">
                            🏠 Find Your Perfect Property
                        </h2>
                        <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            Discover houses, lands, warehouses, apartments, and more. 
                            Your dream property is just a click away with our comprehensive marketplace.
                        </p>

                        {/* Stats */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
                            <div className="bg-white rounded-lg p-6 shadow-sm border">
                                <div className="flex items-center justify-center mb-2">
                                    <TrendingUp className="h-6 w-6 text-blue-600" />
                                </div>
                                <div className="text-2xl font-bold text-gray-900">
                                    {stats.totalProperties}
                                </div>
                                <div className="text-sm text-gray-600">Total Properties</div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-6 shadow-sm border">
                                <div className="flex items-center justify-center mb-2">
                                    <Home className="h-6 w-6 text-green-600" />
                                </div>
                                <div className="text-2xl font-bold text-gray-900">
                                    {stats.forSale}
                                </div>
                                <div className="text-sm text-gray-600">For Sale</div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-6 shadow-sm border">
                                <div className="flex items-center justify-center mb-2">
                                    <Building2 className="h-6 w-6 text-purple-600" />
                                </div>
                                <div className="text-2xl font-bold text-gray-900">
                                    {stats.forRent}
                                </div>
                                <div className="text-sm text-gray-600">For Rent</div>
                            </div>
                            
                            <div className="bg-white rounded-lg p-6 shadow-sm border">
                                <div className="flex items-center justify-center mb-2">
                                    <Users className="h-6 w-6 text-orange-600" />
                                </div>
                                <div className="text-2xl font-bold text-gray-900">
                                    {Object.keys(stats.propertyTypes).length}
                                </div>
                                <div className="text-sm text-gray-600">Property Types</div>
                            </div>
                        </div>

                        {/* Search Form */}
                        <Card className="max-w-4xl mx-auto">
                            <CardContent className="p-6">
                                <form onSubmit={handleSearch} className="space-y-4">
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                        <div className="lg:col-span-2">
                                            <div className="relative">
                                                <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                                                <Input
                                                    placeholder="Search properties..."
                                                    className="pl-10"
                                                    value={searchForm.search}
                                                    onChange={(e) => setSearchForm({
                                                        ...searchForm,
                                                        search: e.target.value
                                                    })}
                                                />
                                            </div>
                                        </div>
                                        
                                        <Select 
                                            value={searchForm.type} 
                                            onValueChange={(value) => setSearchForm({
                                                ...searchForm,
                                                type: value
                                            })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Property Type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="">All Types</SelectItem>
                                                {propertyTypes.map(type => (
                                                    <SelectItem key={type.value} value={type.value}>
                                                        {type.label}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        
                                        <Select 
                                            value={searchForm.listing_type} 
                                            onValueChange={(value) => setSearchForm({
                                                ...searchForm,
                                                listing_type: value
                                            })}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Sale/Rent" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="">Both</SelectItem>
                                                <SelectItem value="sale">For Sale</SelectItem>
                                                <SelectItem value="rent">For Rent</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        
                                        <Button type="submit" className="w-full">
                                            Search
                                        </Button>
                                    </div>
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <Input
                                            placeholder="Min Price (IDR)"
                                            type="number"
                                            value={searchForm.min_price}
                                            onChange={(e) => setSearchForm({
                                                ...searchForm,
                                                min_price: e.target.value
                                            })}
                                        />
                                        <Input
                                            placeholder="Max Price (IDR)"
                                            type="number"
                                            value={searchForm.max_price}
                                            onChange={(e) => setSearchForm({
                                                ...searchForm,
                                                max_price: e.target.value
                                            })}
                                        />
                                    </div>
                                    
                                    {(filters.search || filters.type || filters.listing_type || filters.min_price || filters.max_price) && (
                                        <Button 
                                            type="button" 
                                            variant="outline" 
                                            onClick={clearFilters}
                                            className="w-full"
                                        >
                                            Clear Filters
                                        </Button>
                                    )}
                                </form>
                            </CardContent>
                        </Card>
                    </div>
                </section>

                {/* Featured Properties */}
                {featuredProperties.length > 0 && (
                    <section className="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
                        <div className="max-w-7xl mx-auto">
                            <h3 className="text-2xl font-bold text-gray-900 mb-8 text-center">
                                ✨ Featured Properties
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {featuredProperties.slice(0, 6).map((property) => {
                                    const IconComponent = getPropertyTypeIcon(property.type);
                                    return (
                                        <Card key={property.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                                            <div className="relative">
                                                {property.photos && property.photos.length > 0 ? (
                                                    <img 
                                                        src={property.photos[0]} 
                                                        alt={property.title}
                                                        className="w-full h-48 object-cover"
                                                    />
                                                ) : (
                                                    <div className="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                        <IconComponent className="h-16 w-16 text-gray-400" />
                                                    </div>
                                                )}
                                                
                                                <div className="absolute top-2 left-2">
                                                    <Badge 
                                                        variant={property.listing_type === 'sale' ? 'default' : 'secondary'}
                                                    >
                                                        {property.listing_type === 'sale' ? 'For Sale' : 'For Rent'}
                                                    </Badge>
                                                </div>
                                                
                                                <div className="absolute top-2 right-2">
                                                    <Badge variant="outline" className="bg-white">
                                                        {property.type_display}
                                                    </Badge>
                                                </div>
                                            </div>
                                            
                                            <CardContent className="p-6">
                                                <h4 className="font-semibold text-lg mb-2 line-clamp-1">
                                                    {property.title}
                                                </h4>
                                                
                                                <div className="flex items-center text-gray-600 mb-2">
                                                    <MapPin className="h-4 w-4 mr-1" />
                                                    <span className="text-sm line-clamp-1">{property.address}</span>
                                                </div>
                                                
                                                <div className="text-2xl font-bold text-blue-600 mb-4">
                                                    {property.formatted_price}
                                                </div>
                                                
                                                <div className="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                                                    {property.bedrooms && (
                                                        <div className="flex items-center">
                                                            <Bed className="h-4 w-4 mr-1" />
                                                            <span>{property.bedrooms}</span>
                                                        </div>
                                                    )}
                                                    {property.bathrooms && (
                                                        <div className="flex items-center">
                                                            <Bath className="h-4 w-4 mr-1" />
                                                            <span>{property.bathrooms}</span>
                                                        </div>
                                                    )}
                                                    {property.land_area && (
                                                        <div className="flex items-center">
                                                            <span>{property.land_area}m²</span>
                                                        </div>
                                                    )}
                                                </div>
                                                
                                                <p className="text-gray-600 text-sm line-clamp-2 mb-4">
                                                    {property.description}
                                                </p>
                                                
                                                <div className="flex items-center justify-between">
                                                    <div className="text-xs text-gray-500">
                                                        By {property.owner.name}
                                                    </div>
                                                    <Button size="sm" variant="outline">
                                                        <Eye className="h-4 w-4 mr-1" />
                                                        View Details
                                                    </Button>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    );
                                })}
                            </div>
                        </div>
                    </section>
                )}

                {/* All Properties */}
                <section className="py-12 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-7xl mx-auto">
                        <div className="flex justify-between items-center mb-8">
                            <h3 className="text-2xl font-bold text-gray-900">
                                🏘️ All Properties ({properties.meta?.total || 0})
                            </h3>
                            
                            {auth?.user && (
                                <Link href={route('properties.create')}>
                                    <Button>
                                        Add Property
                                    </Button>
                                </Link>
                            )}
                        </div>
                        
                        {properties.data.length > 0 ? (
                            <>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                                    {properties.data.map((property) => {
                                        const IconComponent = getPropertyTypeIcon(property.type);
                                        return (
                                            <Card key={property.id} className="overflow-hidden hover:shadow-lg transition-shadow">
                                                <div className="relative">
                                                    {property.photos && property.photos.length > 0 ? (
                                                        <img 
                                                            src={property.photos[0]} 
                                                            alt={property.title}
                                                            className="w-full h-40 object-cover"
                                                        />
                                                    ) : (
                                                        <div className="w-full h-40 bg-gray-200 flex items-center justify-center">
                                                            <IconComponent className="h-12 w-12 text-gray-400" />
                                                        </div>
                                                    )}
                                                    
                                                    <div className="absolute top-2 left-2">
                                                        <Badge 
                                                            variant={property.listing_type === 'sale' ? 'default' : 'secondary'}
                                                            className="text-xs"
                                                        >
                                                            {property.listing_type === 'sale' ? 'Sale' : 'Rent'}
                                                        </Badge>
                                                    </div>
                                                </div>
                                                
                                                <CardContent className="p-4">
                                                    <h4 className="font-semibold mb-1 line-clamp-1 text-sm">
                                                        {property.title}
                                                    </h4>
                                                    
                                                    <div className="flex items-center text-gray-600 mb-2">
                                                        <MapPin className="h-3 w-3 mr-1" />
                                                        <span className="text-xs line-clamp-1">{property.address}</span>
                                                    </div>
                                                    
                                                    <div className="text-lg font-bold text-blue-600 mb-2">
                                                        {property.formatted_price}
                                                    </div>
                                                    
                                                    <div className="flex items-center space-x-2 text-xs text-gray-600 mb-3">
                                                        {property.bedrooms && (
                                                            <div className="flex items-center">
                                                                <Bed className="h-3 w-3 mr-1" />
                                                                <span>{property.bedrooms}</span>
                                                            </div>
                                                        )}
                                                        {property.bathrooms && (
                                                            <div className="flex items-center">
                                                                <Bath className="h-3 w-3 mr-1" />
                                                                <span>{property.bathrooms}</span>
                                                            </div>
                                                        )}
                                                        {property.land_area && (
                                                            <span>{property.land_area}m²</span>
                                                        )}
                                                    </div>
                                                    
                                                    <Button size="sm" variant="outline" className="w-full">
                                                        View Details
                                                    </Button>
                                                </CardContent>
                                            </Card>
                                        );
                                    })}
                                </div>
                                
                                {/* Pagination would go here if needed */}
                            </>
                        ) : (
                            <div className="text-center py-12">
                                <Building2 className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">No Properties Found</h3>
                                <p className="text-gray-600 mb-4">
                                    {(filters.search || filters.type || filters.listing_type) 
                                        ? "Try adjusting your search filters to find more properties."
                                        : "Be the first to list a property on our platform!"
                                    }
                                </p>
                                {auth?.user && (
                                    <Link href={route('properties.create')}>
                                        <Button>List Your Property</Button>
                                    </Link>
                                )}
                            </div>
                        )}
                    </div>
                </section>

                {/* Call to Action */}
                {!auth?.user && (
                    <section className="py-12 px-4 sm:px-6 lg:px-8 bg-blue-600">
                        <div className="max-w-4xl mx-auto text-center">
                            <h3 className="text-3xl font-bold text-white mb-4">
                                🚀 Ready to Get Started?
                            </h3>
                            <p className="text-xl text-blue-100 mb-8">
                                Join our platform to list your properties or find your dream home today!
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href={route('register')}>
                                    <Button size="lg" variant="secondary">
                                        Register Now
                                    </Button>
                                </Link>
                                <Link href={route('login')}>
                                    <Button size="lg" variant="outline" className="text-white border-white hover:bg-white hover:text-blue-600">
                                        Sign In
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </section>
                )}

                {/* Footer */}
                <footer className="bg-gray-900 text-white py-8 px-4 sm:px-6 lg:px-8">
                    <div className="max-w-7xl mx-auto text-center">
                        <div className="flex items-center justify-center space-x-2 mb-4">
                            <Building2 className="h-6 w-6" />
                            <span className="text-xl font-bold">PropertyMarket</span>
                        </div>
                        <p className="text-gray-400 mb-4">
                            Your trusted marketplace for properties in Indonesia
                        </p>
                        <div className="text-sm text-gray-500">
                            © 2024 PropertyMarket. All rights reserved.
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}