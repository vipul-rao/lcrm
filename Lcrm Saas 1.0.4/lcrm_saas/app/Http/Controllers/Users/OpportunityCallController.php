<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallRequest;
use App\Repositories\CallRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use DataTables;

class OpportunityCallController extends Controller
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    private $organizationRepository;

    private $opportunityRepository;

    private $callRepository;

    protected $user;

    public function __construct(CompanyRepository $companyRepository,
                                UserRepository $userRepository,
                                OrganizationRepository $organizationRepository,
                                OpportunityRepository $opportunityRepository,
                                CallRepository $callRepository)
    {
        parent::__construct();

        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->opportunityRepository = $opportunityRepository;
        $this->organizationRepository = $organizationRepository;
        $this->callRepository = $callRepository;

        view()->share('type', 'opportunitycall');
    }

    public function index($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('call.opportunity_calls');

        return view('user.opportunitycall.index', compact('title', 'opportunity'));
    }

    public function create($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('call.opportunity_new');

        return view('user.opportunitycall.create', compact('title', 'opportunity'));
    }

    public function store($opportunity, CallRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id]);
        $opportunity->calls()->create($request->all(),['user_id'=>$user->id]);

        return redirect('opportunitycall/'.$opportunity->id);
    }

    public function edit($opportunity, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $call = $this->callRepository->find($call);
        $title = trans('call.opportunity_edit');

        return view('user.opportunitycall.create', compact('title', 'call', 'opportunity'));
    }

    public function update(CallRequest $request, $opportunity, $call)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $call = $this->callRepository->find($call);
        $call->update($request->all());

        return redirect('opportunitycall/'.$opportunity->id);
    }

    public function delete($opportunity, $call)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $call = $this->callRepository->find($call);
        $title = trans('call.opportunity_delete');

        return view('user.opportunitycall.delete', compact('title', 'call', 'opportunity'));
    }

    public function destroy($opportunity, $call)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $call = $this->callRepository->find($call);
        $call->delete();
        return redirect('opportunitycall/'.$opportunity->id);
    }

    public function data($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $calls = $opportunity->calls()
            ->with('responsible', 'company')
            ->get()
            ->map(function ($call) use ($opportunity,$orgRole,$dateFormat) {
                return [
                    'id' => $call->id,
                    'company_id' => $call->company->name ?? null,
                    'date' => date($dateFormat,strtotime($call->date)),
                    'call_summary' => $call->call_summary,
                    'duration' => $call->duration,
                    'resp_staff_id' => $call->responsible->full_name ?? null,
                    'call_type_id' => $opportunity->id,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($calls)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'logged_calls.write\']) || $orgRole=="admin")
<a href="{{ url(\'opportunitycall/\' . $call_type_id . \'/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                             @endif
                                            @if(Sentinel::getUser()->hasAccess([\'logged_calls.delete\']) || $orgRole=="admin")
                                     <a href="{{ url(\'opportunitycall/\' . $call_type_id . \'/\' . $id . \'/delete\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->removeColumn('call_type_id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('dashboard.select_company'),'');
        $staffs = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('dashboard.select_staff'),'');

        view()->share('staffs', $staffs);
        view()->share('companies', $companies);
    }
}
