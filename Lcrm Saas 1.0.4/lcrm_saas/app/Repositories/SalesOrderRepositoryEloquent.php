<?php namespace App\Repositories;

use App\Models\Saleorder;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class SalesOrderRepositoryEloquent extends BaseRepository implements SalesOrderRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Saleorder::class;
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
        $org = $this->userRepository->getOrganization()->load('salesOrders.companies','salesOrders.salesTeam');
        $salesorders = $org->salesOrders->where('is_delete_list', 0)->where('is_invoice_list', 0);
        return $salesorders;
    }

    public function withAll()
    {
        $this->generateParams();
        $salesorders = $this->userRepository->getOrganization()->salesOrders()->get();
        return $salesorders;
    }

    public function onlySalesorderInvoiceLists(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('salesOrders.companies','salesOrders.salesTeam');
        $salesorders = $org->salesOrders->where('is_invoice_list', 1);
        return $salesorders;
    }

    public function salesorderDeleteList(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('salesOrders.companies','salesOrders.salesTeam');
        $salesorders = $org->salesOrders->where('is_delete_list', 1);
        return $salesorders;
    }

    public function draftedSalesorder(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('salesOrders.companies','salesOrders.salesTeam');
        $salesorders = $org->salesOrders->where('is_delete_list', 0)
            ->where('status','=',trans('sales_order.draft_salesorder'));
        return $salesorders;
    }

    public function createSalesOrder(array $data){
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id']= $user->id;
        $data['organization_id']= $organization->id;

        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $salesorders = $this->create($team);
        $list =[];

        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $salesorders->salesOrderProducts()->attach($list);
    }

    public function updateSalesOrder(array $data,$saleorder_id){
        $this->generateParams();
        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $salesorders = $this->update($team,$saleorder_id);
        $list =[];

        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $salesorders->salesOrderProducts()->sync($list);
    }

    public function getAllForCustomer($company_id)
    {
        $this->generateParams();
        $salesorders = $this->userRepository->getOrganization()->salesOrders()->where([
            ['is_delete_list','=',0],
            ['is_invoice_list','=',0],
            ['status','!=',trans('sales_order.draft_salesorder')],
            ['company_id','=', $company_id]
        ]);
        return $salesorders;
    }

    public function getMonthYear($monthno, $year)
    {
        $this->generateParams();
        $salesorders = $this->userRepository->getOrganization()->salesOrders()->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();
        return $salesorders;
    }
}
