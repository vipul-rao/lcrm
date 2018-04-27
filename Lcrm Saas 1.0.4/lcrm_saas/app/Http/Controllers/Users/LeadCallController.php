<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallRequest;
use App\Repositories\CallRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use DataTables;

class LeadCallController extends Controller
{

    private $companyRepository;

    private $userRepository;

    private $callRepository;

    private $organizationRepository;

    private $leadRepository;

    protected $user;


    public function __construct(CompanyRepository $companyRepository,
                                UserRepository $userRepository,
                                CallRepository $callRepository,
                                OrganizationRepository $organizationRepository,
                                LeadRepository $leadRepository
    )
    {
        parent::__construct();

        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->callRepository = $callRepository;
        $this->organizationRepository = $organizationRepository;
        $this->leadRepository = $leadRepository;

        view()->share('type', 'leadcall');
    }

    public function index($lead)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $title = trans('call.lead_calls');

        return view('user.leadcall.index', compact('title', 'lead'));
    }

    public function create($lead)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $title = trans('call.lead_new');

        return view('user.leadcall.create', compact('title', 'lead'));
    }

    public function store($lead, CallRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id]);
        $lead->calls()->create($request->all(),['user_id'=>$user->id]);

        return redirect('leadcall/'.$lead->id);
    }

    public function edit($lead, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $call = $this->callRepository->find($call);
        $title = trans('call.lead_edit');

        return view('user.leadcall.create', compact('title', 'call', 'lead'));
    }

    public function update(CallRequest $request, $lead, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $call = $this->callRepository->find($call);
        $call->update($request->all());

        return redirect('leadcall/'.$lead->id);
    }

    public function delete($lead, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $call = $this->callRepository->find($call);
        $title = trans('call.lead_delete');

        return view('user.leadcall.delete', compact('title', 'call', 'lead'));
    }

    public function destroy($lead, $call)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['logged_calls.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $call = $this->callRepository->find($call);
        $call->delete();

        return redirect('leadcall/'.$lead->id);
    }

    public function data($lead)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['logged_calls.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $lead = $this->leadRepository->find($lead);
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $calls = $lead->calls()
            ->with('responsible', 'company')
            ->get()
            ->map(function ($call) use ($lead,$orgRole,$dateFormat) {
                return [
                    'id' => $call->id,
                    'company_id' => $call->company_name,
                    'date' => date($dateFormat,strtotime($call->date)),
                    'call_summary' => $call->call_summary,
                    'duration' => $call->duration,
                    'lead' => $lead->id,
                    'resp_staff_id' => isset($call->responsible->full_name) ? $call->responsible->full_name : '',
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($calls)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'logged_calls.write\']) || $orgRole=="admin")
<a href="{{ url(\'leadcall/\' . $lead . \'/\' . $id . \'/edit\' ) }}"  title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                            @endif
                                            @if(Sentinel::getUser()->hasAccess([\'logged_calls.delete\']) || $orgRole=="admin")
                                     <a href="{{ url(\'leadcall/\' . $lead . \'/\' . $id . \'/delete\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->removeColumn('lead')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $staffs = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('dashboard.select_company'), '');
        view()->share('staffs', $staffs);
    }
}
