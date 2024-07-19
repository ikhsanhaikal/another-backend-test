<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAccount extends Migration
{
    public function up()
    {
        $fields = [
            'deposito_type_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
        ];

        $this->forge->addColumn("accounts", $fields);
        $this->forge->addForeignKey("deposito_type_id", "deposito_types", "id");
    }

    public function down()
    {
        $this->forge->dropForeignKey("accounts", "accounts_customer_id_foreign");
        $this->forge->dropColumn("accounts", "deposito_type_id");
    }
}
