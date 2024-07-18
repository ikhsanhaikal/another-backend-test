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
            'account_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ]
        ];

        $this->forge->addField($fields)->addPrimaryKey("id")
        ->addForeignKey('account_id', 'accounts', 'id')->addUniqueKey("account_id");
        $this->forge->createTable("deposito_types");
    }

    public function down()
    {
        $this->forge->dropTable("deposito_types");
    }
}
