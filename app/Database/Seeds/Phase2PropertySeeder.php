<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Libraries\PropertyStatus;

class Phase2PropertySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Property Types (All 11 required by prompt)
        $propertyTypes = [
            ['name' => 'Residential',       'slug' => 'residential',       'description' => 'Residential living spaces, apartments, and private dwellings', 'status' => 'active'],
            ['name' => 'Commercial',        'slug' => 'commercial',        'description' => 'Commercial office complexes, retail premises, and hubs',       'status' => 'active'],
            ['name' => 'Plot/Land',         'slug' => 'plot-land',         'description' => 'Open residential, commercial, or mixed-use land plots',       'status' => 'active'],
            ['name' => 'Industrial',        'slug' => 'industrial',        'description' => 'Industrial manufacturing plants, sheds, and assembly sites',    'status' => 'active'],
            ['name' => 'Agricultural',      'slug' => 'agricultural',      'description' => 'Fertile agricultural farmland and rural acreage',               'status' => 'active'],
            ['name' => 'Apartment',         'slug' => 'apartment',         'description' => 'Multi-storey luxury and standard residential flats',           'status' => 'active'],
            ['name' => 'Villa',             'slug' => 'villa',             'description' => 'Independent gated community luxury villas',                    'status' => 'active'],
            ['name' => 'Independent House', 'slug' => 'independent-house', 'description' => 'Stand-alone residential bungalows and houses',                 'status' => 'active'],
            ['name' => 'Office',            'slug' => 'office',            'description' => 'Grade-A corporate office suites and IT facilities',           'status' => 'active'],
            ['name' => 'Shop',              'slug' => 'shop',              'description' => 'High-street retail shops and showroom spaces',                 'status' => 'active'],
            ['name' => 'Warehouse',         'slug' => 'warehouse',         'description' => 'Logistics warehouses and distribution hubs',                   'status' => 'active'],
        ];

        foreach ($propertyTypes as $type) {
            $existing = $db->table('property_types')->where('slug', $type['slug'])->get()->getRowArray();
            if (!$existing) {
                $type['created_at'] = date('Y-m-d H:i:s');
                $type['updated_at'] = date('Y-m-d H:i:s');
                $db->table('property_types')->insert($type);
            }
        }

        // 2. Amenities (All 10 required by prompt)
        $amenities = [
            ['name' => 'Parking',        'icon' => 'ri-car-line',              'description' => 'Dedicated covered and open parking spaces', 'status' => 'active'],
            ['name' => 'Lift',           'icon' => 'ri-arrow-up-down-line',    'description' => 'High-speed passenger and stretcher elevators', 'status' => 'active'],
            ['name' => 'Gym',            'icon' => 'ri-heart-pulse-line',      'description' => 'Fully equipped fitness center and gym', 'status' => 'active'],
            ['name' => 'Swimming Pool',  'icon' => 'ri-water-flash-line',      'description' => 'Temperature-controlled swimming pool with kids deck', 'status' => 'active'],
            ['name' => 'Clubhouse',      'icon' => 'ri-community-line',        'description' => 'Multi-purpose recreational community clubhouse', 'status' => 'active'],
            ['name' => 'Garden',         'icon' => 'ri-plant-line',            'description' => 'Landscaped central gardens and walking trails', 'status' => 'active'],
            ['name' => 'CCTV',           'icon' => 'ri-video-chat-line',       'description' => '24/7 CCTV surveillance across perimeter and lobbies', 'status' => 'active'],
            ['name' => 'Security',       'icon' => 'ri-shield-check-line',     'description' => 'Manned entrance security and boom barriers', 'status' => 'active'],
            ['name' => 'Power Backup',   'icon' => 'ri-flashlight-line',       'description' => '100% DG generator emergency power backup', 'status' => 'active'],
            ['name' => 'Water Supply',   'icon' => 'ri-drop-line',             'description' => '24-hour treated municipal and borewell water supply', 'status' => 'active'],
        ];

        foreach ($amenities as $amenity) {
            $existing = $db->table('amenities')->where('name', $amenity['name'])->get()->getRowArray();
            if (!$existing) {
                $amenity['created_at'] = date('Y-m-d H:i:s');
                $amenity['updated_at'] = date('Y-m-d H:i:s');
                $db->table('amenities')->insert($amenity);
            }
        }

        // 3. Locations
        $locations = [
            [
                'state'            => 'Maharashtra',
                'city'             => 'Mumbai',
                'area'             => 'Bandra West',
                'locality'         => 'Pali Hill',
                'landmark'         => 'Near Carter Road Promenade',
                'pincode'          => '400050',
                'nearby_locations' => 'Khar West, Santacruz, Bandra-Worli Sea Link',
                'map_location'     => '19.0607° N, 72.8277° E',
                'status'           => 'active',
            ],
            [
                'state'            => 'Maharashtra',
                'city'             => 'Pune',
                'area'             => 'Kharadi',
                'locality'         => 'EON Free Zone',
                'landmark'         => 'Adjacent to World Trade Center',
                'pincode'          => '411014',
                'nearby_locations' => 'Viman Nagar, Kalyani Nagar, Magarpatta',
                'map_location'     => '18.5514° N, 73.9352° E',
                'status'           => 'active',
            ],
            [
                'state'            => 'Karnataka',
                'city'             => 'Bengaluru',
                'area'             => 'Whitefield',
                'locality'         => 'EPIP Zone',
                'landmark'         => 'ITPL Main Road',
                'pincode'          => '560066',
                'nearby_locations' => 'Brookefield, Hoodi, Marathahalli',
                'map_location'     => '12.9698° N, 77.7500° E',
                'status'           => 'active',
            ],
            [
                'state'            => 'Haryana',
                'city'             => 'Gurugram',
                'area'             => 'Golf Course Road',
                'locality'         => 'DLF Phase 5',
                'landmark'         => 'Near Sector 42 Metro Station',
                'pincode'          => '122002',
                'nearby_locations' => 'Cyber Hub, MG Road, Sushant Lok',
                'map_location'     => '28.4595° N, 77.0266° E',
                'status'           => 'active',
            ],
        ];

        $locationMap = [];
        foreach ($locations as $loc) {
            $existing = $db->table('locations')->where(['city' => $loc['city'], 'area' => $loc['area']])->get()->getRowArray();
            if (!$existing) {
                $loc['created_at'] = date('Y-m-d H:i:s');
                $loc['updated_at'] = date('Y-m-d H:i:s');
                $db->table('locations')->insert($loc);
                $locationMap[$loc['city']] = $db->insertID();
            } else {
                $locationMap[$loc['city']] = $existing['id'];
            }
        }

        // 4. Projects
        $projects = [
            [
                'project_code'        => 'PRJ-MUM-SKY01',
                'name'                => 'Skyline Horizon Residences',
                'description'         => 'Ultra-luxury sea-facing apartments and penthouses in prime Bandra West with world-class clubhouse amenities.',
                'builder_developer'   => 'Skyline Luxury Infra Group',
                'location_id'         => $locationMap['Mumbai'] ?? 1,
                'construction_status' => 'Under Construction',
                'possession_date'     => '2027-12-31',
                'total_units'         => 0,
                'available_units'     => 0,
                'status'              => 'active',
            ],
            [
                'project_code'        => 'PRJ-PUN-APX02',
                'name'                => 'Apex Prime Business Towers',
                'description'         => 'Grade-A corporate office hub and retail boulevard with LEED Gold green building certification in Kharadi IT corridor.',
                'builder_developer'   => 'Apex Commercial Realty',
                'location_id'         => $locationMap['Pune'] ?? 2,
                'construction_status' => 'Ready to Move',
                'possession_date'     => '2026-03-31',
                'total_units'         => 0,
                'available_units'     => 0,
                'status'              => 'active',
            ],
            [
                'project_code'        => 'PRJ-BLR-GRN03',
                'name'                => 'Green Meadows Luxury Villas',
                'description'         => 'Exclusive gated enclave of eco-designed sustainable villas featuring private lawns and clubhouse in Whitefield.',
                'builder_developer'   => 'Meadows Eco Living Ltd',
                'location_id'         => $locationMap['Bengaluru'] ?? 3,
                'construction_status' => 'Pre-Launch',
                'possession_date'     => '2028-06-30',
                'total_units'         => 0,
                'available_units'     => 0,
                'status'              => 'active',
            ],
        ];

        $projectMap = [];
        foreach ($projects as $proj) {
            $existing = $db->table('projects')->where('project_code', $proj['project_code'])->get()->getRowArray();
            if (!$existing) {
                $proj['created_at'] = date('Y-m-d H:i:s');
                $proj['updated_at'] = date('Y-m-d H:i:s');
                $db->table('projects')->insert($proj);
                $projectMap[$proj['project_code']] = $db->insertID();
            } else {
                $projectMap[$proj['project_code']] = $existing['id'];
            }
        }

        // 5. Project Towers
        $towers = [
            // Skyline Horizon
            [
                'project_id'       => $projectMap['PRJ-MUM-SKY01'],
                'tower_name'       => 'Tower Alpha (Sea View)',
                'tower_code'       => 'TWR-A',
                'number_of_floors' => 24,
                'total_units'      => 48,
                'status'           => 'active',
            ],
            [
                'project_id'       => $projectMap['PRJ-MUM-SKY01'],
                'tower_name'       => 'Tower Beta (Club View)',
                'tower_code'       => 'TWR-B',
                'number_of_floors' => 20,
                'total_units'      => 40,
                'status'           => 'active',
            ],
            // Apex Business Towers
            [
                'project_id'       => $projectMap['PRJ-PUN-APX02'],
                'tower_name'       => 'Wing 1 (Executive Suites)',
                'tower_code'       => 'WNG-1',
                'number_of_floors' => 12,
                'total_units'      => 24,
                'status'           => 'active',
            ],
            [
                'project_id'       => $projectMap['PRJ-PUN-APX02'],
                'tower_name'       => 'Wing 2 (Retail Plaza)',
                'tower_code'       => 'WNG-2',
                'number_of_floors' => 6,
                'total_units'      => 18,
                'status'           => 'active',
            ],
        ];

        $towerMap = [];
        foreach ($towers as $twr) {
            $existing = $db->table('project_towers')->where(['project_id' => $twr['project_id'], 'tower_code' => $twr['tower_code']])->get()->getRowArray();
            if (!$existing) {
                $twr['created_at'] = date('Y-m-d H:i:s');
                $twr['updated_at'] = date('Y-m-d H:i:s');
                $db->table('project_towers')->insert($twr);
                $towerMap[$twr['tower_code']] = $db->insertID();
            } else {
                $towerMap[$twr['tower_code']] = $existing['id'];
            }
        }

        // Get Property Types IDs
        $apartmentType = $db->table('property_types')->where('slug', 'apartment')->get()->getRowArray();
        $officeType    = $db->table('property_types')->where('slug', 'office')->get()->getRowArray();
        $villaType     = $db->table('property_types')->where('slug', 'villa')->get()->getRowArray();
        $shopType      = $db->table('property_types')->where('slug', 'shop')->get()->getRowArray();

        // 6. Properties
        $properties = [
            [
                'property_code'           => 'PROP-MUM-001',
                'title'                   => 'Skyline Luxury 3 BHK Sea-View Residence',
                'description'             => 'Exquisite 3 BHK high-floor apartment overlooking the Arabian Sea, featuring Italian marble flooring, wrap-around balcony, and smart home automation.',
                'property_type_id'        => $apartmentType['id'] ?? 1,
                'project_id'              => $projectMap['PRJ-MUM-SKY01'],
                'location_id'             => $locationMap['Mumbai'] ?? 1,
                'owner_name_or_reference' => 'Skyline Developers Primary Listing',
                'ownership_details'       => 'Freehold Title, Clear RERA Registration No. P51800049281',
                'area'                    => 1850.00,
                'price'                   => 45000000.00, // 4.5 Cr
                'status'                  => PropertyStatus::AVAILABLE,
            ],
            [
                'property_code'           => 'PROP-MUM-002',
                'title'                   => 'Skyline Grand Penthouse Suite',
                'description'             => 'Spectacular 5 BHK duplex penthouse with private terrace jacuzzi, personal elevator access, and 360-degree panorama.',
                'property_type_id'        => $apartmentType['id'] ?? 1,
                'project_id'              => $projectMap['PRJ-MUM-SKY01'],
                'location_id'             => $locationMap['Mumbai'] ?? 1,
                'owner_name_or_reference' => 'Ananya Singhania (Private Owner)',
                'ownership_details'       => 'Direct Owner Resale, Clear Society Conveyance',
                'area'                    => 4200.00,
                'price'                   => 125000000.00, // 12.5 Cr
                'status'                  => PropertyStatus::UNDER_NEGOTIATION,
            ],
            [
                'property_code'           => 'PROP-PUN-001',
                'title'                   => 'Apex Grade-A Corporate Office Suite',
                'description'             => 'Plug-and-play furnished corporate office space on 7th floor with 40 workstations, 2 conference rooms, and server room.',
                'property_type_id'        => $officeType['id'] ?? 2,
                'project_id'              => $projectMap['PRJ-PUN-APX02'],
                'location_id'             => $locationMap['Pune'] ?? 2,
                'owner_name_or_reference' => 'Apex Commercials Institutional Inventory',
                'ownership_details'       => 'Commercial Freehold with Occupancy Certificate (OC)',
                'area'                    => 3500.00,
                'price'                   => 38500000.00, // 3.85 Cr
                'status'                  => PropertyStatus::AVAILABLE,
            ],
            [
                'property_code'           => 'PROP-BLR-001',
                'title'                   => 'Green Meadows 4 BHK Signature Eco Villa',
                'description'             => 'Contemporary modern villa with private swimming plunge pool, solar power generation, and manicured landscaped lawn in Whitefield.',
                'property_type_id'        => $villaType['id'] ?? 7,
                'project_id'              => $projectMap['PRJ-BLR-GRN03'],
                'location_id'             => $locationMap['Bengaluru'] ?? 3,
                'owner_name_or_reference' => 'Meadows Master Joint Venture',
                'ownership_details'       => 'A-Khata Freehold Independent Land Title',
                'area'                    => 3800.00,
                'price'                   => 29500000.00, // 2.95 Cr
                'status'                  => PropertyStatus::RESERVED,
            ],
        ];

        $propertyMap = [];
        foreach ($properties as $prop) {
            $existing = $db->table('properties')->where('property_code', $prop['property_code'])->get()->getRowArray();
            if (!$existing) {
                $prop['created_at'] = date('Y-m-d H:i:s');
                $prop['updated_at'] = date('Y-m-d H:i:s');
                $db->table('properties')->insert($prop);
                $propertyMap[$prop['property_code']] = $db->insertID();
            } else {
                $propertyMap[$prop['property_code']] = $existing['id'];
            }
        }

        // 7. Property Amenities Mapping
        $allAmenities = $db->table('amenities')->get()->getResultArray();
        foreach ($propertyMap as $propId) {
            foreach ($allAmenities as $idx => $am) {
                // Link primary amenities to properties
                if ($idx < 6) {
                    $exists = $db->table('property_amenities')->where(['property_id' => $propId, 'amenity_id' => $am['id']])->countAllResults();
                    if ($exists === 0) {
                        $db->table('property_amenities')->insert([
                            'property_id' => $propId,
                            'amenity_id'  => $am['id'],
                            'created_at'  => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }

        // 8. Property Units (Diverse units with realistic inventory status)
        $units = [
            // Units in Skyline Horizon Tower A
            [
                'property_id'         => $propertyMap['PROP-MUM-001'] ?? null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-A'] ?? null,
                'unit_number'         => 'A-1201',
                'floor'               => 12,
                'flat_type'           => '3 BHK',
                'carpet_area'         => 1450.00,
                'built_up_area'       => 1850.00,
                'balcony'             => 2,
                'parking'             => 2,
                'facing'              => 'West (Sea View)',
                'unit_price'          => 45000000.00,
                'availability_status' => PropertyStatus::AVAILABLE,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-A'] ?? null,
                'unit_number'         => 'A-1202',
                'floor'               => 12,
                'flat_type'           => '3 BHK',
                'carpet_area'         => 1450.00,
                'built_up_area'       => 1850.00,
                'balcony'             => 2,
                'parking'             => 2,
                'facing'              => 'East (City View)',
                'unit_price'          => 43500000.00,
                'availability_status' => PropertyStatus::BOOKED,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-A'] ?? null,
                'unit_number'         => 'A-1401',
                'floor'               => 14,
                'flat_type'           => '4 BHK',
                'carpet_area'         => 2100.00,
                'built_up_area'       => 2600.00,
                'balcony'             => 3,
                'parking'             => 3,
                'facing'              => 'West (Sea View)',
                'unit_price'          => 65000000.00,
                'availability_status' => PropertyStatus::RESERVED,
            ],
            [
                'property_id'         => $propertyMap['PROP-MUM-002'] ?? null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-A'] ?? null,
                'unit_number'         => 'A-PH-2401',
                'floor'               => 24,
                'flat_type'           => 'Penthouse',
                'carpet_area'         => 3400.00,
                'built_up_area'       => 4200.00,
                'balcony'             => 4,
                'parking'             => 4,
                'facing'              => '360 Panorama',
                'unit_price'          => 125000000.00,
                'availability_status' => PropertyStatus::UNDER_NEGOTIATION,
            ],

            // Units in Skyline Horizon Tower B
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-B'] ?? null,
                'unit_number'         => 'B-0401',
                'floor'               => 4,
                'flat_type'           => '2 BHK',
                'carpet_area'         => 850.00,
                'built_up_area'       => 1150.00,
                'balcony'             => 1,
                'parking'             => 1,
                'facing'              => 'North',
                'unit_price'          => 28500000.00,
                'availability_status' => PropertyStatus::AVAILABLE,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-B'] ?? null,
                'unit_number'         => 'B-0402',
                'floor'               => 4,
                'flat_type'           => '2 BHK',
                'carpet_area'         => 850.00,
                'built_up_area'       => 1150.00,
                'balcony'             => 1,
                'parking'             => 1,
                'facing'              => 'South',
                'unit_price'          => 28500000.00,
                'availability_status' => PropertyStatus::SOLD,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-MUM-SKY01'],
                'tower_id'            => $towerMap['TWR-B'] ?? null,
                'unit_number'         => 'B-0801',
                'floor'               => 8,
                'flat_type'           => '3 BHK',
                'carpet_area'         => 1350.00,
                'built_up_area'       => 1700.00,
                'balcony'             => 2,
                'parking'             => 2,
                'facing'              => 'East',
                'unit_price'          => 39000000.00,
                'availability_status' => PropertyStatus::RENTED,
            ],

            // Units in Apex Business Towers
            [
                'property_id'         => $propertyMap['PROP-PUN-001'] ?? null,
                'project_id'          => $projectMap['PRJ-PUN-APX02'],
                'tower_id'            => $towerMap['WNG-1'] ?? null,
                'unit_number'         => 'W1-701',
                'floor'               => 7,
                'flat_type'           => 'Office Suite',
                'carpet_area'         => 2800.00,
                'built_up_area'       => 3500.00,
                'balcony'             => 0,
                'parking'             => 4,
                'facing'              => 'North-East',
                'unit_price'          => 38500000.00,
                'availability_status' => PropertyStatus::AVAILABLE,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-PUN-APX02'],
                'tower_id'            => $towerMap['WNG-1'] ?? null,
                'unit_number'         => 'W1-702',
                'floor'               => 7,
                'flat_type'           => 'Office Suite',
                'carpet_area'         => 2400.00,
                'built_up_area'       => 3000.00,
                'balcony'             => 0,
                'parking'             => 3,
                'facing'              => 'South-West',
                'unit_price'          => 33000000.00,
                'availability_status' => PropertyStatus::AVAILABLE,
            ],
            [
                'property_id'         => null,
                'project_id'          => $projectMap['PRJ-PUN-APX02'],
                'tower_id'            => $towerMap['WNG-2'] ?? null,
                'unit_number'         => 'W2-G01',
                'floor'               => 0,
                'flat_type'           => 'Retail Shop',
                'carpet_area'         => 1200.00,
                'built_up_area'       => 1500.00,
                'balcony'             => 0,
                'parking'             => 2,
                'facing'              => 'Main Road Frontage',
                'unit_price'          => 22500000.00,
                'availability_status' => PropertyStatus::BOOKED,
            ],

            // Green Meadows Villas
            [
                'property_id'         => $propertyMap['PROP-BLR-001'] ?? null,
                'project_id'          => $projectMap['PRJ-BLR-GRN03'],
                'tower_id'            => null,
                'unit_number'         => 'VILLA-07',
                'floor'               => 1,
                'flat_type'           => '4 BHK Villa',
                'carpet_area'         => 3100.00,
                'built_up_area'       => 3800.00,
                'balcony'             => 3,
                'parking'             => 2,
                'facing'              => 'East (Vastu Compliant)',
                'unit_price'          => 29500000.00,
                'availability_status' => PropertyStatus::RESERVED,
            ],
        ];

        foreach ($units as $u) {
            $existing = $db->table('property_units')->where(['project_id' => $u['project_id'], 'unit_number' => $u['unit_number']])->get()->getRowArray();
            if (!$existing) {
                $u['created_at'] = date('Y-m-d H:i:s');
                $u['updated_at'] = date('Y-m-d H:i:s');
                $db->table('property_units')->insert($u);
            }
        }

        // 9. Synchronize dynamic unit counts on all projects
        $projectModel = new \App\Models\ProjectModel();
        foreach ($projectMap as $pId) {
            $projectModel->syncUnitCounts($pId);
        }

        // 10. Initial Pricing Records
        $prop1Id = $propertyMap['PROP-MUM-001'] ?? null;
        if ($prop1Id) {
            $existingPricing = $db->table('property_pricing')->where('property_id', $prop1Id)->get()->getRowArray();
            if (!$existingPricing) {
                $db->table('property_pricing')->insert([
                    'property_id'      => $prop1Id,
                    'unit_id'          => null,
                    'base_price'       => 45000000.00,
                    'price_per_sqft'   => round(45000000.00 / 1850.00, 2),
                    'market_price'     => 47500000.00,
                    'negotiated_price' => 44000000.00,
                    'discount'         => 1000000.00,
                    'effective_from'   => date('Y-m-01'),
                    'remarks'          => 'Pre-launch baseline valuation with 10L launch rebate',
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // 11. Initial Status History
        if ($prop1Id) {
            $existingHistory = $db->table('property_status_history')->where('property_id', $prop1Id)->get()->getRowArray();
            if (!$existingHistory) {
                $db->table('property_status_history')->insert([
                    'property_id' => $prop1Id,
                    'unit_id'     => null,
                    'old_status'  => 'Under Negotiation',
                    'new_status'  => PropertyStatus::AVAILABLE,
                    'changed_by'  => 1, // Super Admin
                    'remarks'     => 'Initial inventory listing registered into system as Available',
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
