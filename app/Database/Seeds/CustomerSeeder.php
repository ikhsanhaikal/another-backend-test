<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ["name" => "sarah"],
            ["name" => "marcus"],
            ["name" => "bobby"]
        ];

        $this->db->table("customers")->insertBatch($data);
    }
}
