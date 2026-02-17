<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Ejecutar en orden de dependencias
        $this->call('RoleSeeder');
        $this->call('UserSeeder');
        $this->call('CategorySeeder');
        $this->call('ShippingZoneSeeder');
        $this->call('SettingsSeeder');
        $this->call('ProductSeeder');
    }
}
