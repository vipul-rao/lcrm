<?php

namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface QuotationRepository extends RepositoryInterface
{
    public function getAll();

    public function withAll();

    public function createQuotation(array $data);

    public function updateQuotation(array $data,$quotation_id);

    public function quotationDeleteList();

    public function quotationSalesOrderList();

    public function onlyQuotationInvoiceLists();

    public function draftedQuotation();

    public function getAllForCustomer($company_id);

    public function getMonth($created_at);

    public function getQuotationsForCustomerByMonthYear($year, $monthno,$company_id);

    public function getMonthYear($monthno, $year);

}