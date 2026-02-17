<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockTable extends Migration
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
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'reserved' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'low_stock_threshold' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
            ],
            'allow_backorder' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'track_inventory' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('product_id');

        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('stock');
    }

    public function down()
    {
        $this->forge->dropTable('stock');
    }
}
