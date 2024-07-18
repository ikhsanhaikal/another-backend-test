<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Customer as ModelsCustomer;
use CodeIgniter\HTTP\ResponseInterface;

class Customer extends BaseController
{

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
