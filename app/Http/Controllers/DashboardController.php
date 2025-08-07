<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isAgent()) {
            return $this->agentDashboard($user);
        } else {
            return $this->principalDashboard($user);
        }
    }
    
    /**
     * Admin dashboard with system overview.
     */
    protected function adminDashboard()
    {
        $totalUsers = User::count();
        $totalAgents = User::agents()->count();
        $totalPrincipals = User::principals()->count();
        $totalProperties = Property::count();
        $activeProperties = Property::available()->count();
        $soldProperties = Property::where('status', 'sold')->count();
        $rentedProperties = Property::where('status', 'rented')->count();
        
        // Latest properties
        $latestProperties = Property::with(['owner', 'agents'])
            ->latest()
            ->take(5)
            ->get();
        
        // Properties by type
        $propertiesByType = Property::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type')
            ->toArray();
        
        // Monthly property stats (last 6 months)
        $monthlyStats = Property::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when listing_type = "sale" then 1 else 0 end) as sales'),
                DB::raw('sum(case when listing_type = "rent" then 1 else 0 end) as rentals')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return Inertia::render('dashboard/admin', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalAgents' => $totalAgents,
                'totalPrincipals' => $totalPrincipals,
                'totalProperties' => $totalProperties,
                'activeProperties' => $activeProperties,
                'soldProperties' => $soldProperties,
                'rentedProperties' => $rentedProperties,
            ],
            'latestProperties' => $latestProperties,
            'propertiesByType' => $propertiesByType,
            'monthlyStats' => $monthlyStats,
        ]);
    }
    
    /**
     * Agent dashboard with managed properties.
     */
    protected function agentDashboard(User $user)
    {
        $managedProperties = $user->managedProperties()->count();
        $activeProperties = $user->managedProperties()->where('status', 'available')->count();
        $soldProperties = $user->managedProperties()->where('status', 'sold')->count();
        $rentedProperties = $user->managedProperties()->where('status', 'rented')->count();
        
        // Recent properties managed by agent
        $recentProperties = $user->managedProperties()
            ->with(['owner'])
            ->latest()
            ->take(5)
            ->get();
        
        // Properties by type managed by agent
        $propertiesByType = $user->managedProperties()
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type')
            ->toArray();
        
        // Performance stats (last 6 months)
        $performanceStats = $user->managedProperties()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when status = "sold" then 1 else 0 end) as sold'),
                DB::raw('sum(case when status = "rented" then 1 else 0 end) as rented')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return Inertia::render('dashboard/agent', [
            'stats' => [
                'managedProperties' => $managedProperties,
                'activeProperties' => $activeProperties,
                'soldProperties' => $soldProperties,
                'rentedProperties' => $rentedProperties,
            ],
            'recentProperties' => $recentProperties,
            'propertiesByType' => $propertiesByType,
            'performanceStats' => $performanceStats,
        ]);
    }
    
    /**
     * Principal dashboard with owned properties.
     */
    protected function principalDashboard(User $user)
    {
        $ownedProperties = $user->ownedProperties()->count();
        $activeProperties = $user->ownedProperties()->where('status', 'available')->count();
        $soldProperties = $user->ownedProperties()->where('status', 'sold')->count();
        $rentedProperties = $user->ownedProperties()->where('status', 'rented')->count();
        
        // Recent properties owned by principal
        $recentProperties = $user->ownedProperties()
            ->with(['agents'])
            ->latest()
            ->take(5)
            ->get();
        
        // Properties by type owned by principal
        $propertiesByType = $user->ownedProperties()
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
            ->pluck('total', 'type')
            ->toArray();
        
        // Financial summary
        $totalSaleValue = $user->ownedProperties()
            ->where('listing_type', 'sale')
            ->where('status', 'available')
            ->sum('price');
            
        $monthlyRentalIncome = $user->ownedProperties()
            ->where('listing_type', 'rent')
            ->where('status', 'rented')
            ->where('rent_period', 'monthly')
            ->sum('price');
        
        return Inertia::render('dashboard/principal', [
            'stats' => [
                'ownedProperties' => $ownedProperties,
                'activeProperties' => $activeProperties,
                'soldProperties' => $soldProperties,
                'rentedProperties' => $rentedProperties,
                'totalSaleValue' => $totalSaleValue,
                'monthlyRentalIncome' => $monthlyRentalIncome,
            ],
            'recentProperties' => $recentProperties,
            'propertiesByType' => $propertiesByType,
        ]);
    }
}