<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Property
 *
 * @property int $id
 * @property string $type
 * @property string $title
 * @property string $address
 * @property string $price
 * @property string $listing_type
 * @property string|null $rent_period
 * @property string|null $land_area
 * @property string|null $building_area
 * @property int|null $bedrooms
 * @property int|null $bathrooms
 * @property string $description
 * @property array|null $photos
 * @property string $status
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $agents
 * @property-read int|null $agents_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Property newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Property query()
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereBathrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereBedrooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereBuildingArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereLandArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereListingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereRentPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Property available()
 * @method static \Illuminate\Database\Eloquent\Builder|Property forSale()
 * @method static \Illuminate\Database\Eloquent\Builder|Property forRent()
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'title',
        'address',
        'price',
        'listing_type',
        'rent_period',
        'land_area',
        'building_area',
        'bedrooms',
        'bathrooms',
        'description',
        'photos',
        'status',
        'owner_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'land_area' => 'decimal:2',
        'building_area' => 'decimal:2',
        'photos' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
    ];

    /**
     * Get the owner of the property.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the agents managing this property.
     */
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_agents', 'property_id', 'agent_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include available properties.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include properties for sale.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSale($query)
    {
        return $query->where('listing_type', 'sale');
    }

    /**
     * Scope a query to only include properties for rent.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForRent($query)
    {
        return $query->where('listing_type', 'rent');
    }

    /**
     * Get formatted price with currency.
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        $price = number_format((float) $this->price, 0, ',', '.');
        $suffix = $this->listing_type === 'rent' && $this->rent_period 
            ? " / {$this->rent_period}" 
            : '';
        
        return "Rp {$price}{$suffix}";
    }

    /**
     * Get property type display name.
     *
     * @return string
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'house' => 'House',
            'land' => 'Land',
            'warehouse' => 'Warehouse',
            'shop_house' => 'Shop House',
            'kiosk' => 'Kiosk',
            'boarding_house' => 'Boarding House',
            'building' => 'Building',
            'apartment' => 'Apartment',
            default => ucfirst($this->type)
        };
    }

    /**
     * Get status display name with color.
     *
     * @return array
     */
    public function getStatusDisplayAttribute(): array
    {
        return match($this->status) {
            'available' => ['label' => 'Available', 'color' => 'green'],
            'sold' => ['label' => 'Sold', 'color' => 'red'],
            'rented' => ['label' => 'Rented', 'color' => 'blue'],
            default => ['label' => ucfirst($this->status), 'color' => 'gray']
        };
    }
}