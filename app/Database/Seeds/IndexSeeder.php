<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IndexSeeder extends Seeder
{
    public function run()
    {
        $this->call('CustomerSeeder');
        $this->call('DepositoTypeSeeder');
        $this->call('AccountSeeder');
    }
}
