<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDepositoType extends Migration
{
    public function up()
    {

        $fields = [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'yearly' => [
                'type' => 'FLOAT',
            ],
        ];

        $this->forge->addField($fields)->addPrimaryKey("id"); 
        $this->forge->createTable("deposito_types");
    }

    public function down()
    {
        $this->forge->dropTable("deposito_types");
    }
}
