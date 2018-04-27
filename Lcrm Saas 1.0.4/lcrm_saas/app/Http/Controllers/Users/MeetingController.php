<?php

namespace App\Http\Controllers\Users;

use App\Events\Meeting\MeetingCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeetingRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\MeetingRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\UserRepository;
use DataTables;

class MeetingController extends Controller
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

    private $organizationRepository;

    private $customerRepository;

    protected $user;

    /**
     * MeetingController constructor.
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
                                CustomerRepository $customerRepository,
                                OptionRepository $optionRepository
    )
    {

        parent::__construct();

        $this->meetingRepository = $meetingRepository;
        $this->companyRepository = $companyRepository;
        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;

        view()->share('type', 'meeting');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('meeting.meetings');

        return view('user.meeting.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('meeting.new');

        return view('user.meeting.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MeetingRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param $
     */
    public function store(MeetingRequest $request)
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
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id'=>$user->id,'organization_id'=>$organization->id,'company_attendees'=>$company_attendees]);
        $meting = $this->meetingRepository->create($request->all());

        event(new MeetingCreated($meting));

        return redirect('meeting');
    }

    public function edit($meeting)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $meeting = $this->meetingRepository->find($meeting);
        $title = trans('meeting.edit');
        $company_attendees = $this->userRepository->findWhereIn('id',explode(',',$meeting->company_attendees));
        $staff_attendees = $this->userRepository->findWhereIn('id',explode(',',$meeting->staff_attendees));

        return view('user.meeting.create', compact('title', 'meeting','company_attendees','staff_attendees'));
    }

    public function update(MeetingRequest $request, $meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $company_attendees = implode(',',$request->company_attendees);;
        if(isset($request->staff_attendees)){
            $staff_attendees = implode(',',$request->staff_attendees);
            $request->merge(['staff_attendees'=>$staff_attendees]);
        }
        $request->merge(['company_attendees'=>$company_attendees]);
        $meeting = $this->meetingRepository->find($meeting);
        $request->merge(['all_day' => $request->all_day ?? 0]);
        $meeting->update($request->all());

        return redirect('meeting');
    }

    public function delete($meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $meeting = $this->meetingRepository->find($meeting);
        $title = trans('meeting.delete');
        return view('user.meeting.delete', compact('title', 'meeting'));
    }

    public function destroy($meeting)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $meeting = $this->meetingRepository->find($meeting);
        $meeting->delete();

        return redirect('meeting');
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['meetings.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateTimeFormat = config('settings.date_time_format');
        $user = $this->user;
        $meetings = $this->meetingRepository->getAll()
            ->filter(function ($meeting) use ($user) {
                return trans('meeting.everyone') == $meeting->privacy || ( trans('meeting.onlyme') == $meeting->privacy && $meeting->user_id==$user->id);
            })
            ->map(function ($meeting) use ($orgRole,$dateTimeFormat){
                return [
                    'id' => $meeting->id,
                    'meeting_subject' => $meeting->meeting_subject,
                    'starting_date' => date($dateTimeFormat,strtotime($meeting->starting_date)),
                    'ending_date' => date($dateTimeFormat,strtotime($meeting->ending_date)),
                    'resp_staff_id' => $meeting->responsible->full_name ?? null,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($meetings)
            ->addColumn('actions', ' @if(Sentinel::getUser()->hasAccess([\'meetings.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'meeting/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}" >
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'meetings.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'meeting/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->toArray();

        $staffs = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('dashboard.select_staff'),'');

        $customers = $this->customerRepository->getAll()->pluck('user_id','id');
        $company_customer = [];
        foreach ($customers as $customer){
            $company_customer[]=$customer;
        }
        $customers = $this->userRepository->findWhereIn('id',$company_customer)->pluck('full_name','id');

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

        view()->share('privacy', $privacy);
        view()->share('show_times', $show_times);
        view()->share('staffs', $staffs);
        view()->share('companies', $companies);
        view()->share('customers', $customers);
    }

    public function calendar()
    {
        $title = trans('meeting.meetings');

        return view('user.meeting.calendar', compact('title', 'opportunity'));
    }

    public function calendar_data()
    {
        $events = [];
        $meetings = $this->meetingRepository->with('responsible')->getAll()
            ->filter(function ($meeting) {
                return 'Everyone' == $meeting->privacy || ('Only me' == $meeting->privacy);
            })
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->meeting_subject,
                    'start_date' => $meeting->starting_date,
                    'end_date' => $meeting->ending_date,
                    'type' => 'meeting',
                ];
            });
        foreach ($meetings as $d) {
            $orgRole = $this->getUser()->orgRole;
            $event = [];
            $start_date = date('Y-m-d', (is_numeric($d['start_date']) ? $d['start_date'] : strtotime($d['start_date'])));
            $end_date = date('Y-m-d', (is_numeric($d['end_date']) ? $d['end_date'] : strtotime($d['end_date'].'+1 day')));
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
}
