<?php

namespace App\Http\Controllers\Users;

use App\Events\Call\CallCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CallRequest;
use App\Repositories\CallRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use DataTables;

class CallController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CallRepository
     */
    private $callRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    private $organizationRepository;

    private $leadRepository;

    protected $user;

    public function __construct(UserRepository $userRepository,
                                CallRepository $callRepository,
                                CompanyRepository $companyRepository,
                                OrganizationRepository $organizationRepository,
                                LeadRepository $leadRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->callRepository = $callRepository;
        $this->companyRepository = $companyRepository;
        $this->organizationRepository = $organizationRepository;
        $this->leadRepository = $leadRepository;

        view()->share('type', 'call');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('call.calls');

        return view('user.call.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('call.new');

        return view('user.call.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CallRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id]);
        $call = $this->callRepository->create($request->all());

        event(new CallCreated($call));

        return redirect('call');
    }

    public function edit($call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('call.edit');
        $call = $this->callRepository->find($call);

        return view('user.call.create', compact('title', 'call'));
    }

    public function update(CallRequest $request, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $call = $this->callRepository->find($call);
        $call->update($request->all());

        return redirect('call');
    }

    public function delete($call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $call = $this->callRepository->find($call);
        $title = trans('call.delete');

        return view('user.call.delete', compact('title', 'call'));
    }

    public function destroy($call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $call = $this->callRepository->find($call);
        $call->delete();

        return redirect('call');
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $calls = $this->callRepository->getAll()
            ->map(function ($call) use ($orgRole,$dateFormat) {
                if(is_int($call->company_id) && $call->company_id>0){
                    $company_name = $call->company->name??null;
                }else{
                    $company_name = $call->company_name;
                }
                return [
                    'id' => $call->id,
                    'company' => $company_name,
                    'date' => date($dateFormat, strtotime($call->date)),
                    'call_summary' => $call->call_summary,
                    'duration' => $call->duration,
                    'resp_staff_id' => $call->responsible->full_name??'',
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($calls)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'logged_calls.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'call/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'logged_calls.delete\']) || $orgRole=="admin")
                                     <a href="{{ url(\'call/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('lead.company_name'), '');
        $staffs = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('call.responsible'), '');

        view()->share('staffs', $staffs);
        view()->share('companies', $companies);
    }
}
