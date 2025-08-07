<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page with property marketplace.
     */
    public function index(Request $request)
    {
        $query = Property::with(['owner', 'agents'])
            ->available()
            ->latest();
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Apply listing type filter
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }
        
        // Apply price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        $properties = $query->paginate(12);
        
        // Get featured properties (latest 6)
        $featuredProperties = Property::with(['owner', 'agents'])
            ->available()
            ->latest()
            ->take(6)
            ->get();
        
        // Get property statistics for the homepage
        $stats = [
            'totalProperties' => Property::available()->count(),
            'forSale' => Property::available()->forSale()->count(),
            'forRent' => Property::available()->forRent()->count(),
            'propertyTypes' => Property::available()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray()
        ];
        
        return Inertia::render('welcome', [
            'properties' => $properties,
            'featuredProperties' => $featuredProperties,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'listing_type', 'min_price', 'max_price']),
        ]);
    }
}