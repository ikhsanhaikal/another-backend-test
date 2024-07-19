<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Customer as ModelsCustomer;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class Customer extends BaseController
{
    public function playground() 
    {
       $db = db_connect();
       try {
       $db->transException(true)->transStart();

       $total = $db->table("customers")->countAll(); 
       $result = $db->table("customers")->get();
       $acc = $db->table("accounts")->get();
       $customer = $result->getResultObject();
       $accounts = $acc->getResultObject();


       $accountWithIdCustomer1 = $db->table('accounts')->getWhere(['id' => 1]);

    //    var_dump($accountWithIdCustomer1->getRowObject());

    //    var_dump($accounts);
    //    echo "balance {$accounts[0]->balance }";

    //    $date = new DateTime();
    //    $formattedDate = $date->format('Y-m-d H:i:s');


     $result = $db->table("accounts")
     ->where(['accounts.id' => 1])
     ->join('deposito_types', 'deposito_types.id = accounts.deposito_type_id')
     ->get();
     

     var_dump("\n\ndeposito_types\n\n");

    var_dump($result->getRowObject());

    //    $data = [
    //     "account_id" => 1,
    //     "tanggal_setor" => $formattedDate,
    //     "tanggal_penarikan" => null,
    //     "setor" => 2_000_000,
    //     "penarikan" => null,
    //     "saldo" => 4_00_000,
    //    ];

    //    $db->table("transactions")->insert($data);

       $db->transComplete();
       } catch (DatabaseException $e) {
           var_dump($e);
       }

    //    echo $customer[0]->name;

       return $this->response->setJSON([
        'total' => $total,
       ]);
    }

    public function index()
    {
        $customerModel = model(ModelsCustomer::class);
        $result = $customerModel->findAll(limit: 5);
        return $this->response->setJSON(["customer" => $result]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $rules = [
            "name" => "required|max_length[255]"
        ];

        if (! $this->validateData($data, rules: $rules)) {
           return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        $validData = $this->validator->getValidated();

        $customerModel = model(ModelsCustomer::class);

        try {
            $insertedId = $customerModel->insert(row: $validData);
            $customer = $customerModel->find(id: $insertedId);
            return $this->response->setJSON(['customerId' => $customer]);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500);
        }
    }

    public function show($id) {
        $customerModel = model(ModelsCustomer::class);
        try {
            $customer = $customerModel->find(id: $id);
            return $this->response->setJSON(["customer" => $customer]);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500);
        }
    }

    public function update($id) {
        $customerModel = model(ModelsCustomer::class);

        $data = $this->request->getJSON(true);
        $rules = ["name" => "max_length[255]"];

        if (!$this->validateData($data, rules: $rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $validData = $this->validator->getValidated();

        try {
            $customerModel->update(id: $id, row: $validData);
            $customer = $customerModel->find(id: $id);
            return $this->response->setJSON(["customer" => $customer]);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500);
        }
    }

    public function delete() 
    {
        $data = $this->request->getJSON(true);
        
        if (! $this->validateData($data, ["id" => "required|numeric"] )) {
          return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST); 
        }

        $validData = $this->validator->getValidated();
        $customerModel = model(ModelsCustomer::class);

        try {
           $customerModel->delete(id: $validData);
           return $this->response->setStatusCode(ResponseInterface::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
           return  $this->response->setStatusCode(500);
        }
        
    }
}
