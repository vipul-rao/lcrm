<?php namespace
App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface SalesOrderRepository extends RepositoryInterface
{
    public function getAll();

    public function withAll();

    public function onlySalesorderInvoiceLists();

    public function salesorderDeleteList();

    public function draftedSalesorder();

    public function createSalesOrder(array $data);

    public function updateSalesOrder(array $data,$saleorder_id);

    public function getAllForCustomer($company_id);

    public function getMonthYear($monthno, $year);
}