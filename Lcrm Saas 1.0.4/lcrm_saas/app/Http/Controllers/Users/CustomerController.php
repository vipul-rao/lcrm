<?php

namespace App\Http\Controllers\Users;

use App\Helpers\ExcelfileValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ExcelRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;
class CustomerController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var ExcelRepository
     */
    private $excelRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $customerRepository;

    private $organizationRepository;

    private $organizationRolesRepository;

    protected $user;
    /**
     * CustomerController constructor.
     *
     * @param UserRepository                 $userRepository
     * @param CompanyRepository              $companyRepository
     * @param SalesTeamRepository            $salesTeamRepository
     * @param ExcelRepository                $excelRepository
     * @param OptionRepository               $optionRepository
     */
    public function __construct(
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        SalesTeamRepository $salesTeamRepository,
        ExcelRepository $excelRepository,
        OptionRepository $optionRepository,
        CustomerRepository $customerRepository,
        OrganizationRepository $organizationRepository,
        OrganizationRolesRepository $organizationRolesRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->excelRepository = $excelRepository;
        $this->optionRepository = $optionRepository;
        $this->customerRepository = $customerRepository;
        $this->organizationRepository = $organizationRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;

        view()->share('type', 'customer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['customers.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('customer.customers');

        return view('user.customer.index', compact('title', 'companies', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('customer.new');

        return view('user.customer.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        if ($request->hasFile('user_avatar_file')) {
            $file = $request->file('user_avatar_file');
            $file = $this->userRepository->uploadAvatar($file);

            $request->merge([
                'user_avatar' => $file->getFileInfo()->getFilename(),
            ]);

            $this->userRepository->generateThumbnail($file);
        }

        $customer = $this->userRepository->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => $request->password,
            'user_id' => $user->id,
            'user_avatar' => $request->user_avatar
        ], true);

        $this->userRepository->assignRole($customer, 'user');
        $organization = $this->organizationRepository->find($organization->id);
        $role = $this->organizationRolesRepository->findByField('slug', 'customer')->first();
        $this->organizationRolesRepository->attachRole($organization, $customer, $role);


        $request->merge(['user_id'=>$customer->id,'organization_id'=>$organization->id]);
        $this->customerRepository->create($request->except('first_name', 'last_name', 'phone_number', 'email', 'password',
            'password_confirmation', 'user_avatar_file','user_avatar'));

        return redirect('customer');
    }

    public function edit($customer ) {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $customer = $this->customerRepository->getAll()->find($customer);
        $title = trans( 'customer.edit' );
        return view( 'user.customer.edit', compact( 'customer', 'title' ) );
    }

    public function update(CustomerRequest $request, $customer)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['customers.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $customer = $this->customerRepository->getAll()->find($customer);

        if ($request->hasFile('user_avatar_file')) {
            $file = $request->file('user_avatar_file');
            $file = $this->userRepository->uploadAvatar($file);

            $request->merge([
                'user_avatar' => $file->getFileInfo()->getFilename(),
            ]);

            $this->userRepository->generateThumbnail($file);
        }

        if ($request->password != null) {
            $customer->user->password = bcrypt($request->password);
        }
        if (isset($request->user_avatar) && $request->user_avatar!="") {
            $customer->user->user_avatar = $request->user_avatar;
        }
        $customer->user->update($request->only('first_name','last_name','email','phone_number'));
        $customer->update($request->except('first_name', 'last_name', 'phone_number', 'email', 'password',
            'password_confirmation', 'user_avatar_file','user_avatar'));
        return redirect("customer");
    }

    public function show($customer) {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['customers.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $customer = $this->customerRepository->getAll()->find($customer);
        $title  = trans( 'customer.details' );
        $action = "show";
        return view( 'user.customer.show', compact( 'title', 'customer', 'action' ) );
    }

    public function delete($customer)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['customers.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $customer = $this->customerRepository->getAll()->find($customer);
        $title = trans('customer.delete');
        return view('user.customer.delete', compact('title', 'customer'));
    }


    public function destroy($customer)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['customers.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $customer = $this->customerRepository->getAll()->find($customer);
        $customer->user()->delete();
        $customer->delete();
        return redirect('customer');
    }

    public function data()
    {
        $orgRole = $this->getUser()->orgRole;
        $customers = $this->customerRepository->getAll()
            ->map(function ($customer) use ($orgRole) {
                return [
                    'id' => $customer->id,
                    'full_name' => isset($customer->user->full_name)?$customer->user->full_name:null,
                    'company' => isset($customer->company->name) ? $customer->company->name : null,
                    'email' => isset($customer->user->email)?$customer->user->email:null,
                    'phone' => isset($customer->user->phone_number)?$customer->user->phone_number:null,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($customers)

            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'customers.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'customer/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>
                                    @endif
                                    <a href="{{ url(\'customer/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @if(Sentinel::getUser()->hasAccess([\'customers.delete\']) || $orgRole=="admin")
                                    <a href="{{ url(\'customer/\' . $id . \'/delete\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                       @endif')

            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    public function downloadExcelTemplate()
    {
        return response()->download(base_path('resources/excel-templates/customer.xlsx'));
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('dashboard.select_company'),'');
        $titles = $this->optionRepository->getAll()
            ->where('category', 'titles')->pluck('title', 'value')->prepend(trans('customer.title'),'');
        view()->share('companies', $companies);
        view()->share('titles', $titles);
    }

    public function getImport()
    {
        $title = trans('customer.customers');

        return view('user.customer.import', compact('title'));
    }

    public function postImport(Request $request)
    {
        if (!ExcelfileValidator::validate($request)) {
            return response('invalid File or File format', 500);
        }

        $reader = $this->excelRepository->load($request->file('file'));

        $customers = $reader->all()->map(function ($row) {
            return [
                'first_name' => $row->first_name,
                'last_name' => $row->last_name,
                'email' => $row->email,
                'phone_number' => $row->phone,
                'title' => $row->title,
                'password' => $row->password,
                'password_confirmation' => $row->password,
                'mobile' => $row->mobile,
                'address' => $row->address,
                'job_position' => $row->job_position
            ];
        });

        $titles = $this->optionRepository->getAll()->where('category','titles')->map(function ($title) {
            return [
                'text' => $title->value,
                'id' => $title->value,
            ];
        })->values();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->map(function ($company) {
            return [
                'text' => $company->name,
                'id' => $company->id,
            ];
        })->values();

        return response()->json(compact('customers', 'companies','titles'), 200);
    }

    public function postAjaxStore(CustomerRequest $request)
    {
//        return $request;
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
//        return $request;
//        $this->userRepository->create($request->except('created', 'errors', 'selected'));
//        return $user;

        $customer = $this->userRepository->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => $request->password,
            'user_id' => $user->id,
        ], true);

        $this->userRepository->assignRole($customer, 'user');
        $organization = $this->organizationRepository->find($organization->id);
        $role = $this->organizationRolesRepository->findByField('slug', 'customer')->first();
        $this->organizationRolesRepository->attachRole($organization, $customer, $role);


        $request->merge(['user_id'=>$customer->id,'organization_id'=>$organization->id]);
        $this->customerRepository->create($request->except('created', 'errors', 'selected','first_name', 'last_name', 'phone_number', 'email', 'password',
            'password_confirmation', 'user_avatar_file','user_avatar'));
        return response()->json([], 200);
    }

}
