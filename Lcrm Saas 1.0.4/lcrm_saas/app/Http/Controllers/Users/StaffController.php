<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteRequest;
use App\Http\Requests\StaffRequest;
use App\Repositories\InviteUserRepository;
use App\Repositories\UserRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationSettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteStaff;
use DataTables;

class StaffController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var InviteUserRepository
     */
    private $inviteUserRepository;

    /*user site settings*/
    private $emailSettings;
    /**
     * @var PayplanRepository
     */
    private $payPlanRepository;

    private $siteNameSettings;
    /**
     * @var OrganizationSettingsRepository
     */
    private $organizationSettingsRepository;

    protected $user;

    /**
     * StaffController constructor.
     *
     * @param UserRepository                 $userRepository
     * @param InviteUserRepository           $inviteUserRepository
     * @param OrganizationSettingsRepository $organizationSettingsRepository
     */
    public function __construct(
        UserRepository $userRepository,
        InviteUserRepository $inviteUserRepository,
        OrganizationRepository $organizationRepository,
        PayPlanRepository $payPlanRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->inviteUserRepository = $inviteUserRepository;
    }

    public function generateParams()
    {
        $this->user = $this->getUser();
        $can_edit = 0;
        $this->emailSettings = config('settings.site_email');
        $this->siteNameSettings = $this->organizationSettingsRepository->getKey('site_name');

        view()->share('can_edit', $can_edit);

        view()->share('type', 'staff');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }

        $title = trans('staff.staffs');

        return view('user.staff.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('staff.new');

        return view('user.staff.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StaffRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StaffRequest $request)
    {
        flash('unexpected', 'danger');

        return redirect('staff');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $staff
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param int $id
     */
    public function edit($staff)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $staff = $this->userRepository->find($staff);
        if ('1' == $staff->id) {
            return redirect('staff');
        } else {
            $title = trans('staff.edit');

            return view('user.staff.edit', compact('title', 'staff'));
        }
    }

    public function update(StaffRequest $request, $staff)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $staff = $this->userRepository->find($staff);

        if ($request->hasFile('user_avatar_file')) {
            $file = $request->file('user_avatar_file');
            $file = $this->userRepository->uploadAvatar($file);

            $request->merge([
                'user_avatar' => $file->getFileInfo()->getFilename(),
            ]);

            $this->userRepository->generateThumbnail($file);
        } else {
            $request->merge([
                'user_avatar' => $staff->user_avatar,
            ]);
        }

        foreach ($staff->getPermissions() as $key => $item) {
            $staff->removePermission($key);
        }

        foreach ($request->get('permissions', []) as $permission) {
            $staff->addPermission($permission);
        }
        $staff->save();
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'user_avatar' => $request->user_avatar,
        ];

        if ('' != $request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $this->userRepository->update($data, $staff->id);

        return redirect('staff');
    }

    public function show($staff)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $staff = $this->userRepository->find($staff);
        $title = trans('staff.show_staff');
        $action = trans('action.show');

        return view('user.staff.show', compact('title', 'staff', 'action'));
    }

    public function delete($staff)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $staff = $this->userRepository->find($staff);
        $title = trans('staff.delete_staff');

        return view('user.staff.delete', compact('title', 'staff'));
    }

    public function destroy($staff)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $staff = $this->userRepository->find($staff);
        if ('1' != $staff->id) {
            $staff->delete();
        }

        return redirect('staff');
    }

    public function data()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['staff.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $staff = $this->organizationRepository->getStaff()->get()
           ->map(function ($user) use ($orgRole){
               return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format(config('settings.date_format')),
                   'orgRole' => $orgRole,
                ];
           });

        return DataTables::of($staff)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'staff.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'staff/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     @endif
                                     <a href="{{ url(\'staff/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     @if(Sentinel::getUser()->hasAccess([\'staff.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'staff/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                      @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    /**
     * invite.
     */
    public function invite()
    {
        $this->generateParams();
        $title = trans('staff.invite_staffs');

        return view('user.staff.invite', compact('title'));
    }

    public function inviteSave(InviteRequest $request)
    {
        $this->generateParams();

        if (filter_var($this->emailSettings, FILTER_VALIDATE_EMAIL)) {
            $emails = array_filter(array_unique(explode(';', $request->emails)));

            $organization = $this->userRepository->getOrganization();
            $plan = null;
            $staff_count = $this->organizationRepository->getStaffWithUser()->count();

            $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
                ])->count();

            if ($organization
                && count($organization->subscriptions)
                && $organization->subscribed($organization->subscriptions->first()->name)
            ) {
                $subscriptions = $organization->subscriptions->first();
                $plan = $this->payPlanRepository->findByField('plan_id', $subscriptions->stripe_plan)->first();
            }
            if ($organization && $organization->generic_trial_plan) {
                if ($organization->onGenericTrial()) {
                    $subscriptions = $organization->subscriptions->first();
                    $plan = $this->payPlanRepository->find($organization->generic_trial_plan);
                } else {
                    /**
                     * Check for Unlimited Plan.
                     */
                    $temp_plan = $this->payPlanRepository->find($organization->generic_trial_plan);
                    if (!$temp_plan->is_credit_card_required && !$temp_plan->trial_period_days) {
                        $plan = $temp_plan;
                    }
                }
            }
            if (isset($organization) && $organization->subscription_type=='paypal'){
                if(isset($organization->generic_trial_plan)){
                    $plan_id = $organization->generic_trial_plan;
                }else{
                    $plan_id = $organization->subscriptions->first()->payplan_id;
                }
                $plan = $this->payPlanRepository->find($plan_id);
            }

            if (
                (count($emails) > ($plan->no_people - ($staff_count + $unanswered_invites)))
                &&
                ($plan->no_people)
             ) {
                flash(trans('staff.invite_limit_exceed'))->error();

                return redirect()->back()->with(['input' => $request->emails]);
            }

            foreach ($emails as $key => $email) {
                $this->inviteUserRepository->deleteWhere([
                    'claimed_at' => null,
                    'email' => $email,
                ]);
                $user_email = $this->userRepository->usersWithTrashed($email)->count();
                if (0 == $user_email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $invite = $this->inviteUserRepository->createInvite(['email' => trim($email)]);
                        $inviteUrl = url('invite/'.$invite->code);
                        Mail::to($email)->send(new InviteStaff([
                            'from' => $this->emailSettings,
                            'subject' => 'Invite to '.$this->siteNameSettings,
                            'inviteUrl' => $inviteUrl,
                        ]));
                    } else {
                        flash(trans('Email '.$email.' is not valid.'))->error();
                    }
                } else {
                    flash(trans('Email '.$email.' is already taken'))->error();
                }
            }
        } else {
            flash(trans('staff.invalid-email'))->error();
        }

        return redirect('staff/invite');
    }

    public function inviteCancel($id)
    {
        $this->generateParams();
        $title = trans('staff.invite_cancel');

        $organization = $this->userRepository->getOrganization();
        $invite = $this->inviteUserRepository->findWhere([
                'id' => $id,
                'organization_id' => $organization->id,
            ])->first();

        return view('user.staff.invite-cancel', compact('title', 'invite'));
    }

    public function inviteCancelConfirm($id)
    {
        $organization = $this->userRepository->getOrganization();
        $this->inviteUserRepository->deleteWhere([
                'id' => $id,
                'claimed_at' => null,
                'organization_id' => $organization->id,
            ]);

        return redirect('staff/invite');
    }
}
