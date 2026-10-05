<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Phase2PermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $phase2Permissions = [
            // Property Types
            ['name' => 'View Property Types',      'slug' => 'property_types.view',      'group_name' => 'Property Types',   'description' => 'View property types listing and details'],
            ['name' => 'Create Property Type',     'slug' => 'property_types.create',    'group_name' => 'Property Types',   'description' => 'Create new property types'],
            ['name' => 'Edit Property Type',       'slug' => 'property_types.edit',      'group_name' => 'Property Types',   'description' => 'Edit existing property types and status'],
            ['name' => 'Delete Property Type',     'slug' => 'property_types.delete',    'group_name' => 'Property Types',   'description' => 'Delete or archive property types'],

            // Locations
            ['name' => 'View Locations',           'slug' => 'locations.view',           'group_name' => 'Locations',        'description' => 'View locations and geographic directories'],
            ['name' => 'Create Location',          'slug' => 'locations.create',         'group_name' => 'Locations',        'description' => 'Add new states, cities, areas and landmarks'],
            ['name' => 'Edit Location',            'slug' => 'locations.edit',           'group_name' => 'Locations',        'description' => 'Update geographic and locality details'],
            ['name' => 'Delete Location',          'slug' => 'locations.delete',         'group_name' => 'Locations',        'description' => 'Delete unused locations'],

            // Amenities
            ['name' => 'View Amenities',           'slug' => 'amenities.view',           'group_name' => 'Amenities',        'description' => 'View property amenities list'],
            ['name' => 'Create Amenity',           'slug' => 'amenities.create',         'group_name' => 'Amenities',        'description' => 'Create new amenities'],
            ['name' => 'Edit Amenity',             'slug' => 'amenities.edit',           'group_name' => 'Amenities',        'description' => 'Edit amenities and status'],
            ['name' => 'Delete Amenity',           'slug' => 'amenities.delete',         'group_name' => 'Amenities',        'description' => 'Delete or deactivate amenities'],

            // Projects
            ['name' => 'View Projects',            'slug' => 'projects.view',            'group_name' => 'Projects',         'description' => 'View real estate projects and master details'],
            ['name' => 'Create Project',           'slug' => 'projects.create',          'group_name' => 'Projects',         'description' => 'Create new real estate projects'],
            ['name' => 'Edit Project',             'slug' => 'projects.edit',            'group_name' => 'Projects',         'description' => 'Update projects, construction status and towers'],
            ['name' => 'Delete Project',           'slug' => 'projects.delete',          'group_name' => 'Projects',         'description' => 'Soft delete real estate projects'],

            // Properties
            ['name' => 'View Properties',          'slug' => 'properties.view',          'group_name' => 'Properties',       'description' => 'View master properties and listings'],
            ['name' => 'Create Property',          'slug' => 'properties.create',        'group_name' => 'Properties',       'description' => 'Create new properties and inventory records'],
            ['name' => 'Edit Property',            'slug' => 'properties.edit',          'group_name' => 'Properties',       'description' => 'Edit properties, ownership details and pricing'],
            ['name' => 'Delete Property',          'slug' => 'properties.delete',        'group_name' => 'Properties',       'description' => 'Delete or deactivate properties'],

            // Property Units
            ['name' => 'View Property Units',      'slug' => 'units.view',               'group_name' => 'Units',            'description' => 'View individual flats, floors and units'],
            ['name' => 'Create Property Unit',     'slug' => 'units.create',             'group_name' => 'Units',            'description' => 'Add new property units to towers/projects'],
            ['name' => 'Edit Property Unit',       'slug' => 'units.edit',               'group_name' => 'Units',            'description' => 'Update unit specifications and floor plans'],
            ['name' => 'Delete Property Unit',     'slug' => 'units.delete',             'group_name' => 'Units',            'description' => 'Delete or archive units'],

            // Media & Documents
            ['name' => 'View Property Media',      'slug' => 'media.view',               'group_name' => 'Media',            'description' => 'View photos, floor plans and brochures'],
            ['name' => 'Upload Property Media',    'slug' => 'media.create',             'group_name' => 'Media',            'description' => 'Upload and assign photos and documents'],
            ['name' => 'Delete Property Media',    'slug' => 'media.delete',             'group_name' => 'Media',            'description' => 'Delete media assets and documents'],

            // Availability
            ['name' => 'View Availability',        'slug' => 'availability.view',        'group_name' => 'Availability',     'description' => 'View property and unit availability status and history'],
            ['name' => 'Change Availability',      'slug' => 'availability.edit',        'group_name' => 'Availability',     'description' => 'Update availability statuses with audit logs'],

            // Pricing & Valuation
            ['name' => 'View Pricing',             'slug' => 'pricing.view',             'group_name' => 'Pricing',          'description' => 'View property pricing, base rates and valuation history'],
            ['name' => 'Create Pricing',           'slug' => 'pricing.create',           'group_name' => 'Pricing',          'description' => 'Record new pricing schedules and revisions'],
            ['name' => 'Edit Pricing',             'slug' => 'pricing.edit',             'group_name' => 'Pricing',          'description' => 'Edit current property pricing'],
            ['name' => 'Delete Pricing',           'slug' => 'pricing.delete',           'group_name' => 'Pricing',          'description' => 'Remove historical pricing entries'],

            // Unit Inventory
            ['name' => 'View Unit Inventory',      'slug' => 'inventory.view',           'group_name' => 'Inventory',        'description' => 'View dynamic floor-wise and tower-wise inventory matrix'],
        ];

        foreach ($phase2Permissions as $perm) {
            $existing = $db->table('permissions')->where('slug', $perm['slug'])->get()->getRowArray();
            if (!$existing) {
                $perm['created_at'] = date('Y-m-d H:i:s');
                $perm['updated_at'] = date('Y-m-d H:i:s');
                $db->table('permissions')->insert($perm);
            }
        }

        // Assign all Phase 2 permissions to Admin
        $adminRole = $db->table('roles')->where('name', 'Admin')->get()->getRowArray();
        $managerRole = $db->table('roles')->where('name', 'Manager')->get()->getRowArray();
        $allPhase2Perms = $db->table('permissions')->get()->getResultArray();

        if ($adminRole) {
            foreach ($allPhase2Perms as $p) {
                $exists = $db->table('role_permissions')->where([
                    'role_id'       => $adminRole['id'],
                    'permission_id' => $p['id'],
                ])->countAllResults();

                if ($exists === 0) {
                    $db->table('role_permissions')->insert([
                        'role_id'       => $adminRole['id'],
                        'permission_id' => $p['id'],
                    ]);
                }
            }
        }

        // Assign viewing and managing permissions to Manager
        if ($managerRole) {
            $managerAllowedSlugs = [
                'property_types.view',
                'locations.view',
                'locations.create',
                'locations.edit',
                'amenities.view',
                'projects.view',
                'projects.create',
                'projects.edit',
                'properties.view',
                'properties.create',
                'properties.edit',
                'units.view',
                'units.create',
                'units.edit',
                'media.view',
                'media.create',
                'availability.view',
                'availability.edit',
                'pricing.view',
                'pricing.create',
                'inventory.view',
            ];

            foreach ($allPhase2Perms as $p) {
                if (in_array($p['slug'], $managerAllowedSlugs, true)) {
                    $exists = $db->table('role_permissions')->where([
                        'role_id'       => $managerRole['id'],
                        'permission_id' => $p['id'],
                    ])->countAllResults();

                    if ($exists === 0) {
                        $db->table('role_permissions')->insert([
                            'role_id'       => $managerRole['id'],
                            'permission_id' => $p['id'],
                        ]);
                    }
                }
            }
        }
    }
}
