<?php namespace App\Repositories;


interface InvoicePaymentRepository
{
    public function getAll();

    public function createPayment(array $data);

    public function getAllPaidForCustomer($company_id);
}