<?php

namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface InvoiceRepository extends RepositoryInterface
{
    public function getAll();

    public function withAll();

    public function createInvoice(array $data);

    public function updateInvoice(array $data,$invoice_id);

    public function invoiceDeleteList();

    public function paidInvoice();

    public function getAllOpen();

    public function getAllOverdue();

    public function getAllPaid();

    public function getAllForCustomer($company_id);

    public function getAllOpenForCustomer($company_id);

    public function getAllOverdueForCustomer($company_id);

    public function getAllPaidForCustomer($company_id);

    public function getMonth($created_at);

    public function getMonthYear($monthno,$year);

    public function getInvoicesForCustomerByMonthYear($year,$monthno,$company_id);

}