<?php

namespace Database\Seeders;

use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'warehouse', 'cashier', 'owner', 'support', 'finance'];
        
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@namada.com',
        ]);
        
        $user->assignRole('admin');
        $user->profile()->create([
            'bio' => 'Newly created at ' . date('Y-m-d'),
        ]);

        $user->branches()->syncWithPivotValues([1, 2, 3, 4], [
            'started_at' => now(),
            'expired_at' => '2036-12-31 23:59:59'
        ]);
        
        $member = User::factory()->create([
            'name' => 'Baba',
            'email' => 'baba@namada.com',
        ]);
            
        $member->assignRole('cashier');
        $member->profile()->create([
            'bio' => 'Newly created at ' . date('Y-m-d'),
        ]);

        $member->branches()->syncWithPivotValues([1, 2, 3, 4], [
            'started_at' => now(),
            'expired_at' => '2036-12-31 23:59:59'
        ]);
    }
}
