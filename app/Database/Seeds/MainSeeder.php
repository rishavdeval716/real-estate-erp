<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('RolePermissionSeeder');
        $this->call('CompanyBranchSeeder');
        $this->call('UserSeeder');
        $this->call('Phase2PermissionSeeder');
        $this->call('Phase2PropertySeeder');
        $this->call('Phase3PermissionSeeder');
        $this->call('Phase3CrmSeeder');
        $this->call('Phase4PermissionSeeder');
        $this->call('Phase4SalesSeeder');
        $this->call('Phase5PermissionSeeder');
        $this->call('Phase5OperationsSeeder');
        $this->call('Phase6PermissionSeeder');
        $this->call('Phase6ConstructionSeeder');
        $this->call('Phase7PermissionSeeder');
        $this->call('Phase7CompletenessSeeder');
    }
}
