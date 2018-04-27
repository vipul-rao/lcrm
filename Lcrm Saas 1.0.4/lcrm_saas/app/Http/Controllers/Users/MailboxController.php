<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailboxRequest;
use App\Mail\Mailbox;
use App\Repositories\EmailRepository;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailboxController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;

    private $emailRepository;
    private $organizationRepository;
    private $organizationRolesRepository;

    public function __construct(
        UserRepository $userRepository,
        EmailTemplateRepository $emailTemplateRepository,
        EmailRepository $emailRepository,
        OrganizationRepository $organizationRepository,
        OrganizationRolesRepository $organizationRolesRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->emailRepository = $emailRepository;
        $this->organizationRepository = $organizationRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;
        view()->share('type', 'mailbox');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('mailbox.mailbox');

        return view('user.mailbox.index', compact('title'));
    }

    public function getAllData()
    {
        $user = $this->getUser();
        $email_list = $this->emailRepository->orderBy('id', 'desc')->findWhere([
            ['to', '=', $user->id],
            ['delete_receiver', '=', 0],
        ]);
        $sent_email_list = $this->emailRepository->orderBy('id', 'desc')->findWhere([
            ['from', '=', $user->id],
            ['delete_sender', '=', 0],
        ]);
        $users = $this->organizationRepository->getUserStaffCustomers()->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name.' ('.$user->email.')',
                ];
            })->values();

        $users_list = $this->organizationRepository->getUserStaffCustomers()->get()
            ->map(function ($user) {
                return [
                    'full_name' => $user->full_name,
                    'user_avatar' => $user->user_avatar,
                ];
            });
        $email_templates = $this->emailTemplateRepository->getAllForUser()
                ->map(function ($email) {
                    return [
                        'id' => $email->id,
                        'text' => $email->title.' ('.$email->id.') ',
                    ];
                })->toArray();

        $have_email_template = true;

        return response()->json(compact('email_list', 'sent_email_list', 'users', 'users_list', 'email_templates', 'have_email_template'), 200);
    }

    public function getMail($email)
    {
        $user = $this->getUser();
        $email = $this->emailRepository->with('receiver')->orderBy('id', 'desc')->find($email);
        if ($user->id != $email->to) {
            abort(403, trans('dashboard.unauthorized'));
        }
        $email->load('sender');
        $email->read = 1;
        $email->save();

        return response()->json(compact('email'), 200);
    }

    public function getMailTemplate($id)
    {
        $template = $this->emailTemplateRepository->find($id);

        return response()->json(compact('template'), 200);
    }

    public function sendEmail(MailboxRequest $request)
    {
        $user = $this->getUser();
        $message_return = '<div class="alert alert-danger">'.trans('mailbox.danger').'</div>';
        if (!empty($request->recipients)) {
            foreach ($request->recipients as $item) {
                if ('0' != $item && '' != $item) {

                    $request->merge(['to' => $item, 'from' => $user->id]);
                    $emails =$this->emailRepository->create($request->except('recipients', 'emailTemplate'));
                    $userFrom = $this->userRepository->getUser();
                    $userTo = $this->userRepository->find($item);

                    $organization = $this->userRepository->getOrganization();
                    $role= $this->organizationRolesRepository->getRole($organization,$userTo);
                    $site_email = config('settings.site_email');
                    if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($userTo->email)->send(new Mailbox([
                            'from' => $userFrom->email,
                            'subject' => $request->subject,
                            'message' => $request->message,
                            'userFrom' => $userFrom,
                            'emails' => $emails,
                            'role' => $role
                        ]));
                    }

                    $message_return = '<div class="alert alert-success">'.trans('mailbox.success').'</div>';
                }
            }
        }
        echo $message_return;
    }

    public function deleteMail($mail)
    {
        $mail = $this->emailRepository->find($mail);
        $user = $this->getUser();
        if ($mail->to == $user->id) {
            $mail->delete_receiver = 1;
        } else {
            $mail->delete_sender = 1;
        }
        $mail->save();
    }

    public function postRead(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $model = $this->emailRepository->find($request->get('id'));
        $model->read = true;
        $model->save();

        return response()->json(['message' => trans('mailbox.update_status')], 200);
    }

    public function getData()
    {
        $user = $this->getUser();

        $emails_list = $this->emailRepository->with('sender')
            ->orderBy('id', 'desc')
            ->findWhere([
                ['to', '=', $user->id],
                ['delete_receiver', '=', 0],
                ['read', '=', 0],
            ]);

        $total = $emails_list->count();
        $emails = collect($emails_list)->take(5);

        return response()->json(compact('total', 'emails'), 200);
    }

    public function postMarkAsRead(Request $request)
    {
        if ($ids = $request->get('ids')) {
            if (is_array($ids)) {
                $messages = $this->emailRepository->findWhereIn('id', $ids);
                foreach ($messages as $message) {
                    $message->read = true;
                    $message->save();
                }
            } else {
                $message = $this->emailRepository->find($ids);
                $message->read = true;
                $message->save();
            }
        }
    }

    public function getSent()
    {
        $user = $this->getUser();

        $sent = $this->emailRepository->with('receiver')->orderBy('id', 'desc')->findWhere([
            ['from', '=', $user->id],
            ['delete_sender', '=', 0],
        ]);

        return response()->json(compact('sent'), 200);
    }

    public function getSentMail($id)
    {
        $user = $this->getUser();
        $email = $this->emailRepository->with('receiver')->orderBy('id', 'desc')->find($id);
        if ($user->id != $email->from) {
            abort(403, trans('dashboard.unauthorized'));
        }

        return response()->json(compact('email'), 200);
    }

    public function getReceived(Request $request)
    {
        $user = $this->getUser();
        $received_list = $this->emailRepository->with('sender')->orderBy('id', 'desc')->findWhere([
            ['to', '=', $user->id],
            ['delete_receiver', '=', 0],
            ['subject', 'like', '%'.$request->get('query', '').'%'],
            ['message', 'like', '%'.$request->get('query', '').'%'],
        ]);
        $received = $received_list;

        $received_count = $received_list->count();

        return response()->json(compact('received', 'received_count'), 200);
    }

    public function postDelete(Request $request)
    {
        if ($ids = $request->get('ids')) {
            if (is_array($ids)) {
                $messages = $this->emailRepository->findWhereIn('id', $ids);
                foreach ($messages as $message) {
                    $message->delete_receiver = 1;
                    $message->save();
                }
            } else {
                $message = $this->emailRepository->find($ids);
                $message->delete_sender = 1;
                $message->save();
            }
        }
    }

    public function postReply($id, Request $request)
    {
        $orgMail = $this->emailRepository->find($id);

        $subject = ('Re:' === substr($orgMail->subject, 0, strlen('Re:')))
            ?
            ($orgMail->subject)
            :
            ('Re: '.$orgMail->subject);
        $request->merge([
            'subject' => $subject,
            'to' => $orgMail->from,
            'from' => $this->getUser()->id,
        ]);
        $emails = $this->emailRepository->create($request->all());
        $userTo = $this->userRepository->find($orgMail->from);
        $userFrom = $this->userRepository->find($orgMail->to);

        $organization = $this->userRepository->getOrganization();
        $role= $this->organizationRolesRepository->getRole($organization,$userTo);
        $site_email = config('settings.site_email');
        if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($userTo->email)->send(new Mailbox([
                'from' => $userFrom->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'userFrom' => $userFrom,
                'emails' => $emails,
                'role' => $role
            ]));
        }
    }
}
