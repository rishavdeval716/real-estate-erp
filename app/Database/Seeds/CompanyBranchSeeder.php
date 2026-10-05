<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanyBranchSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Seed Default Company
        $company = $db->table('companies')->where('id', 1)->get()->getRowArray();
        if (!$company) {
            $companyData = [
                'name'            => 'Apex Horizon Real Estate Corp',
                'logo'            => null,
                'email'           => 'contact@apexhorizon.com',
                'phone'           => '+91 22 2490 8800',
                'alternate_phone' => '+91 98200 12345',
                'address'         => 'Floor 18, Apex Horizon Towers, Financial District',
                'city'            => 'Mumbai',
                'state'           => 'Maharashtra',
                'country'         => 'India',
                'pincode'         => '400051',
                'website'         => 'https://apexhorizon.com',
                'tax_number'      => '27AAACA9988B1Z6',
                'description'     => 'Premier enterprise real estate developer and asset management group operating residential, commercial, and retail portfolios across metropolitan hubs.',
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ];
            $db->table('companies')->insert($companyData);
            $companyId = $db->insertID();
        } else {
            $companyId = $company['id'];
        }

        // 2. Seed Default Branches
        $branches = [
            [
                'company_id'   => $companyId,
                'name'         => 'Corporate Headquarters',
                'code'         => 'BR-HQ-001',
                'manager_name' => 'Vikram Singhania',
                'phone'        => '+91 22 2490 8801',
                'email'        => 'hq.mumbai@apexhorizon.com',
                'address'      => 'Apex Horizon Towers, BKC',
                'city'         => 'Mumbai',
                'state'        => 'Maharashtra',
                'country'      => 'India',
                'pincode'      => '400051',
                'status'       => 'active',
            ],
            [
                'company_id'   => $companyId,
                'name'         => 'North Regional Office',
                'code'         => 'BR-NCR-002',
                'manager_name' => 'Rajesh Sharma',
                'phone'        => '+91 124 450 9900',
                'email'        => 'delhi.ncr@apexhorizon.com',
                'address'      => 'Cyber City, DLF Phase 2',
                'city'         => 'Gurugram',
                'state'        => 'Haryana',
                'country'      => 'India',
                'pincode'      => '122002',
                'status'       => 'active',
            ],
            [
                'company_id'   => $companyId,
                'name'         => 'South Tech Hub Branch',
                'code'         => 'BR-BLR-003',
                'manager_name' => 'Ananya Iyer',
                'phone'        => '+91 80 4120 7700',
                'email'        => 'bengaluru@apexhorizon.com',
                'address'      => 'Outer Ring Road, Bellandur',
                'city'         => 'Bengaluru',
                'state'        => 'Karnataka',
                'country'      => 'India',
                'pincode'      => '560103',
                'status'       => 'active',
            ],
        ];

        foreach ($branches as $branch) {
            $existing = $db->table('branches')->where('code', $branch['code'])->get()->getRowArray();
            if (!$existing) {
                $branch['created_at'] = date('Y-m-d H:i:s');
                $branch['updated_at'] = date('Y-m-d H:i:s');
                $db->table('branches')->insert($branch);
            }
        }
    }
}
