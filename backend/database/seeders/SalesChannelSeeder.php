<?php

namespace Database\Seeders;

use App\Models\SalesChannel;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesChannel::create(['name' => 'In-Store', 'slug' => 'in-store']);   
        SalesChannel::create(['name' => 'Shopee', 'slug' => 'shopee']);   
        SalesChannel::create(['name' => 'Tokopedia', 'slug' => 'tokopedia']);   
        SalesChannel::create(['name' => 'Website', 'slug' => 'website']);   
    }
}
