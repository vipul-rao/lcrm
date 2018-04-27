<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\OpportunityRepository;
use Yajra\Datatables\Datatables;
use App\Repositories\QuotationRepository;

class OpportunityConvertedListController extends Controller
{
    private $quotationRepository;

    private $opportunityRepository;

    public function __construct(OpportunityRepository $opportunityRepository,
                                QuotationRepository $quotationRepository)
    {
        parent::__construct();

        $this->opportunityRepository = $opportunityRepository;
        $this->quotationRepository = $quotationRepository;

        view()->share('type', 'opportunity_converted_list');
    }

    public function index()
    {

        $title = trans('opportunity.converted_list');
        return view('user.opportunity.converted_list',compact('title'));
    }


    public function data(Datatables $datatables)
    {
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $convertedList = $this->opportunityRepository->getConverted()
            ->map(function ($convertedList) use ($orgRole,$dateFormat){
                return [
                    'id' => $convertedList->id,
                    'opportunity' => $convertedList->opportunity,
                    'company' => $convertedList->companies->name ?? null,
                    'customer' => $convertedList->customer->full_name ?? null,
                    'next_action' => date($dateFormat,strtotime($convertedList->next_action)),
                    'stages' => $convertedList->stages,
                    'expected_revenue' => $convertedList->expected_revenue,
                    'probability' => $convertedList->probability,
                    'salesteam' => $convertedList->salesTeam->salesteam ?? null,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($convertedList)
            ->addColumn('actions', '
                                            @if(Sentinel::getUser()->hasAccess([\'quotations.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'convertedlist_view/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    public function quatationList($id)
    {
        $quotation_id = $this->quotationRepository->getAll()->where('opportunity_id',$id)->first();
        if(isset($quotation_id)){
            return redirect('quotation/' . $quotation_id->id . '/show');
        }else{
            return redirect('opportunity_converted_list')->withErrors(trans('opportunity.converted_salesorder'));
        }
    }

}
