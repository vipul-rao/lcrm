<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ContactUsRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use DataTables;

class ContactUsController extends Controller
{
    private $contactUsRepository;
    private $settingsRepository;
    private $userRepository;

    public function __construct(
        ContactUsRepository $contactUsRepository,
        SettingsRepository $settingsRepository,
        UserRepository $userRepository
    ) {
        parent::__construct();
        $this->contactUsRepository = $contactUsRepository;
        $this->settingsRepository = $settingsRepository;
        $this->userRepository = $userRepository;
        view()->share('type', 'admin/contactus');
    }

    public function index()
    {
        $title = trans('contactus.contacts');

        return view('admin.contactus.index', compact('title'));
    }

    public function show($contactUs)
    {
        $contactUs = $this->contactUsRepository->find($contactUs);
        $title = trans('contactus.show_contact');
        $action = trans('action.show');

        return view('admin.contactus.show', compact('title', 'contactUs', 'action'));
    }

    public function delete($contactUs)
    {
        $contactUs = $this->contactUsRepository->find($contactUs);
        $title = trans('contactus.delete_contact');

        return view('admin.contactus.delete', compact('title', 'contactUs', 'action'));
    }

    public function destroy($contactUs)
    {
        $contactUs = $this->contactUsRepository->find($contactUs);
        $contactUs->delete();

        return redirect('admin/contactus');
    }

    public function data()
    {
        $contactUs = $this->contactUsRepository->all()
            ->map(function ($contactUs) {
                return [
                    'id' => $contactUs->id,
                    'name' => $contactUs->name,
                    'email' => $contactUs->email,
                    'phone_number' => $contactUs->phone_number,
                ];
            });

        return DataTables::of($contactUs)
            ->addColumn('actions', '<a href="{{ url(\'admin/contactus/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            <a href="{{ url(\'admin/contactus/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}" >
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }
}
