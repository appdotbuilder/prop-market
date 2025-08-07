import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { 
    Building2, 
    TrendingUp, 
    Eye,
    Home,
    Calendar,
    MapPin,
    Target,
    Award
} from 'lucide-react';

interface Property {
    id: number;
    title: string;
    address: string;
    type: string;
    listing_type: string;
    formatted_price: string;
    status: string;
    owner: {
        name: string;
    };
    created_at: string;
}

interface Props {
    stats: {
        managedProperties: number;
        activeProperties: number;
        soldProperties: number;
        rentedProperties: number;
    };
    recentProperties: Property[];
    propertiesByType: Record<string, number>;
    performanceStats: Array<{
        month: string;
        total: number;
        sold: number;
        rented: number;
    }>;
    [key: string]: unknown;
}

export default function AgentDashboard({ 
    stats, 
    recentProperties, 
    propertiesByType, 
    performanceStats 
}: Props) {
    const propertyTypeLabels: Record<string, string> = {
        house: 'Houses',
        apartment: 'Apartments',
        land: 'Land',
        warehouse: 'Warehouses',
        shop_house: 'Shop Houses',
        kiosk: 'Kiosks',
        boarding_house: 'Boarding Houses',
        building: 'Buildings'
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'available': return 'bg-green-100 text-green-800';
            case 'sold': return 'bg-red-100 text-red-800';
            case 'rented': return 'bg-blue-100 text-blue-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const totalDeals = stats.soldProperties + stats.rentedProperties;
    const successRate = stats.managedProperties > 0 ? 
        ((totalDeals / stats.managedProperties) * 100).toFixed(1) : '0.0';

    return (
        <AppShell>
            <Head title="Agent Dashboard" />
            
            <div className="space-y-6">
                {/* Header */}
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">🏢 Agent Dashboard</h1>
                        <p className="text-gray-600">Your property management overview</p>
                    </div>
                    
                    <div className="flex space-x-2">
                        <Link href={route('properties.index')}>
                            <Button variant="outline">
                                <Eye className="h-4 w-4 mr-2" />
                                View Properties
                            </Button>
                        </Link>
                        <Link href={route('properties.create')}>
                            <Button>
                                <Building2 className="h-4 w-4 mr-2" />
                                Add Property
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Card>
                        <CardContent className="p-6">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <Building2 className="h-8 w-8 text-blue-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Managed Properties</p>
                                    <p className="text-2xl font-bold text-gray-900">{stats.managedProperties}</p>
                                    <p className="text-xs text-gray-500">{stats.activeProperties} available</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent className="p-6">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <TrendingUp className="h-8 w-8 text-green-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Properties Sold</p>
                                    <p className="text-2xl font-bold text-gray-900">{stats.soldProperties}</p>
                                    <p className="text-xs text-green-600">Completed sales</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent className="p-6">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <Home className="h-8 w-8 text-purple-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Properties Rented</p>
                                    <p className="text-2xl font-bold text-gray-900">{stats.rentedProperties}</p>
                                    <p className="text-xs text-blue-600">Active rentals</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent className="p-6">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">
                                    <Award className="h-8 w-8 text-orange-600" />
                                </div>
                                <div className="ml-4">
                                    <p className="text-sm font-medium text-gray-600">Success Rate</p>
                                    <p className="text-2xl font-bold text-gray-900">{successRate}%</p>
                                    <p className="text-xs text-orange-600">{totalDeals} total deals</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Property Types */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <Target className="h-5 w-5 mr-2" />
                                Properties by Type
                            </CardTitle>
                            <CardDescription>
                                Your managed properties by type
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {Object.entries(propertiesByType).map(([type, count]) => (
                                    <div key={type} className="flex items-center justify-between">
                                        <span className="text-sm font-medium text-gray-700">
                                            {propertyTypeLabels[type] || type}
                                        </span>
                                        <Badge variant="secondary">{count}</Badge>
                                    </div>
                                ))}
                                {Object.keys(propertiesByType).length === 0 && (
                                    <p className="text-gray-500 text-center py-4">No properties assigned yet</p>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Performance Statistics */}
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <TrendingUp className="h-5 w-5 mr-2" />
                                Performance Overview
                            </CardTitle>
                            <CardDescription>
                                Your deals over the last 6 months
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {performanceStats.map((stat) => (
                                    <div key={stat.month} className="flex items-center justify-between">
                                        <span className="text-sm font-medium text-gray-700">
                                            {new Date(stat.month + '-01').toLocaleDateString('en-US', { 
                                                month: 'short', 
                                                year: 'numeric' 
                                            })}
                                        </span>
                                        <div className="flex space-x-2">
                                            <Badge variant="outline">{stat.total} managed</Badge>
                                            <Badge variant="default">{stat.sold} sold</Badge>
                                            <Badge variant="secondary">{stat.rented} rented</Badge>
                                        </div>
                                    </div>
                                ))}
                                {performanceStats.length === 0 && (
                                    <p className="text-gray-500 text-center py-4">No performance data yet</p>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Recent Properties */}
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle className="flex items-center">
                                <Calendar className="h-5 w-5 mr-2" />
                                Recent Properties
                            </CardTitle>
                            <CardDescription>
                                Properties you're currently managing
                            </CardDescription>
                        </div>
                        <Link href={route('properties.index')}>
                            <Button variant="outline" size="sm">
                                View All
                            </Button>
                        </Link>
                    </CardHeader>
                    <CardContent>
                        {recentProperties.length > 0 ? (
                            <div className="space-y-4">
                                {recentProperties.map((property) => (
                                    <div key={property.id} className="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                        <div className="flex-1">
                                            <div className="flex items-center space-x-2 mb-1">
                                                <h3 className="font-semibold text-gray-900">{property.title}</h3>
                                                <Badge 
                                                    variant={property.listing_type === 'sale' ? 'default' : 'secondary'}
                                                    className="text-xs"
                                                >
                                                    {property.listing_type === 'sale' ? 'Sale' : 'Rent'}
                                                </Badge>
                                                <span className={`text-xs px-2 py-1 rounded-full ${getStatusColor(property.status)}`}>
                                                    {property.status}
                                                </span>
                                            </div>
                                            <div className="flex items-center text-gray-600 text-sm mb-1">
                                                <MapPin className="h-4 w-4 mr-1" />
                                                {property.address}
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <div className="text-lg font-bold text-blue-600">
                                                    {property.formatted_price}
                                                </div>
                                                <div className="text-xs text-gray-500">
                                                    Owner: {property.owner.name}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-2 ml-4">
                                            <Link href={route('properties.show', property.id)}>
                                                <Button variant="outline" size="sm">
                                                    <Eye className="h-4 w-4 mr-1" />
                                                    View
                                                </Button>
                                            </Link>
                                            <Link href={route('properties.edit', property.id)}>
                                                <Button variant="outline" size="sm">
                                                    Edit
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <Building2 className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">No Properties Assigned</h3>
                                <p className="text-gray-600 mb-4">You don't have any properties to manage yet. Contact your admin to get assigned to properties.</p>
                                <Link href={route('properties.create')}>
                                    <Button>Add New Property</Button>
                                </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppShell>
    );
}