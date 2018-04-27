<?php
namespace App\Repositories;


use App\Models\Quotation;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class QuotationRepositoryEloquent extends BaseRepository implements QuotationRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Quotation::class;
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
        $org = $this->userRepository->getOrganization()->load('quotations.companies','quotations.salesTeam');
        $quotations = $org->quotations->where('is_delete_list',0)->where('is_converted_list',0)->where('is_quotation_invoice_list',0);
        return $quotations;
    }

    public function withAll()
    {
        $this->generateParams();
        $quotations = $this->userRepository->getOrganization()->quotations()->get();
        return $quotations;
    }

    public function createQuotation(array $data){
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id']= $user->id;
        $data['organization_id']= $organization->id;

        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $quotation = $this->create($team);
        $list =[];

        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $quotation->quotationProducts()->attach($list);
    }

    public function updateQuotation(array $data,$quotation_id)
    {
        $this->generateParams();
        $team = collect($data)->except('product_list','taxes','product_id','description','quantity','price','sub_total')->toArray();
        $quotation = $this->update($team,$quotation_id);
        $list =[];

        foreach ($data['product_id'] as $key =>$product){
            if ($product != "") {
                $temp['quantity'] = $data['quantity'][$key];
                $temp['price'] = $data['price'][$key];
                $list[$data['product_id'][$key]] = $temp;
            }
        }

        $quotation->quotationProducts()->sync($list);
    }

    public function quotationDeleteList(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('quotations.companies','quotations.salesTeam');
        $quotations = $org->quotations->where('is_delete_list',1);
        return $quotations;
    }

    public function draftedQuotation(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('quotations.companies','quotations.salesTeam');
        $quotations = $org->quotations->where('is_delete_list',0)
            ->where('status','=',trans('quotation.draft_quotation'));
        return $quotations;
    }

    public function quotationSalesOrderList(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('quotations.companies','quotations.salesTeam');
        $quotations = $org->quotations->where('is_converted_list',1);
        return $quotations;
    }

    public function onlyQuotationInvoiceLists(){
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('quotations.companies','quotations.salesTeam');
        $quotations = $org->quotations->where('is_quotation_invoice_list',1);
        return $quotations;
    }

    public function getAllForCustomer($company_id)
    {
        $this->generateParams();
        $quotations = $this->userRepository->getOrganization()->quotations()->where([
            ['is_delete_list','=',0],
            ['is_converted_list','=',0],
            ['is_quotation_invoice_list','=',0],
            ['status','!=',trans('quotation.draft_quotation')],
            ['company_id','=', $company_id]
        ]);
        return $quotations;
    }

    public function getMonth($created_at)
    {
        $quotations = $this->model->whereMonth('created_at', $created_at)->get();
        return $quotations;
    }

    public function getQuotationsForCustomerByMonthYear($year, $monthno,$company_id)
    {
        $quotations = $this->model->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->where([
            ['is_delete_list','=',0],
            ['is_converted_list','=',0],
            ['is_quotation_invoice_list','=',0],
            ['status','!=',trans('quotation.draft_quotation')],
            ['company_id','=', $company_id]
        ])->get();
        return $quotations;
    }
    public function getMonthYear($monthno, $year)
    {
        $this->generateParams();
        $quotations = $this->userRepository->getOrganization()->quotations()->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();
        return $quotations;
    }
}
