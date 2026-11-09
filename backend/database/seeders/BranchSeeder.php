<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\StockLocation;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StockLocation::create([
            'code' => 'MAIN01',
            'name' => 'Warehouse',
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        $branches = [
            [
                'code' => 'JKT001',
                'name' => 'Jakarta',
                'city' => 'DKI Jakarta',
                'address' => 'Jl. Sapta Taruna Raya, Rusun Pasar Jumat, Blok Sakura L9 No.6 RT 001 RW 010, Keb. Lama, Pd. Pinang, DKI Jakarta 12310'
            ],
            [
                'code' => 'BDG001',
                'name' => 'Bandung',
            ],
            [
                'code' => 'PAL001',
                'name' => 'Palu',
                'address' => 'Permata Regency Blok H No. 7, Jl. Zebra, RT 009 RW 042, Tatura Selatan, Sulawesi Tengah 94231',
                'city' => 'Palu',
            ],
            [
                'code' => 'SRG001',
                'name' => 'Serang',
                'address' => 'Taman Graha Asri Blok BB-4 No. 8, Jl. Atut Sulastri Raya, Kota Serang, Banten 42116',
                'city' => 'Serang',
            ],
        ];
        
        foreach ($branches as $branch) {
            Branch::create($branch);
        }
        
        Branch::query()->get()->each(function (Branch $branch) {
            StockLocation::updateOrCreate(
                ['branch_id' => $branch->id],
                [
                    'code' => $branch->code,
                    'name' => $branch->name,
                    'type' => 'branch',
                    'is_active' => true,
                ]
            );
        });
    }
}
