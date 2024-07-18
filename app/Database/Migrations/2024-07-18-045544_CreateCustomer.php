<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;


class CreateCustomer extends Migration
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
            ]
            ];
        $this->forge->addField($fields)->addPrimaryKey("id");
        $this->forge->createTable("customers");
    }

    public function down()
    {
        $this->forge->dropTable("customers");
    }
}
