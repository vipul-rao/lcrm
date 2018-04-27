<?php

namespace App\Http\Controllers;

use App\Events\Organization\OrganizationCreated;
use App\Http\Requests\RegisterRequest;
use App\Models\UserLogin;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\UserRepository;
use Laracasts\Flash\Flash;

class RegisterController extends Controller
{
    private $organizationRepository;

    private $userRepository;

    private $organizationRolesRepository;

    private $organizationSettingsRepository;

    public function __construct(
        OrganizationRepository $organizationRepository,
        UserRepository $userRepository,
        OrganizationRolesRepository $organizationRolesRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($this->userRepository->check()) {
            return redirect('/');
        }
        $title = trans('auth.signup');
        $navbar_custom = trans('frontend.custom_navbar');

        return view('frontend.register', compact('title','navbar_custom'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        if ($this->userRepository->check()) {
            return redirect('/');
        }
        $this->user = $this->userRepository->getUser();
        $request->merge(['user_id' => 0]);

        $user = $this->userRepository->create([
            'first_name' => $request->owner_first_name,
            'last_name' => $request->owner_last_name,
            'email' => $request->owner_email,
            'phone_number' => $request->owner_phone_number,
            'password' => $request->owner_password,
            'user_id' => $request->user_id,
        ], true);

        $this->userRepository->assignRole($user, 'user');
        // create organization for the user
        $organization = '';
        if ($user) {
            $organization = $this->organizationRepository->create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'user_id' => $user->id,
                'is_deleted' => 0,
            ]);
            $request->merge([
                'site_name' => $request->name,
                'site_email' => $request->email,
                'phone' => $request->phone_number,
            ]);

            foreach ($request->only( 'site_name', 'site_email', 'phone') as $key => $value) {
                $this->organizationSettingsRepository->setKey($key, $value,$organization->id);
            }
            event(new OrganizationCreated($organization));
            $role = $this->organizationRolesRepository->findByField('slug', 'admin')->first();
            $this->organizationRolesRepository->attachRole($organization, $user, $role);
        }
        $newUser = $this->userRepository->login($user);
        if ($newUser) {
            Flash::success(trans('auth.signin_success'));
            $userLogin = new UserLogin();
            $userLogin->user_id = $user->id;
            $userLogin->ip_address = $request->ip();
            $userLogin->save();

            return redirect('dashboard');
        }

        return redirect('/');
    }
}
