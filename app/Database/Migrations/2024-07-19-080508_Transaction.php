<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transaction extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'int',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'account_id' => [
                'type'           => 'int',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'tanggal_setor' => [
                'type' => 'date',
                'null' => true
            ],
            'setor' => [
                'type' => 'float',
                'null' => true
            ],
            'tanggal_penarikan' => [
                'type'           => 'date',
                'null' => true 
            ],
            'penarikan' => [
                'type'           => 'float',
                'null'       => true,
            ],
            'saldo' => [
                'type'           => 'float',
                'default' => 0
            ]
        ];

        $this->forge->addField($fields)
        ->addPrimaryKey('id')
        ->addForeignKey('account_id', 'accounts', 'id');

        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable("transactions");
    }
}
