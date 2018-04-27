<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\MeetingRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use DataTables;

class OpportunityMeetingController extends Controller
{
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $opportunityRepository;

    private $organizationRepository;

    private $customerRepository;

    private $user;

    /**
     * OpportunityMeetingController constructor.
     *
     * @param MeetingRepository $meetingRepository
     * @param CompanyRepository $companyRepository
     * @param UserRepository    $userRepository
     * @param OptionRepository  $optionRepository
     */
    public function __construct(MeetingRepository $meetingRepository,
                                CompanyRepository $companyRepository,
                                UserRepository $userRepository,
                                OrganizationRepository $organizationRepository,
                                OptionRepository $optionRepository,
                                OpportunityRepository $opportunityRepository,
                                CustomerRepository $customerRepository
    )
    {
        parent::__construct();

        $this->meetingRepository = $meetingRepository;
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        $this->opportunityRepository = $opportunityRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;
        view()->share('type', 'opportunitymeeting');
    }

    public function index($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('meeting.opportunity_meetings');

        return view('user.opportunitymeeting.index', compact('title', 'opportunity'));
    }

    public function create($opportunity)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->find($opportunity);
        $title = trans('meeting.opportunity_new');
        $this->customers($opportunity->assigned_partner_id);

        return view('user.opportunitymeeting.create', compact('title', 'opportunity'));
    }

    public function store($opportunity, MeetingRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $company_attendees = implode(',',$request->company_attendees);
        if(isset($request->staff_attendees)){
            $staff_attendees = implode(',',$request->staff_attendees);
            $request->merge(['staff_attendees'=>$staff_attendees]);
        }

        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id,'company_attendees'=>$company_attendees]);
        $opportunity->meetings()->create($request->all(), ['user_id'=>$user->id]);

        return redirect('opportunitymeeting/'.$opportunity->id);
    }

    public function edit($opportunity, $meeting)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $meeting = $this->meetingRepository->find($meeting);
        $title = trans('meeting.opportunity_edit');
        $this->customers($opportunity->assigned_partner_id);

        $company_attendees = $this->userRepository->findWhereIn('id',explode(',',$meeting->company_attendees));
        $staff_attendees = $this->userRepository->findWhereIn('id',explode(',',$meeting->staff_attendees));
        return view('user.opportunitymeeting.create', compact('title', 'meeting', 'opportunity','customers','company_attendees','staff_attendees'));
    }

    public function update(MeetingRequest $request, $opportunity, $meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $company_attendees = implode(',',$request->company_attendees);
        if(isset($request->staff_attendees)){
            $staff_attendees = implode(',',$request->staff_attendees);
            $request->merge(['staff_attendees'=>$staff_attendees]);
        }
        $request->merge(['company_attendees'=>$company_attendees]);

        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $meeting = $this->meetingRepository->find($meeting);
        $meeting->all_day = ($request->all_day) ? $request->all_day : 0;
        $meeting->update($request->all());

        return redirect('opportunitymeeting/'.$opportunity->id);
    }

    public function delete($opportunity, $meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $meeting = $this->meetingRepository->find($meeting);
        $title = trans('meeting.opportunity_delete');

        return view('user.opportunitymeeting.delete', compact('title', 'meeting', 'opportunity'));
    }

    public function destroy($opportunity, $meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $meeting = $this->meetingRepository->find($meeting);
        $meeting->delete();

        return redirect('opportunitymeeting/'.$opportunity->id);
    }

    public function data($opportunity)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $opportunity = $this->opportunityRepository->find($opportunity)->load('companies');
        $orgRole = $this->getUser()->orgRole;
        $dateTimeFormat = config('settings.date_time_format');
        $user = $this->user;
        $meetings = $opportunity->meetings()
            ->with('responsible')
            ->get()
            ->filter(function ($meeting) use ($user) {
                return 'Everyone' == $meeting->privacy || ('Only me' == $meeting->privacy && $meeting->user_id==$user->id);
            })
            ->map(function ($meeting) use ($opportunity,$orgRole,$dateTimeFormat){
                return [
                    'id' => $meeting->id,
                    'meeting_subject' => $meeting->meeting_subject,
                    'company_name' => $opportunity->companies->name ?? null,
                    'starting_date' => date($dateTimeFormat,strtotime($meeting->starting_date)),
                    'ending_date' => date($dateTimeFormat,strtotime($meeting->ending_date)),
                    'meeting_type_id' => $opportunity->id,
                    'resp_staff_id' => $meeting->responsible->full_name ?? null,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($meetings)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'meetings.write\']) || $orgRole=="admin")
<a href="{{ url(\'opportunitymeeting/\' . $meeting_type_id . \'/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>
                                            @endif
                                            @if(Sentinel::getUser()->hasAccess([\'meetings.delete\']) || $orgRole=="admin")
                                     <a href="{{ url(\'opportunitymeeting/\' . $meeting_type_id . \'/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->removeColumn('meeting_type_id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->toArray();

        $staffs = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('dashboard.select_staff'),'');

        $privacy = $this->optionRepository->getAll()
            ->where('category', 'privacy')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->toArray();

        $show_times = $this->optionRepository->getAll()
            ->where('category', 'show_times')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->toArray();

        view()->share('show_times', $show_times);
        view()->share('privacy', $privacy);
        view()->share('staffs', $staffs);
        view()->share('companies', $companies);
    }

    public function calendar($opportunity)
    {
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $title = trans('meeting.opportunity_meetings');

        return view('user.opportunitymeeting.calendar', compact('title', 'opportunity'));
    }

    public function calendar_data($opportunity)
    {
        $opportunity = $this->opportunityRepository->getAll()->find($opportunity);
        if (!$opportunity){
            abort(404);
        }
        $events = [];
        $meetings = $opportunity->meetings()
            ->with('responsible')
            ->get()
            ->map(function ($meeting) use ($opportunity) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->meeting_subject,
                    'start_date' => $meeting->starting_date,
                    'end_date' => $meeting->ending_date,
                    'meeting_type_id' => $opportunity->id,
                    'type' => 'meeting',
                ];
            });
        foreach ($meetings as $d) {
            $orgRole = $this->getUser()->orgRole;
            $event = [];
            $start_date = date('Y-m-d', (is_numeric($d['start_date']) ? $d['start_date'] : strtotime($d['start_date'])));
            $end_date = date('Y-m-d', (is_numeric($d['end_date']) ? $d['end_date'] : strtotime($d['end_date'])));
            $event['title'] = $d['title'];
            $event['id'] = $d['id'];
            $event['start'] = $start_date;
            $event['end'] = $end_date;
            $event['allDay'] = true;
            $event['description'] =
                ($this->getUser()->hasAccess(['meetings.write']) || $orgRole=="admin")?
                $d['title'].'&nbsp;<a href="'.url($d['type'].'/'.$d['id'].'/edit').'" class="btn btn-sm btn-success"><i class="fa fa-pencil-square-o">&nbsp;</i></a>'
            : $d['title'];
            array_push($events, $event);
        }

        return json_encode($events);
    }

    private function customers($opportunity){
        $customers_list = $this->customerRepository->getAll()->where('company_id',$opportunity);
        $customers=[];
        foreach ($customers_list as $customer){
            $customers[$customer->user->id]=$customer->user->full_name;
        }
        view()->share('customers',$customers);
    }
}
