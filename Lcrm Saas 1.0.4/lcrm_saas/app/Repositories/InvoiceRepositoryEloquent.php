<?php

namespace App\Repositories;

use App\Models\Invoice;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class InvoiceRepositoryEloquent extends BaseRepository implements InvoiceRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Invoice::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams(){
        $this->userRepository = new UserRepositoryEloquent(app());
    }

    public function getAll()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('invoices.companies','invoices.receivePayment');
        $invoices = $org->invoices->where('is_delete_list', 0)->where('status','!=',trans('invoice.paid_invoice'));
        return $invoices;
    }

    public function withAll()
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->get();
        return $invoices;
    }

    public function createInvoice(array $data){
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id']= $user->id;
        $data['organization_id']= $organization->id;

        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $invoices = $this->create($team);
        $list =[];

        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $invoices->invoiceProducts()->attach($list);
    }

    public function updateInvoice(array $data,$invoice_id){

        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $invoices = $this->update($team,$invoice_id);
        $list =[];
        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }
        $invoices->invoiceProducts()->sync($list);
    }
    public function invoiceDeleteList(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('invoices.companies','invoices.receivePayment');
        $invoices = $org->invoices->where('is_delete_list', 1);
        return $invoices;
    }

    public function paidInvoice(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('invoices.companies','invoices.receivePayment');
        $invoices = $org->invoices->where('is_delete_list', 0)->where('status','=',trans('invoice.paid_invoice'));
        return $invoices;
    }
    public function getAllOpen()
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['status','=',trans('invoice.open_invoice')]
        ])->get();
        return $invoices;
    }

    public function getAllOverdue()
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['status','=',trans('invoice.overdue_invoice')]
        ])->get();
        return $invoices;
    }

    public function getAllPaid()
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['status','=',trans('invoice.paid_invoice')]
        ])->get();
        return $invoices;
    }

    public function getAllForCustomer($company_id)
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['company_id','=', $company_id],
            ['status','!=',trans('invoice.paid_invoice')]
        ])->get();
        return $invoices;
    }
    public function getAllOpenForCustomer($company_id)
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['company_id','=', $company_id],
            ['status','=',trans('invoice.open_invoice')]
        ])->get();
        return $invoices;
    }

    public function getAllOverdueForCustomer($company_id)
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['company_id','=', $company_id],
            ['status','=',trans('invoice.overdue_invoice')]
        ])->get();
        return $invoices;
    }

    public function getAllPaidForCustomer($company_id)
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->where([
            ['is_delete_list','=',0],
            ['company_id','=', $company_id],
            ['status','=',trans('invoice.paid_invoice')]
        ])->get();
        return $invoices;
    }

    public function getMonth($created_at)
    {
        $invoices = $this->model->whereMonth('created_at', $created_at)->get();
        return $invoices;
    }

    public function getInvoicesForCustomerByMonthYear($year,$monthno,$company_id)
    {
        $invoices = $this->model->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->where([
            ['company_id','=', $company_id]
        ])->get();
        return $invoices;
    }

    public function getMonthYear($monthno,$year)
    {
        $this->generateParams();
        $invoices = $this->userRepository->getOrganization()->invoices()->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();
        return $invoices;
    }
}
