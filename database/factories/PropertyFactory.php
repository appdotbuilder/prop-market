<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([
            'house', 'land', 'warehouse', 'shop_house', 
            'kiosk', 'boarding_house', 'building', 'apartment'
        ]);
        
        $listingType = $this->faker->randomElement(['sale', 'rent']);
        $rentPeriod = $listingType === 'rent' ? $this->faker->randomElement(['monthly', 'yearly']) : null;
        
        // Price ranges based on property type and listing type
        $priceRanges = [
            'house' => $listingType === 'sale' ? [500000000, 2000000000] : [5000000, 25000000],
            'land' => $listingType === 'sale' ? [100000000, 1000000000] : [2000000, 15000000],
            'warehouse' => $listingType === 'sale' ? [800000000, 3000000000] : [15000000, 50000000],
            'shop_house' => $listingType === 'sale' ? [600000000, 1500000000] : [8000000, 30000000],
            'kiosk' => $listingType === 'sale' ? [50000000, 200000000] : [1000000, 5000000],
            'boarding_house' => $listingType === 'sale' ? [1000000000, 4000000000] : [20000000, 80000000],
            'building' => $listingType === 'sale' ? [2000000000, 10000000000] : [30000000, 150000000],
            'apartment' => $listingType === 'sale' ? [300000000, 1200000000] : [3000000, 20000000],
        ];
        
        $priceRange = $priceRanges[$type];
        $price = $this->faker->numberBetween($priceRange[0], $priceRange[1]);
        
        // Get random principal as owner
        $principals = User::principals()->pluck('id');
        if ($principals->isEmpty()) {
            $principals = collect([1]); // Fallback to user ID 1
        }
        
        $hasBuilding = !in_array($type, ['land']);
        
        return [
            'type' => $type,
            'title' => $this->generateTitle($type),
            'address' => $this->faker->streetAddress() . ', ' . $this->faker->city() . ', Indonesia',
            'price' => $price,
            'listing_type' => $listingType,
            'rent_period' => $rentPeriod,
            'land_area' => $this->faker->numberBetween(100, 2000),
            'building_area' => $hasBuilding ? $this->faker->numberBetween(80, 800) : null,
            'bedrooms' => in_array($type, ['house', 'apartment', 'boarding_house']) ? $this->faker->numberBetween(1, 6) : null,
            'bathrooms' => in_array($type, ['house', 'apartment', 'boarding_house']) ? $this->faker->numberBetween(1, 4) : null,
            'description' => $this->generateDescription($type, $listingType),
            'photos' => $this->faker->randomElement([
                null,
                [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800',
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800'
                ]
            ]),
            'status' => $this->faker->randomElement(['available', 'available', 'available', 'sold', 'rented']), // Mostly available
            'owner_id' => $this->faker->randomElement($principals),
        ];
    }
    
    /**
     * Generate property title based on type.
     */
    protected function generateTitle(string $type): string
    {
        $titles = [
            'house' => [
                'Beautiful Family House',
                'Modern Minimalist Home',
                'Cozy Two-Story House',
                'Spacious Corner House',
                'Elegant Contemporary Home'
            ],
            'land' => [
                'Prime Land for Development',
                'Strategic Location Plot',
                'Investment Land Opportunity',
                'Commercial Land for Sale',
                'Residential Plot Ready to Build'
            ],
            'warehouse' => [
                'Industrial Warehouse Facility',
                'Modern Storage Complex',
                'Logistics Hub Warehouse',
                'Distribution Center Building',
                'Multi-Purpose Warehouse'
            ],
            'shop_house' => [
                'Strategic Shop House',
                'Commercial Shop House',
                'Modern Ruko Building',
                'Business Ready Shop House',
                'Prime Location Ruko'
            ],
            'kiosk' => [
                'Business Kiosk Unit',
                'Strategic Kiosk Location',
                'Commercial Kiosk Space',
                'Ready-to-Operate Kiosk',
                'Prime Kiosk Opportunity'
            ],
            'boarding_house' => [
                'Profitable Boarding House',
                'Student Boarding Facility',
                'Modern Kos-kosan Building',
                'Income-Generating Property',
                'Strategic Boarding House'
            ],
            'building' => [
                'Commercial Office Building',
                'Multi-Story Business Complex',
                'Modern Office Tower',
                'Investment Grade Building',
                'Premium Commercial Building'
            ],
            'apartment' => [
                'Luxury Apartment Unit',
                'Modern Studio Apartment',
                'Spacious Family Apartment',
                'High-Rise Apartment',
                'Premium Condo Unit'
            ]
        ];
        
        return $this->faker->randomElement($titles[$type] ?? ['Property for Sale']);
    }
    
    /**
     * Generate property description based on type and listing type.
     */
    protected function generateDescription(string $type, string $listingType): string
    {
        $action = $listingType === 'sale' ? 'sale' : 'rent';
        
        $descriptions = [
            'house' => "This beautiful house is perfect for families looking for a comfortable living space. Features include spacious rooms, modern amenities, and a strategic location with easy access to schools, shopping centers, and public transportation. Available for {$action}.",
            'land' => "Prime land opportunity in a strategic location perfect for development or investment. Clear certificates, ready to build, with good access roads and complete utilities nearby. Ideal for residential or commercial development. Available for {$action}.",
            'warehouse' => "Modern warehouse facility suitable for storage, distribution, or manufacturing operations. Features high ceilings, loading dock access, and strategic location near major transportation routes. Perfect for logistics and industrial businesses. Available for {$action}.",
            'shop_house' => "Strategic shop house in prime commercial area with high foot traffic. Perfect for retail business, office, or mixed-use purposes. Ground floor suitable for shop/office, upper floors for residence or additional business space. Available for {$action}.",
            'kiosk' => "Well-positioned kiosk unit in busy commercial area with guaranteed foot traffic. Ideal for small retail business, food service, or service-oriented business. Ready to operate with existing utilities and strategic visibility. Available for {$action}.",
            'boarding_house' => "Established boarding house with proven rental income. Multiple rooms, shared facilities, and strategic location near universities or business districts. Perfect investment property with steady returns. Available for {$action}.",
            'building' => "Modern commercial building suitable for offices, retail, or mixed-use purposes. Multiple floors, elevator access, parking facilities, and premium location in business district. Excellent investment opportunity. Available for {$action}.",
            'apartment' => "Modern apartment unit with contemporary design and city views. Features include spacious living areas, modern kitchen, and access to building amenities like parking, security, and recreational facilities. Available for {$action}."
        ];
        
        return $descriptions[$type] ?? "Quality property in excellent condition, strategically located with great potential. Available for {$action}.";
    }
    
    /**
     * Create property that is available.
     */
    public function available(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }
    
    /**
     * Create property that is sold.
     */
    public function sold(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
            'listing_type' => 'sale',
        ]);
    }
    
    /**
     * Create property that is rented.
     */
    public function rented(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rented',
            'listing_type' => 'rent',
            'rent_period' => $this->faker->randomElement(['monthly', 'yearly']),
        ]);
    }
    
    /**
     * Create property for sale.
     */
    public function forSale(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'listing_type' => 'sale',
            'rent_period' => null,
        ]);
    }
    
    /**
     * Create property for rent.
     */
    public function forRent(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'listing_type' => 'rent',
            'rent_period' => $this->faker->randomElement(['monthly', 'yearly']),
        ]);
    }
}