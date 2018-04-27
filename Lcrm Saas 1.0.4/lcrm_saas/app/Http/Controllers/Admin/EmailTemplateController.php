<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailTemplateRequest;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\UserRepository;
use DataTables;

class EmailTemplateController extends Controller
{
    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    private $userRepository;

    /**
     * EmailTemplateController constructor.
     *
     * @param EmailTemplateRepository $emailTemplateRepository
     */
    public function __construct(EmailTemplateRepository $emailTemplateRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();

        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->userRepository = $userRepository;

        view()->share('type', 'email_template');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('email_template.email_templates');

        $emailTemplates = $this->emailTemplateRepository->all();

        return view('admin.email_template.index', compact('title', 'emailTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('email_template.new');

        return view('admin.email_template.create', compact('title'));
    }

    public function store(EmailTemplateRequest $request)
    {
        $user = $this->userRepository->getUser();
        $request->merge(['user_id' => $user->id]);
        $this->emailTemplateRepository->create($request->all());

        return redirect('admin/email_template');
    }

    public function edit($emailTemplate)
    {
        $emailTemplate = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)->find($emailTemplate);
        $title = trans('email_template.edit');

        return view('admin.email_template.edit', compact('title', 'emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmailTemplateRequest $request, $emailTemplate)
    {
        $emailTemplate = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)->find($emailTemplate);
        $emailTemplate->update($request->all());

        return redirect('admin/email_template');
    }

    public function show($emailTemplate)
    {
        $emailTemplate = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)->find($emailTemplate);
        $title = trans('email_template.show');
        $action = trans('action.show');

        return view('admin.email_template.show', compact('title', 'emailTemplate', 'action'));
    }

    public function delete($emailTemplate)
    {
        $emailTemplate = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)->find($emailTemplate);
        $title = trans('email_template.delete');

        return view('admin.email_template.delete', compact('title', 'emailTemplate'));
    }

    public function destroy($emailTemplate)
    {
        $emailTemplate = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)->find($emailTemplate);
        $emailTemplate->delete();

        return redirect('admin/email_template');
    }

    /**
     * Get ajax datatables data.
     */
    public function data()
    {
        $email_templates = $this->emailTemplateRepository->all()->where('user_id', $this->userRepository->getUser()->id)
            ->map(function ($email_template) {
                return [
                    'id' => $email_template->id,
                    'title' => $email_template->title,
                    'text' => $email_template->text,
                ];
            })->values();

        return DataTables::of($email_templates)
            ->addColumn('actions', '
                                        <a href="{{ url(\'admin/email_template/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>

                                     <a href="{{ url(\'admin/email_template/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.show\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'admin/email_template/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     ')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }
}
