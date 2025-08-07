<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::with(['owner', 'agents']);
        
        // Filter by user role
        $user = auth()->user();
        if ($user->isAgent()) {
            $query->whereHas('agents', function ($q) use ($user) {
                $q->where('agent_id', $user->id);
            });
        } elseif ($user->isPrincipal()) {
            $query->where('owner_id', $user->id);
        }
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $properties = $query->latest()->paginate(12);
        
        return Inertia::render('properties/index', [
            'properties' => $properties,
            'filters' => $request->only(['type', 'listing_type', 'status', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get available owners and agents based on user role
        $owners = $user->isAdmin() 
            ? User::principals()->get() 
            : User::where('id', $user->id)->get();
            
        $agents = User::agents()->get();
        
        return Inertia::render('properties/create', [
            'owners' => $owners,
            'agents' => $agents,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request)
    {
        $validated = $request->validated();
        $agentIds = $validated['agent_ids'] ?? [];
        unset($validated['agent_ids']);
        
        $property = Property::create($validated);
        
        // Attach agents if provided
        if (!empty($agentIds)) {
            $property->agents()->attach($agentIds);
        }

        return redirect()->route('properties.show', $property)
            ->with('success', 'Property created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        $property->load(['owner', 'agents']);
        
        // Check authorization
        $user = auth()->user();
        if (!$user->isAdmin()) {
            if ($user->isAgent() && !$property->agents->contains($user)) {
                abort(403, 'Unauthorized access to this property.');
            }
            if ($user->isPrincipal() && $property->owner_id !== $user->id) {
                abort(403, 'Unauthorized access to this property.');
            }
        }
        
        return Inertia::render('properties/show', [
            'property' => $property,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        $property->load(['owner', 'agents']);
        $user = auth()->user();
        
        // Check authorization
        if (!$user->isAdmin()) {
            if ($user->isAgent() && !$property->agents->contains($user)) {
                abort(403, 'Unauthorized to edit this property.');
            }
            if ($user->isPrincipal() && $property->owner_id !== $user->id) {
                abort(403, 'Unauthorized to edit this property.');
            }
        }
        
        $owners = $user->isAdmin() 
            ? User::principals()->get() 
            : User::where('id', $property->owner_id)->get();
            
        $agents = User::agents()->get();
        
        return Inertia::render('properties/edit', [
            'property' => $property,
            'owners' => $owners,
            'agents' => $agents,
            'selectedAgentIds' => $property->agents->pluck('id')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $user = auth()->user();
        
        // Check authorization
        if (!$user->isAdmin()) {
            if ($user->isAgent() && !$property->agents->contains($user)) {
                abort(403, 'Unauthorized to update this property.');
            }
            if ($user->isPrincipal() && $property->owner_id !== $user->id) {
                abort(403, 'Unauthorized to update this property.');
            }
        }
        
        $validated = $request->validated();
        $agentIds = $validated['agent_ids'] ?? [];
        unset($validated['agent_ids']);
        
        $property->update($validated);
        
        // Sync agents
        $property->agents()->sync($agentIds);

        return redirect()->route('properties.show', $property)
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $user = auth()->user();
        
        // Check authorization
        if (!$user->isAdmin()) {
            if ($user->isAgent()) {
                abort(403, 'Agents cannot delete properties.');
            }
            if ($user->isPrincipal() && $property->owner_id !== $user->id) {
                abort(403, 'Unauthorized to delete this property.');
            }
        }
        
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}