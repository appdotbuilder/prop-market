<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'house', 'land', 'warehouse', 'shop_house', 
                'kiosk', 'boarding_house', 'building', 'apartment'
            ])->comment('Type of property');
            $table->string('title')->comment('Property listing title');
            $table->text('address')->comment('Full property address');
            $table->decimal('price', 15, 2)->comment('Property price');
            $table->enum('listing_type', ['sale', 'rent'])->comment('For sale or rent');
            $table->string('rent_period')->nullable()->comment('Rent period (monthly, yearly) if applicable');
            $table->decimal('land_area', 10, 2)->nullable()->comment('Land area in square meters');
            $table->decimal('building_area', 10, 2)->nullable()->comment('Building area in square meters');
            $table->integer('bedrooms')->nullable()->comment('Number of bedrooms');
            $table->integer('bathrooms')->nullable()->comment('Number of bathrooms');
            $table->text('description')->comment('Property description');
            $table->json('photos')->nullable()->comment('Property photos as JSON array');
            $table->enum('status', ['available', 'sold', 'rented'])->default('available')->comment('Property status');
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('type');
            $table->index('listing_type');
            $table->index('status');
            $table->index('price');
            $table->index(['status', 'listing_type']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};