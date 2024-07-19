<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run()
    {
        $query1 = $this->db->table("customers")->get();
        $query2 = $this->db->table("deposito_types")->get();
        $customers = $query1->getResultObject();
        $deposito_types = $query2->getResultObject();

        foreach ($customers as $key => $customer) {
            $type = $deposito_types[rand(1, count($deposito_types))-1];
            $this->db->table("accounts")->insert(
                ['customer_id' => $customer->id,
                'balance' => 1_000_000 * rand(1, 7),
                'deposito_type_id' => $type->id]
            );
        }
        
    }
}
