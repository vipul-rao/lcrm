<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\QuotationRepository;
use Yajra\Datatables\Datatables;

class QuotationDeleteListController extends Controller
{
    private $quotationRepository;
    protected $user;
    public function __construct(QuotationRepository $quotationRepository)
    {
        parent::__construct();
        $this->quotationRepository = $quotationRepository;


        view()->share('type', 'quotation_delete_list');
    }
    public function index()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('quotation.delete_list');
        return view('user.quotation_delete_list.index',compact('title'));
    }

    public function show($quotation)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->quotationDeleteList()->find($quotation);
        if (!$quotation){
            abort(404);
        }
        $title = trans('quotation.show_delete_list');
        $action = trans('action.show');
        return view('user.quotation_delete_list.show', compact('title', 'quotation','action'));
    }

    public function delete($quotation){
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->quotationDeleteList()->find($quotation);
        $title = trans('quotation.restore_delete_list');
        $action = 'delete';
        return view('user.quotation_delete_list.restore', compact('title', 'quotation','action'));
    }

    public function restoreQuotation($quotation)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->quotationDeleteList()->find($quotation);
        $quotation->update(['is_delete_list'=>0]);
        return redirect('quotation');
    }

    public function data(Datatables $datatables)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $quotationDeleteList = $this->quotationRepository->quotationDeleteList()
            ->map(function ($quotationDeleteList) use ($orgRole){
                return [
                    'id' => $quotationDeleteList->id,
                    'quotations_number' => $quotationDeleteList->quotations_number,
                    'company_id' => $quotationDeleteList->companies->name ?? null,
                    'sales_team_id' => $quotationDeleteList->salesTeam->salesteam ?? null,
                    'final_price' => $quotationDeleteList->final_price,
                    'payment_term' => $quotationDeleteList->payment_term,
                    'status' => $quotationDeleteList->status,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($quotationDeleteList)

            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'quotations.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation_delete_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @endif
                                            @if(Sentinel::getUser()->hasAccess([\'quotations.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation_delete_list/\' . $id . \'/restore\' ) }}"  title="{{ trans(\'table.restore\') }}">
                                            <i class="fa fa-fw fa-undo text-success"></i> </a>
                                            @endif
                                       ')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    private function generateParams(){
        $this->user = $this->getUser();
    }
}
