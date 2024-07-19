<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Account as ModelsAccount;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use ReflectionException;

class Account extends BaseController
{
    public function index()
    {
        $accountModel = model(ModelsAccount::class);
        $result = $accountModel->findAll(limit: 5);
        return $this->response->setJSON(["accounts" => $result]);
    }

    public function create($customerId)
    {
        $data = $this->request->getJSON(true);
        $rules = [
            "name" => "required|max_length[255]",
            "deposito_type_id" => "required|numeric",
            "balance" => "required|decimal",
        ];

        if (! $this->validateData($data, rules: $rules)) {
           return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }
        
        $validData = $this->validator->getValidated();
        $validData['customer_id'] = $customerId;
        // $accountModel = model(ModelsAccount::class);

        $db = db_connect();

        $customer =  $db->table('customers')->where('id', $customerId)->get()->getRowObject();

        if ($customer == null) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
        $db->transException(true)->transBegin();

        // var_dump($validData);
        $db->table('accounts')->insert($validData);
        // var_dump("it works");
        // var_dump("insertID: " . $db->insertID());
        $account = $db->table('accounts')->where('id', $db->insertID())->get()->getRowObject();
        
        if ($db->transStatus() == false) {
            $db->transRollback();
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $db->transCommit();
        }
        // try {
            // $insertedId = $accountModel->insert(row: $validData);
            // $account = $accountModel->find(id: $insertedId);
            return $this->response->setJSON(['account' => $account]);
        // } catch (\Throwable $th) {
            // return $this->response->setStatusCode(500);
        // }
        } catch (DatabaseException $e) {
            var_dump($e->getMessage());
        }

    }

    public function show($accountId) {
        $data = $this->request->getJSON(true);

        // if (!$this->validateData($data, rules: $rules)) {
        //     return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        // }

        $accountModel = model(ModelsAccount::class);

        try {
            $account = $accountModel->find(id: $accountId);
            if ($account == null) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NO_CONTENT);
            }
            return $this->response->setJSON(["account" => $account]);
        } catch (\Throwable $th) {
            return $this->response->setStatusCode(500);
        }
    }

    public function update($accountId) {
        $accountModel = model(ModelsAccount::class);
        var_dump($accountId);

        $data = $this->request->getJSON(true);
        $rules = [
            "name" => "if_exist|max_length[255]",
            "deposito_type_id" => "if_exist|numeric",
            "balance" => "if_exist|float",
        ];

        if (!$this->validateData($data, rules: $rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $validData = $this->validator->getValidated();
        var_dump('validdata');
        var_dump($validData);

        try {
            if (!$accountModel->update(id: $accountId, row: $validData)) {
                var_dump("error");
            } 
            $account = $accountModel->find(id: $accountId);
            return $this->response->setJSON(["account" => $account]);
        } catch (ReflectionException $e) {
            var_dump($e);
            // return $this->response->setStatusCode(500);
        }
    }

    public function delete($accountId) 
    {
        // $data = $this->request->getJSON(true);
        
        // if (! $this->validateData($data, ["id" => "required|numeric"] )) {
        //   return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST); 
        // }

        // $validData = $this->validator->getValidated();
        $accountModel = model(ModelsAccount::class);

        try {
           $accountModel->delete(id: $accountId);
           return $this->response->setStatusCode(ResponseInterface::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
           return  $this->response->setStatusCode(500);
        }
    }

    public function widthdraw($accountId) 
    {
        $data = $this->request->getJSON(true);

        $rules = [
            "withdraw_amount" => "required|decimal",
            "deposit_date" => "required|valid_date",
            "withdraw_date" => "required|valid_date"
        ];

        if (! $this->validateData($data, $rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $validData = $this->validator->getValidated();
        $accountModel = model(ModelsAccount::class);
        $account = $accountModel->find(id: $validData['id']);

        $db = db_connect();

        $now = new DateTime();
        $formattedDate = $now->format('Y-m-d H:i:s');

        $account = $db->table('accounts')->getWhere([
            'id' => $accountId,
        ])->getRowObject();

        if (!$account->balance > $validData['withdraw_amount']) {
            $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $result = $db->table("accounts")
        ->where(['accounts.id' => 1])
        ->join('deposito_types', 'deposito_types.id = accounts.deposito_type_id')
        ->get()
        ->getRowObject();

        $db->transBegin(true);
        
        $saldo = $account->balance - $validData['deposit_amount'];
        $saldo += $saldo * 12 * $result->yearly / 12;

        $db->table('transactions')->insert([
            'account_id' => $accountId,
            'tanggal_setor' => null,
            'setor' => null,
            'tanggal_penarikan' => $formattedDate,
            'penarikan' => $validData['deposit_amount'],
            'saldo' => $saldo,
        ]);

        $db->table("accounts")
        ->where(['id' => $accountId])->update(['balance' => $saldo]);

        $latestAccount = $db->table('accounts')->getWhere([
            'id' => $accountId,
        ])->getRowObject();

        if ($db->transStatus() === false) {
            $db->transRollback();
        } else {
            $db->transCommit();
        }

        $db->close();

        return $this->response->setJSON(["account" => $latestAccount]);
    }

    public function deposit($accountId) 
    {
        $data = $this->request->getJSON(true);

        if (! $this->validateData($data, ["deposit_amount" => "required|decimal"])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $validData = $this->validator->getValidated();

        // return $this->response->setJSON(["data" => $validData, "id" => $accountId]);

        $db = db_connect();

        $now = new DateTime();
        $formattedDate = $now->format('Y-m-d H:i:s');

        // try {
            // $db->transException(true)->transStart(true);
            $db->transBegin();

            $account = $db->table('accounts')->getWhere([
                'id' => $accountId,
            ])->getRowObject();

            
             $db->table('transactions')->insert([
                 'account_id' => $accountId,
                 'tanggal_setor' => $formattedDate,
                 'setor' => $validData['deposit_amount'],
                 'tanggal_penarikan' => null,
                 'penarikan' => null,
                 'saldo' => $account->balance + $validData['deposit_amount'],
             ]);

            $db->table('accounts')->where(['id' => $accountId])->update([
                'balance' => $account->balance + $validData['deposit_amount'],
            ]);

            $afterDeposit = $db->table('accounts')->getWhere([
                'id' => $accountId,
            ])->getRowObject();

            if ($db->transStatus() === false) {
                var_dump("bad things happened somethimes\n");
                $db->transRollback();
            } else {
                $db->transCommit();
            }

            // var_dump($db->transStatus());
            $db->close();
            return $this->response->setJSON(["account" => $afterDeposit]);
    }
}
