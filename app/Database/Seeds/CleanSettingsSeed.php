<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CleanSettingsSeed extends Seeder
{
    public function run()
    {
        $this->db->query("DELETE FROM settings WHERE setting_key = '' OR setting_key IS NULL");
        $this->db->query("DELETE FROM settings WHERE setting_group = '' OR setting_group IS NULL");
    }
}
