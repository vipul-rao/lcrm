<?php

namespace App\Http\Controllers\Users;

use App\Helpers\ExcelfileValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\LeadImportRequest;
use App\Http\Requests\LeadRequest;
use App\Repositories\CityRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CountryRepository;
use App\Repositories\ExcelRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\StateRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;

class LeadController extends Controller
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $countryRepository;

    private $stateRepository;

    private $cityRepository;

    private $organizationRepository;

    private $excelRepository;

    protected $user;

    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        LeadRepository $leadRepository,
        SalesTeamRepository $salesTeamRepository,
        OptionRepository $optionRepository,
        CountryRepository $countryRepository,
        StateRepository $stateRepository,
        CityRepository $cityRepository,
        OrganizationRepository $organizationRepository,
        ExcelRepository $excelRepository
    ) {

        parent::__construct();

        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->leadRepository = $leadRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
        $this->cityRepository = $cityRepository;
        $this->organizationRepository = $organizationRepository;
        $this->excelRepository = $excelRepository;

        view()->share('type', 'lead');
        $this->optionRepository = $optionRepository;
    }

    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['leads.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('lead.leads');

        $priorities = $this->optionRepository->findByField('category','priority')
            ->map(function ($title) {
                return [
                    'title' => $title->title,
                    'value'   => $title->value,
                ];
            })->toArray();
        $colors = ['#3295ff','#2daf57','#fc4141','#fcb410','#17a2b8','#3295ff','#2daf57','#fc4141','#fcb410','#17a2b8'];
        foreach ($priorities as $key=>$priority){
            $priorities[$key]['color'] = isset($colors[$key])?$colors[$key]:"";
            $priorities[$key]['leads'] = $this->leadRepository->getAll()->where('priority', $priority['value'])->count();
        }

        $graphics = [];

        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'leads' => $this->leadRepository->getMonthYear($monthno, $year)->count(),
            ];
        }

        return view('user.lead.index', compact('title','priorities','graphics'));
    }

    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['leads.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('lead.new');
        $calls = 0;

        return view('user.lead.create', compact('title', 'calls'));
    }

    public function store(LeadRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['leads.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id]);
        $this->leadRepository->create($request->all());

        return redirect('lead');
    }

    public function edit($lead)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['leads.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $title = trans('lead.edit');

        $calls = $lead->calls()->count();
        $states = $this->stateRepository->orderBy('name', 'asc')->findByField('country_id', $lead->country_id)->pluck('name', 'id');
        $cities = $this->cityRepository->orderBy('name', 'asc')->findByField('state_id', $lead->state_id)->pluck('name', 'id');

        return view('user.lead.edit', compact('lead', 'title', 'calls', 'states', 'cities'));
    }

    public function update(LeadRequest $request,$lead)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['leads.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $lead->update($request->all());

        return redirect('lead');
    }

    public function show($lead)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['leads.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $title = trans('lead.show');
        $action = trans('action.show');
        return view('user.lead.show', compact('title', 'lead', 'action'));
    }

    public function delete($lead)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['leads.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $title = trans('lead.delete');
        return view('user.lead.delete', compact('title', 'lead'));
    }

    public function destroy($lead)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['leads.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $lead->delete();

        return redirect('lead');
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['leads.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $leads = $this->leadRepository->getAll()
            ->map(function ($lead) use ($orgRole,$dateFormat){
                return [
                    'id' => $lead->id,
                    'created_at' => date($dateFormat,strtotime($lead->created_at)),
                    'company_name' => $lead->company_name,
                    'contact_name' => $lead->contact_name,
                    'product_name' => $lead->product_name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'calls' => $lead->calls->count(),
                    'priority' => $lead->priority,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($leads)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'leads.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'lead/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                    @endif
                                    @if(Sentinel::getUser()->hasAccess([\'logged_calls.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'leadcall/\'. $id .\'/\' ) }}" title="{{ trans(\'table.calls\') }}">
                                            <i class="fa fa-fw fa-phone text-primary"></i> <sup>{{ $calls }}</sup></a>
                                    @endif
                                     @if(Sentinel::getUser()->hasAccess([\'leads.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'lead/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @endif
                                    @if(Sentinel::getUser()->hasAccess([\'leads.delete\']) || $orgRole=="admin")
                                     <a href="{{ url(\'lead/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                    @endif')
            ->removeColumn('id')
            ->removeColumn('calls')
            ->rawColumns(['actions'])
            ->make();
    }

    public function ajaxStateList(Request $request)
    {
        return $this->stateRepository->orderBy('name','asc')->findByField('country_id',$request->id)->pluck('name', 'id')->prepend(trans('lead.select_state'),'');
    }

    public function ajaxCityList(Request $request)
    {
        return $this->cityRepository->orderBy('name','asc')->findByField('state_id',$request->id)->pluck('name', 'id')->prepend(trans('lead.select_city'),'');
    }

    private function generateParams()
    {
        $this->user = $this->getUser();

        $priority = $this->optionRepository->getAll()
            ->where('category', 'priority')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->prepend(trans('lead.priority'),'');

        $titles = $this->optionRepository->getAll()
            ->where('category', 'titles')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->prepend(trans('lead.title'),'');

        $countries = $this->countryRepository->orderBy('name', 'asc')->pluck('name', 'id')->prepend(trans('lead.select_country'),'');

        $salesteams = $this->salesTeamRepository->orderBy('id', 'asc')->pluck('salesteam', 'id')->prepend(trans('dashboard.select_sales_team'),'');

        $functions = $this->optionRepository->getAll()->where( 'category', 'function_type' )
            ->map( function ( $title ) {
                return [
                    'title' => $title->title,
                    'value' => $title->value,
                ];
            } )->pluck( 'title', 'value' )
            ->prepend(trans('lead.function'), '');


        view()->share('priority', $priority);
        view()->share('titles', $titles);
        view()->share('countries', $countries);
        view()->share('salesteams', $salesteams);
        view()->share('functions',$functions);
    }

    public function downloadExcelTemplate() {
        ob_end_clean();
        $path = base_path('resources/excel-templates/leads.xlsx');

        if (file_exists($path)) {
            return response()->download($path);
        }

        return 'File not found!';
    }

    public function getImport() {
        $title = trans( 'lead.newupload' );
        return view( 'user.lead.import', compact( 'title' ) );
    }

    public function postImport( Request $request ) {
        if(! ExcelfileValidator::validate( $request ))
        {
            return response('invalid File or File format', 500);
        }
        $reader = $this->excelRepository->load($request->file('file'));
        $customers = $reader->all()->map( function ( $row ) {
            return [
                'company_name'   => $row->company,
                'company_site'   => $row->company_site,
                'address'        => $row->address,
                'product_name'   => $row->product_name,
                'contact_name'   => $row->client_name,
                'email'          => $row->email,
                'function'       => $row->function,
                'phone'          => $row->phone,
                'mobile'         => $row->mobile,
                'country_id'     => 101,
                'priority'       => $row->priority,
            ];
        });
        $countries = $this->countryRepository->orderBy( "name", "asc" )->all()
            ->map( function ( $country ) {
                return [
                    'text' => $country->name,
                    'id'   => $country->id,
                ];
            });
        $salesteams = $this->salesTeamRepository->orderBy('id', 'asc')
            ->all()->map( function ( $salesteam ) {
                return [
                    'text' => $salesteam->salesteam,
                    'id'   => $salesteam->id,
                ];
            } );
        $functions = $this->optionRepository->getAll()->where( 'category', 'function_type' )
            ->map( function ( $title ) {
                return [
                    'title' => $title->title,
                    'value' => $title->value,
                ];
            } )->pluck( 'title', 'value' );
        $priorities = $this->optionRepository->getAll()->where( 'category', 'priority' )
            ->map( function ( $title ) {
                return [
                    'title' => $title->title,
                    'value' => $title->value,
                ];
            } )->pluck( 'title', 'value' );
        return response()->json(compact('customers','countries','salesteams','functions','priorities'), 200);
    }
    public function postAjaxStore( LeadImportRequest $request ) {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id' => $user->id, 'organization_id' => $organization->id]);
        $this->leadRepository->create( $request->except( 'created', 'errors', 'selected' ) );

        return response()->json( [], 200 );
    }
}
