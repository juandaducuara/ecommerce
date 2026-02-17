<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingRatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'zone_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'min_weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'default'    => '0.00',
            ],
            'max_weight' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'min_order_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'max_order_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'estimated_days_min' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'estimated_days_max' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('zone_id');

        $this->forge->addForeignKey('zone_id', 'shipping_zones', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('shipping_rates');
    }

    public function down()
    {
        $this->forge->dropTable('shipping_rates');
    }
}
