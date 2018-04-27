<?php

namespace App\Http\Controllers\Installation;

use App\Http\Requests\InstallSettingsEmailRequest;
use App\Http\Requests\InstallSettingsRequest;
use App\Http\Controllers\Controller;
use App\Repositories\InstallRepository;
use App\Repositories\OptionRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Artisan;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Swift_SmtpTransport;
use Swift_TransportException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    /**
     * @var InstallRepository
     */
    private $installRepository;
    private $settingsRepository;
    private $optionRepository;

    /**
     * InstallController constructor.
     *
     * @param InstallRepository $installRepository
     */
    public function __construct(InstallRepository $installRepository,
                                SettingsRepository $settingsRepository,
                                OptionRepository $optionRepository
    ) {
        $this->installRepository = $installRepository;
        $this->settingsRepository = $settingsRepository;
        $this->optionRepository = $optionRepository;
    }

    public function index()
    {
        return view('install.start');
    }

    public function requirements()
    {
        $requirements = $this->installRepository->getRequirements();
        $allLoaded = $this->installRepository->allRequirementsLoaded();

        return view('install.requirements', compact('requirements', 'allLoaded'));
    }

    public function permissions()
    {
        if (!$this->installRepository->allRequirementsLoaded()) {
            return redirect('install/requirements');
        }

        $folders = $this->installRepository->getPermissions();
        $allGranted = $this->installRepository->allPermissionsGranted();

        return view('install.permissions', compact('folders', 'allGranted'));
    }

    public function database()
    {
        if (!$this->installRepository->allRequirementsLoaded()) {
            return redirect('install/requirements');
        }

        if (!$this->installRepository->allPermissionsGranted()) {
            return redirect('install/permissions');
        }
        editEnv([
            'APP_URL' => url('/'),
        ]);

        return view('install.database');
    }

    public function postDatabase(Request $request)
    {
        editEnv([
            'DB_HOST' => isset($request->host) ? $request->host : env('DB_HOST'),
            'DB_PORT' => isset($request->port) ? $request->port : env('DB_PORT'),
            'DB_DATABASE' => isset($request->database) ? $request->database : env('DB_DATABASE'),
            'DB_USERNAME' => isset($request->username) ? $request->username : env('DB_USERNAME'),
            'DB_PASSWORD' => isset($request->password) ? $request->password : env('DB_PASSWORD'),
        ]);

        return redirect('install/start-installation');
    }

    public function installation()
    {
        if (!$this->installRepository->allRequirementsLoaded()) {
            return redirect('install/requirements');
        }

        if (!$this->installRepository->allPermissionsGranted()) {
            return redirect('install/permissions');
        }

        if (!$this->installRepository->dbCredentialsAreValid()) {
            return redirect('install/database')
                ->withErrors(trans('install.connection_failed'));
        }

        return view('install.installation');
    }

    public function install()
    {
        try {
            if (Schema::hasTable('migrations')) {
                return redirect('install/database')
                ->withErrors('The database is not empty, Can\'t proceed with installation');
            }
        } catch (\Exception $e) {
        }
        try {
            config(['app.debug' => true]);

            Artisan::call('key:generate');

            Artisan::call('migrate', ['--force' => true]);

            Artisan::call('db:seed', ['--force' => true]);

            return redirect('install/settings');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect('install/error');
        }
    }

    public function disable()
    {
        $foldersDisable = $this->installRepository->getDisablePermissions();
        $allDisableGranted = $this->installRepository->allDisablePermissionsGranted();

        return view('install.disable', compact('foldersDisable', 'allDisableGranted'));
    }

    public function settings()
    {
        $this->settingsRepository->forgetKey('install.db_credentials');

        $currency = $this->optionRepository->all()->where('category', 'currency')->pluck('title', 'value')->toArray();

        return view('install.settings', compact('currency'));
    }

    public function settingsSave(InstallSettingsRequest $request)
    {
        $this->settingsRepository->setKey('site_name', $request->site_name);

        $this->settingsRepository->setKey('site_email', $request->site_email);

        $this->settingsRepository->setKey('currency', $request->currency);

        $admin = Sentinel::registerAndActivate([
            'email' => $request->email,
            'password' => $request->password,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_id' => 1,
        ]);
        $admin->user_id = $admin->id;
        $admin->save();

        $role = Sentinel::findRoleBySlug('admin');
        $role->users()->attach($admin);

        return redirect('install/email_settings');
    }

    public function settingsEmail()
    {
        return view('install.mail_settings');
    }

    public function settingsEmailSave(InstallSettingsEmailRequest $request)
    {
        try {
            if ('smtp' == $request->email_driver) {
                $transport = (new Swift_SmtpTransport($request->email_host, $request->email_port))
                  ->setUsername($request->email_username)
                  ->setPassword($request->email_password)
                  ;
                $transport->start();
            }
            foreach ($request->except('_token') as $key => $value) {
                $this->settingsRepository->setKey($key, isset($value) ? $value : '');
            }
            file_put_contents(storage_path('installed'), 'LCRM_SAAS INSTALLATION SUCCESSFUL');

            Artisan::call('config:cache');
            Artisan::call('route:cache');

            return redirect('install/complete');
        } catch (Swift_TransportException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function complete()
    {
        return view('install.complete');
    }

    public function error()
    {
        return view('install.error');
    }
}
