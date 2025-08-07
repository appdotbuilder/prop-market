<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users with different roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '08123456789',
                'address' => 'Admin Office Address'
            ]
        );

        $agents = [];
        for ($i = 1; $i <= 3; $i++) {
            $agents[] = User::firstOrCreate(
                ['email' => "agent{$i}@example.com"],
                [
                    'name' => "Agent {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'agent',
                    'phone' => '0812345678' . $i,
                    'address' => "Agent {$i} Address"
                ]
            );
        }

        $principals = [];
        for ($i = 1; $i <= 5; $i++) {
            $principals[] = User::firstOrCreate(
                ['email' => "principal{$i}@example.com"],
                [
                    'name' => "Principal {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'principal',
                    'phone' => '0823456789' . $i,
                    'address' => "Principal {$i} Address"
                ]
            );
        }

        // Create properties with different statuses and types
        $properties = collect();
        
        // Create available properties (mix of sale and rent)
        $availableProperties = Property::factory()
            ->count(30)
            ->available()
            ->create();
        $properties = $properties->merge($availableProperties);
        
        // Create some sold properties
        $soldProperties = Property::factory()
            ->count(8)
            ->sold()
            ->create();
        $properties = $properties->merge($soldProperties);
        
        // Create some rented properties
        $rentedProperties = Property::factory()
            ->count(12)
            ->rented()
            ->create();
        $properties = $properties->merge($rentedProperties);
        
        // Assign agents to properties randomly
        foreach ($properties as $property) {
            // Randomly assign 1-2 agents to each property
            $selectedAgents = collect($agents)
                ->random(random_int(1, 2))
                ->pluck('id')
                ->toArray();
            
            $property->agents()->attach($selectedAgents);
        }
        
        $this->command->info('Created ' . $properties->count() . ' properties with agent assignments.');
        $this->command->info('Admin: admin@example.com (password: password)');
        $this->command->info('Agents: agent1@example.com to agent3@example.com (password: password)');
        $this->command->info('Principals: principal1@example.com to principal5@example.com (password: password)');
    }
}