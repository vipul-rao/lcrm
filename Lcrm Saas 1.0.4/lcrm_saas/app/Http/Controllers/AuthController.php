<?php

namespace App\Http\Controllers;

use App\Helpers\Thumbnail;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordConfirmRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserRequest;
use App\Models\UserLogin;
use App\Repositories\SettingsRepository;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repositories\UserRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\InviteUserRepository;

class AuthController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    private $organization;

    private $userRepository;

    private $inviteUserRepository;

    private $organizationRolesRepository;

    private $settingsRepository;

    public function __construct(
        UserRepository $userRepository,
        OrganizationRepository $organization,
        InviteUserRepository $inviteUserRepository,
        OrganizationRolesRepository $organizationRolesRepository,
        SettingsRepository $settingsRepository
     ) {
        $this->organization = $organization;
        $this->userRepository = $userRepository;
        $this->inviteUserRepository = $inviteUserRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function index()
    {
        if ($this->userRepository->check()) {
            return redirect('/');
        }

        return view('login');
    }

    /**
     * Account sign in.
     *
     * @return View
     */
    public function getSignin()
    {
        $title = trans('auth.login');
        if ($this->userRepository->check()) {
            if ($this->userRepository->inRole('admin')) {
                return redirect('/admin');
            } elseif ($this->userRepository->inRole('user')) {
                $user = $this->userRepository->getUser();
                $organization = $this->userRepository->getOrganization();
                $role = $this->organizationRolesRepository->getRole($organization, $user);
                if ('customer' == $role) {
                    return redirect('/customers');
                } else {
                    return redirect('/dashboard');
                }
            }
        }
        $navbar_custom = trans('frontend.custom_navbar');

        return view('login',compact('title','nav_id','navbar_custom'));
    }

    /**
     * Account sign up.
     *
     * @return View
     */
    public function getSignup($code)
    {
        $inviteUser = $this->inviteUserRepository->findWhere([
            'code' => $code,
            'claimed_at' => null,
        ])->first();

        if ($this->userRepository->check() || !isset($inviteUser)) {
            flash('Invitation not found')->error();

            return redirect('/');
        }
        $nav_id = 'invite';

        return view('invite', compact('inviteUser','nav_id'));
    }

    /**
     * Account sign in form processing.
     *
     * @return Redirect
     */
    public function postSignin(LoginRequest $request)
    {
        try {
            if ($user = $this->userRepository->authenticate($request->only('email', 'password'), $request->has('remember'))) {
                Flash::success(trans('auth.signin_success'));
                $userLogin = new UserLogin();
                $userLogin->user_id = $user->id;
                $userLogin->ip_address = $request->ip();
                $userLogin->save();

                //redirect depending on logged in user role
                if ($this->userRepository->inRole('admin')) {
                    return redirect('/admin');
                }
                elseif ($this->userRepository->inRole('user')) {
                    $user = $this->userRepository->getUser();
                    $organization = $this->userRepository->getOrganization();
                    $role = $this->organizationRolesRepository->getRole($organization, $user);
                    if ('customer' == $role) {
                        return redirect('/customers');
                    } else {
                        return redirect('/dashboard');
                    }
                }
            }
            Flash::error(trans('auth.login_params_not_valid'));
        } catch (NotActivatedException $e) {
            Flash::error(trans('auth.account_not_activated'));
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            Flash::error(trans('auth.account_suspended').$delay.trans('auth.second'));
        }

        return back()->withInput();
    }

    public function postSignup(UserRequest $request, $code)
    {
        $inviteUser = $this->inviteUserRepository->findWhere([
            'code' => $code,
            'claimed_at' => null,
        ])->first();

        if (isset($inviteUser)) {
            $staff = $this->userRepository->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $inviteUser->email,
                    'phone_number' => $request->phone_number,
                    'password' => $request->password,
                    'user_id' => $inviteUser->user_id,
            ], true);
            $this->userRepository->assignRole($staff, 'user');
            $organization = $this->organization->find($inviteUser->organization_id);
            $role = $this->organizationRolesRepository->findByField('slug', 'staff')->first();
            $this->organizationRolesRepository->attachRole($organization, $staff, $role);

            $this->inviteUserRepository->update([
                'claimed_at' => now(),
            ], $inviteUser->id);

            return redirect('/');
        } else {
            return back()->withInput();
        }
    }

    public function getForgotPassword()
    {
        if ($this->userRepository->check()) {
            return redirect('/');
        }
        $title = trans('auth.forgot');
        $navbar_custom = trans('frontend.custom_navbar');

        return view('forgot',compact('title','nav_id','navbar_custom'));
    }

    public function postForgotPassword(PasswordResetRequest $request)
    {
        $site_email = $this->settingsRepository->getKey('site_email');
        if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            $response = Password::sendResetLink(
                $request->only('email')
            );
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    Flash::success(trans($response));

                    return redirect()->back();
                case Password::INVALID_USER:
                    Flash::error(trans($response));

                    return redirect()->back();
            }
        } else {
            return redirect()->back();
        }
    }

    public function getForgotPasswordConfirm($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException();
        }
        $title = trans('auth.reset');
        view()->share('title',$title);
        $nav_id = 'forgot_password';
        return view('reset', compact('token','nav_id'));
    }

    public function postForgotPasswordConfirm(PasswordConfirmRequest $request, $passwordResetCode = null)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });
        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect()->to('/')->with('ok', trans('passwords.reset'));
            default:
                Flash::error(trans($response));

                return redirect()->back()
                    ->withInput($request->only('email'));
        }
    }

    /**
     * Logout page.
     *
     * @return Redirect
     */
    public function getLogout()
    {
        $this->userRepository->logout(null, false);
        Flash::success(trans('auth.successfully_logout'));

        return redirect('signin');
    }

    /**
     * Profile page.
     *
     * @return Redirect
     */
    public function getProfile()
    {
        if (!$this->userRepository->check()) {
            return redirect('/');
        }

        $title = trans('auth.user_profile');
        $user = $this->userRepository->getUser();

        return view('profile', compact('title', 'user'));
    }

    public function getAccount()
    {
        if (!$this->userRepository->check()) {
            return redirect('/');
        }
        $title = trans('auth.edit_profile');
        $user = $this->userRepository->getUser();

        return view('account', compact('title', 'user'));
    }

    public function postAccount(ProfileRequest $request)
    {
        if (!$this->userRepository->check()) {
            return redirect('/');
        }

        $user = $this->userRepository->getUser();
        if ('' != $request->hasFile('user_avatar_file')) {
            $file = $request->file('user_avatar_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10).'.'.$extension;

            $destinationPath = public_path().'/uploads/avatar/';
            $file->move($destinationPath, $picture);
            Thumbnail::generate_image_thumbnail($destinationPath.$picture, $destinationPath.'thumb_'.$picture);
            $user->user_avatar = $picture;
        }
        if ('' != $request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->phone_number = $request->phone_number;
        $user->update($request->except('user_avatar_file', 'password', 'password_confirmation'));
        Flash::success(trans('auth.successfully_change_profile'));

        return redirect('profile');
    }
}
