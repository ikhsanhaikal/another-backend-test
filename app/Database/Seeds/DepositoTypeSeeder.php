<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepositoTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ["name" => "bronze", "yearly" => 0.03],
            ["name" => "silver", "yearly" => 0.05],
            ["name" => "gold", "yearly" => 0.07],
        ];

        $this->db->table("deposito_types")->insertBatch($data);
    }
}
