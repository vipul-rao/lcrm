<?php

namespace App\Http\Controllers\Users;

use App\Helpers\ExcelfileValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyImportRequest;
use App\Http\Requests\CompanyRequest;
use App\Repositories\CallRepository;
use App\Repositories\CityRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\CountryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\EmailRepository;
use App\Repositories\ExcelRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\MeetingRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\StateRepository;
use App\Repositories\UserRepository;
use DataTables;
use Illuminate\Http\Request;

class CompanyController extends Controller
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
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var QuotationRepository
     */
    private $quotationRepository;
    /**
     * @var SalesOrderRepository
     */
    private $salesOrderRepository;

    private $countryRepository;

    private $stateRepository;

    private $cityRepository;

    private $meetingRepository;

    private $callRepository;

    private $emailRepository;

    private $customerRepository;

    private $organizationRepository;

    private $excelRepository;

    protected $user;

    public function __construct(
        CompanyRepository $companyRepository,
        UserRepository $userRepository,
        InvoiceRepository $invoiceRepository,
        QuotationRepository $quotationRepository,
        SalesOrderRepository $salesOrderRepository,
        CountryRepository $countryRepository,
        StateRepository $stateRepository,
        CityRepository $cityRepository,
        MeetingRepository $meetingRepository,
        CallRepository $callRepository,
        EmailRepository $emailRepository,
        CustomerRepository $customerRepository,
        OrganizationRepository $organizationRepository,
        ExcelRepository $excelRepository
    ) {
        parent::__construct();

        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->quotationRepository = $quotationRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
        $this->cityRepository = $cityRepository;
        $this->meetingRepository = $meetingRepository;
        $this->callRepository = $callRepository;
        $this->emailRepository = $emailRepository;
        $this->customerRepository = $customerRepository;
        $this->organizationRepository = $organizationRepository;
        $this->excelRepository = $excelRepository;

        view()->share('type', 'company');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['customers.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('company.companies');

        return view('user.company.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('company.new');

        return view('user.company.create', compact('title'));
    }

    public function store(CompanyRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id' => $user->id, 'organization_id' => $organization->id]);

        if ($request->hasFile('company_avatar_file')) {
            $file = $request->file('company_avatar_file');
            $file = $this->companyRepository->uploadAvatar($file);

            $request->merge([
                'company_avatar' => $file->getFileInfo()->getFilename(),
            ]);
            $this->companyRepository->generateThumbnail($file);
        }

        $this->companyRepository->create($request->except('company_avatar_file'));

        return redirect('company');
    }

    public function edit($company)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('company.edit');
        $company = $this->companyRepository->find($company);
        $states = $this->stateRepository->orderBy('name', 'asc')->findByField('country_id', $company->country_id)->pluck('name', 'id');
        $cities = $this->cityRepository->orderBy('name', 'asc')->findByField('state_id', $company->state_id)->pluck('name', 'id');
        $customers_list = $this->customerRepository->getAll()->where('company_id', $company->id);
        $customers = [];
        foreach ($customers_list as $customer) {
            $customers[$customer->user->id] = $customer->user->full_name;
        }

        return view('user.company.create', compact('title', 'company', 'cities', 'states', 'customers'));
    }

    public function update(CompanyRequest $request, $company)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        if (empty($request->main_contact_person)) {
            $request->merge(['main_contact_person' => 0]);
        }
        $company = $this->companyRepository->find($company);
        if ($request->hasFile('company_avatar_file')) {
            $file = $request->file('company_avatar_file');
            $file = $this->companyRepository->uploadAvatar($file);

            $request->merge([
                'company_avatar' => $file->getFileInfo()->getFilename(),
            ]);
            $this->companyRepository->generateThumbnail($file);
        }

        $company->update($request->except('company_avatar_file'));

        return redirect('company');
    }

    public function show($company)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $company = $this->companyRepository->find($company);
        $title = trans('company.details');
        $action = trans('action.show');
        $open_invoices = round($this->invoiceRepository->getAllOpenForCustomer($company->id)->sum('final_price'), 3);
        $overdue_invoices = round($this->invoiceRepository->getAllOverdueForCustomer($company->id)->sum('unpaid_amount'), 3);
        $paid_invoices = round($this->invoiceRepository->getAllPaidForCustomer($company->id)->sum('final_price'), 3);
        $total_sales = round($this->invoiceRepository->getAllForCustomer($company->id)->sum('final_price'), 3);

        $quotations_total = round($this->quotationRepository->getAllForCustomer($company->id)
            ->get()->sum('grand_total'), 3);

        $salesorder = $this->salesOrderRepository->getAllForCustomer($company->id)
            ->get()->count();

        $invoices = $this->invoiceRepository->getAllForCustomer($company->id)->count();

        $quotations = $this->quotationRepository->getAllForCustomer($company->id)
            ->get()->count();

        $calls = $this->callRepository->findByField('company_id', $company->id)->count();

        $meetings_res = $this->meetingRepository->getAll();
        $meeting = 0;

        foreach ($meetings_res as $meetings) {
            $b = explode(',', $meetings->attendees);
            if (in_array($company->id, $b)) {
                ++$meeting;
            }
        }

        $emails = $this->emailRepository->findByField('assign_customer_id', $company->id)->count();

        return view('user.company.delete', compact('title', 'company', 'action', 'total_sales', 'open_invoices', 'paid_invoices',
            'quotations_total', 'salesorder', 'quotations', 'invoices', 'calls', 'meeting', 'emails', 'overdue_invoices'));
    }

    public function delete($company)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $company = $this->companyRepository->find($company);
        $title = trans('company.delete');

        $open_invoices = round($this->invoiceRepository->getAllOpenForCustomer($company->id)->sum('unpaid_amount'), 3);
        $overdue_invoices = round($this->invoiceRepository->getAllOverdueForCustomer($company->id)->sum('unpaid_amount'), 3);
        $paid_invoices = round($this->invoiceRepository->getAllPaidForCustomer($company->id)->sum('grand_total'), 3);
        $total_sales = round($this->invoiceRepository->getAllForCustomer($company->id)->sum('unpaid_amount'), 3);

        $quotations_total = round($this->quotationRepository->getAllForCustomer($company->id)
            ->get()->sum('grand_total'), 3);

        $salesorder = $this->salesOrderRepository->getAllForCustomer($company->id)
            ->get()->count();

        $invoices = $this->invoiceRepository->getAllOpenForCustomer($company->id)->count();

        $quotations = $this->quotationRepository->getAllForCustomer($company->id)
            ->get()->count();

        $calls = $this->callRepository->findByField('company_id', $company->id)->count();

        $meetings_res = $this->meetingRepository->getAll();
        $meeting = 0;

        foreach ($meetings_res as $meetings) {
            $b = explode(',', $meetings->attendees);
            if (in_array($company->id, $b)) {
                ++$meeting;
            }
        }

        $emails = $this->emailRepository->findByField('assign_customer_id', $company->id)->count();
        return view('user.company.delete', compact('title', 'company', 'action', 'total_sales', 'open_invoices', 'paid_invoices',
            'quotations_total', 'salesorder', 'quotations', 'invoices', 'calls', 'meeting', 'emails', 'overdue_invoices'));
    }

    public function destroy($company)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $company = $this->companyRepository->find($company);
        $company->delete();

        return redirect('company');
    }

    public function data()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $company = $this->companyRepository->getAll()
            ->map(function ($comp) use ($orgRole){
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'customer' => isset($comp->contactPerson) ? $comp->contactPerson->full_name : '--',
                    'phone' => $comp->phone,
                    'orgRole' => $orgRole,
                ];
            });

        return DataTables::of($company)

            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'customers.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'company/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>
                                    @endif
                                    <a href="{{ url(\'company/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @if(Sentinel::getUser()->hasAccess([\'customers.delete\']) || $orgRole=="admin")
                                    <a href="{{ url(\'company/\' . $id . \'/delete\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                       @endif')

            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $countries = $this->countryRepository->orderBy('name', 'asc')->pluck('name', 'id')->prepend(trans('company.select_country'), '');

        view()->share('countries', $countries);
    }

    public function downloadExcelTemplate() {
        ob_end_clean();
        $path = base_path('resources/excel-templates/company.xlsx');

        if (file_exists($path)) {
            return response()->download($path);
        }

        return 'File not found!';
    }

    public function getImport() {
        $title = trans( 'company.newupload' );
        return view( 'user.company.import', compact( 'title' ) );
    }

    public function postImport( Request $request ) {
        if(! ExcelfileValidator::validate( $request ))
        {
            return response('invalid File or File format', 500);
        }
        $reader = $this->excelRepository->load($request->file('file'));
        $data = $reader->all()->map( function ( $row ) {
            return [
                'name'   => $row->name,
                'website'   => $row->website,
                'phone'        => $row->phone,
                'mobile'        => $row->mobile,
                'email'        => $row->email,
                'fax'        => $row->fax,
                'address'        => $row->address,
                'country_id'     => 101,
            ];
        });
        $countries = $this->countryRepository->orderBy( "name", "asc" )->all()
            ->map( function ( $country ) {
                return [
                    'text' => $country->name,
                    'id'   => $country->id,
                ];
            });
        return response()->json(compact('data','countries'), 200);
    }
    public function postAjaxStore( CompanyImportRequest $request ) {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id' => $user->id, 'organization_id' => $organization->id]);
        $this->companyRepository->create( $request->except( 'created', 'errors', 'selected','tags' ) );

        return response()->json( [], 200 );
    }

}
