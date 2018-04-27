<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpportunityRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;

class OpportunityController extends Controller
{
    /*user site settings*/
    /**
     * @var UserRepository
     */
    public $userRepository;
    /**
     * @var OpportunityRepository
     */
    private $opportunityRepository;
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    private $organizationRepository;

    private $customerRepository;

    private $quotationRepository;

    protected $user;

    /**
     * OpportunityController constructor.
     *
     * @param CompanyRepository     $companyRepository
     * @param UserRepository        $userRepository
     * @param OpportunityRepository $opportunityRepository
     * @param SalesTeamRepository   $salesTeamRepository
     * @param OptionRepository      $optionRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        OpportunityRepository $opportunityRepository,
        SalesTeamRepository $salesTeamRepository,
        OptionRepository $optionRepository,
        OrganizationRepository $organizationRepository,
        CustomerRepository $customerRepository,
        QuotationRepository $quotationRepository
    ) {

        parent::__construct();

        $this->opportunityRepository = $opportunityRepository;
        $this->userRepository = $userRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->optionRepository = $optionRepository;
        $this->companyRepository = $companyRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;
        $this->quotationRepository = $quotationRepository;

        view()->share('type', 'opportunity');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('opportunity.opportunities');

        return view('user.opportunity.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('opportunity.new');
        $calls = 0;
        $meetings = 0;

        return view('user.opportunity.create', compact('title', 'meetings', 'calls'));
    }

    public function store(OpportunityRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id,'is_archived'=>0,'is_delete_list'=>0,'is_converted_list'=>0]);
        $this->opportunityRepository->create($request->all());

        return redirect('opportunity');
    }

    public function edit($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $calls = $opportunity->calls()->count();
        $meetings = $opportunity->meetings()->count();

        $customers_list = $this->customerRepository->getAll()->where('company_id',$opportunity->assigned_partner_id);
        $agent_name=[];
        foreach ($customers_list as $customer){
            $agent_name[$customer->user->id]=$customer->user->full_name;
        }

        $title = trans('opportunity.edit');

        return view('user.opportunity.edit', compact('title', 'calls', 'meetings', 'opportunity','agent_name'));
    }

    public function update(OpportunityRequest $request, $opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $opportunity->update($request->all());

        return redirect('opportunity');
    }

    public function show($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.show');
        $action = trans('action.show');
        return view('user.opportunity.show', compact('title', 'opportunity', 'action'));
    }

    public function won($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.won');
        $action = 'won';

        return view('user.opportunity.lost_won', compact('title', 'opportunity', 'action'));
    }

    public function lost($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.lost');
        $action = 'lost';

        return view('user.opportunity.lost_won', compact('title', 'opportunity', 'action'));
    }

    public function updateLost(Request $request, $opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $request->merge([
            'stages' => 'Lost',
        ]);
        $opportunity->update($request->all());

        return redirect('opportunity');
    }

    public function delete($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('opportunity.delete');

        return view('user.opportunity.delete', compact('title', 'opportunity'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $opportunity->update(['is_delete_list' => 1]);
        return redirect('opportunity_delete_list');
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['opportunities.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $opportunities = $this->opportunityRepository->getAll()
            ->map(function ($opportunity) use ($orgRole,$dateFormat){
                return [
                    'id' => $opportunity->id,
                    'opportunity' => $opportunity->opportunity,
                    'company' => $opportunity->companies->name ?? null,
                    'customer' => $opportunity->customer->full_name ?? null,
                    'next_action' => date($dateFormat,strtotime($opportunity->next_action)),
                    'stages' => $opportunity->stages,
                    'expected_revenue' => $opportunity->expected_revenue,
                    'probability' => $opportunity->probability,
                    'salesteam' => $opportunity->salesTeam->salesteam ?? null,
                    'calls' => $opportunity->calls->count(),
                    'meetings' => $opportunity->meetings->count(),
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($opportunities)
            ->addColumn('options', ' @if(Sentinel::getUser()->hasAccess([\'opportunities.write\']) || $orgRole=="admin")
                                         <a href="{{ url(\'opportunity/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                                <i class="fa fa-fw fa-pencil text-warning "></i></a>
                                                @if(Sentinel::getUser()->hasAccess([\'logged_calls.read\']) || $orgRole=="admin")
                                         <a href="{{ url(\'opportunitycall/\' . $id .\'/\' ) }}" title="{{ trans(\'table.calls\') }}">
                                                <i class="fa fa-phone text-primary"></i><sup>{{ $calls }}</sup> </a>
                                                @endif
                                                @if(Sentinel::getUser()->hasAccess([\'meetings.read\']) || $orgRole=="admin")
                                         <a href="{{ url(\'opportunitymeeting/\' . $id .\'/calendar\' ) }}" title="{{ trans(\'table.meeting\') }}">
                                                <i class="fa fa-fw fa-users text-primary"></i> <sup>{{ $meetings }}</sup></a>
                                                @endif
                                      @endif
                                      @if(Sentinel::getUser()->hasAccess([\'opportunities.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'opportunity/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @endif
                                      @if(Sentinel::getUser()->hasAccess([\'opportunities.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'opportunity/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i></a>
                                      @endif')
            ->addColumn('actions', ' @if(Sentinel::getUser()->hasAccess([\'opportunities.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'opportunity/\' . $id .\'/lost\' ) }}" class="btn btn-danger" title="{{ trans(\'opportunity.lost\') }}">
                                                Lost</a>
                                      @endif
                                       @if(Sentinel::getUser()->hasAccess([\'quotations.write\']) || $orgRole=="admin")
                                       <a href="{{ url(\'opportunity/\' . $id .\'/won\' ) }}" class="btn btn-success m-t-10" title="{{ trans(\'opportunity.won\') }}">
                                                Won</a>
                                       @endif')
            ->removeColumn('id')
            ->removeColumn('calls')
            ->removeColumn('meetings')
            ->rawColumns(['actions','options'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $stages = $this->optionRepository->getAll()
            ->where('category', 'stages')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->prepend(trans('dashboard.select_stage'), '');

        $priority = $this->optionRepository->getAll()
            ->where('category', 'priority')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->toArray();

        $lost_reason = ['' => trans('dashboard.select_lost_reason')] +
            $this->optionRepository->getAll()
                ->where('category', 'lost_reason')
                ->map(function ($title) {
                    return [
                        'text' => $title->title,
                        'id' => $title->value,
                    ];
                })->pluck('text', 'id')->toArray();

        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('dashboard.select_company'),'');

        $salesteams = $this->salesTeamRepository->orderBy('id', 'asc')->getAll()->pluck('salesteam', 'id')->prepend(trans('dashboard.select_sales_team'),'');

        view()->share('salesteams', $salesteams);
        view()->share('stages', $stages);
        view()->share('priority', $priority);
        view()->share('lost_reason', $lost_reason);
        view()->share('companies', $companies);
    }

    public function convertToQuotation($opportunity)
    {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }

        $quotation = $this->quotationRepository->withAll()->count();;
        if($quotation == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->quotationRepository->withAll()->last()->id;
        }
        $start_number = config('settings.quotation_start_number');
        $quotation_no = config('settings.quotation_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $this->quotationRepository->create([
            'quotations_number' => $quotation_no,
            'company_id' => $opportunity->assigned_partner_id,
            'date' => date(config('settings.date_format')),
            'exp_date' => $opportunity->expected_closing_date,
            'payment_term' => config('settings.payment_term1')." Days",
            'sales_team_id' => $opportunity->sales_team_id,
            'status' => 'Draft Quotation',
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'opportunity_id' => $opportunity->id,
            'discount' =>0,
            'is_delete_list' =>0,
            'is_converted_list' =>0,
            'is_quotation_invoice_list' =>0,
        ]);
        $opportunity->update(['stages' => 'Won','is_converted_list'=>1]);

        return redirect('quotation/draft_quotations');
    }

    //    convert to archive
    public function convertToArchive($opportunity, Request $request){
        $opportunity = $this->opportunityRepository->find($opportunity);
        $opportunity->update(['stages' => 'Loss','is_archived' => 1,'lost_reason' => $request->lost_reason]);
        return redirect('opportunity_archive');
    }

    public function ajaxAgentList( Request $request ) {
        $customers_list = $this->customerRepository->getAll()->where('company_id',$request->id);
        $customers=[];
        foreach ($customers_list as $customer){
            $customers[$customer->user->id]=$customer->user->full_name;
        }
        return $customers;
    }
}
