<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailboxRequest;
use App\Mail\Mailbox;
use App\Mail\ReplyTicket;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\ReplyRepository;
use App\Repositories\SupportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    private $userRepository;
    private $emailTemplateRepository;
    private $supportRepository;
    private $settingsRepository;
    private $replyRepository;

    public function __construct(
        UserRepository $userRepository,
        EmailTemplateRepository $emailTemplateRepository,
        SettingsRepository $settingsRepository,
        SupportRepository $supportRepository,
        ReplyRepository $replyRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->supportRepository = $supportRepository;
        $this->settingsRepository = $settingsRepository;
        $this->replyRepository = $replyRepository;
        view()->share('type', 'support');
    }

    public function index()
    {
        $title = trans('mailbox.support');

        return view('admin.support.index', compact('title'));
    }

    public function getAllData()
    {
        $email_list = $this->supportRepository->orderBy('id', 'desc')->findWhere([
            ['delete_receiver', '=', 0],
        ]);
        $sent_email_list = $this->supportRepository->orderBy('id', 'desc')->findWhere([
            ['delete_sender', '=', 0],
        ]);

        $users = $this->userRepository->getUsers()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name.' ('.$user->email.')',
                ];
            })->values();

        $users_list = $this->userRepository->getUsers()
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

    public function getMail($id)
    {
        $ticket = $this->supportRepository->with(['creator', 'replies.creator'])->find($id);

        return response()->json(compact('ticket'), 200);
    }

    public function getMailTemplate($id)
    {
        $template = $this->emailTemplateRepository->find($id);

        return response()->json(compact('template'), 200);
    }

    public function sendEmail(MailboxRequest $request)
    {
        $user = $this->userRepository->getUser();
        $message_return = '<div class="alert alert-danger">'.trans('mailbox.danger').'</div>';
        if (!empty($request->recipients)) {
            foreach ($request->recipients as $item) {
                if ('0' != $item && '' != $item) {
                    $request->merge(['to' => $item, 'from' => $user->id]);
                    $this->supportRepository->create($request->except('recipients', 'emailTemplate'));
                    $userTo = $this->userRepository->find($item);
                    $site_email = $this->settingsRepository->getKey('site_email');
                    if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($userTo->email)->send(new Mailbox([
                            'from' => $user->email,
                            'subject' => $request->subject,
                            'message' => $request->message,
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
        $mail = $this->supportRepository->find($mail);
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

        $model = $this->supportRepository->find($request->get('id'));
        $model->read = true;
        $model->save();

        return response()->json(['message' => trans('mailbox.update_status')], 200);
    }

    public function getData()
    {
        $tickets_list = $this->supportRepository->with('creator')
            ->orderBy('id', 'desc')->findWhere([
                ['status', '=', 'open'],
            ]);

        $total = $tickets_list->count();
        $tickets = collect($tickets_list)->take(4);

        return response()->json(compact('total', 'tickets'), 200);
    }

    public function markAsSolved($id)
    {
        $ticket = $this->supportRepository->find($id);

        $ticket->status = 'closed';
        $ticket->save();

        return response()->json(compact('ticket'), 200);
    }

    public function postMarkAsSolved(Request $request)
    {
        if ($request->ids) {
            $ids = $request->ids;
            if (is_array($ids)) {
                $messages = $this->supportRepository->markAsSolvedByAdmin($ids);

                return 'success';
            }
        }

        return;
    }

    public function getSent()
    {
        $user = $this->userRepository->getUser();

        $sent = $this->supportRepository->with('receiver')->orderBy('id', 'desc')->findWhere([
            ['from', '=', $user->id],
            ['delete_sender', '=', 0],
        ]);

        return response()->json(compact('sent'), 200);
    }

    public function getSentMail($id)
    {
        $user = $this->userRepository->getUser();
        $email = $this->supportRepository->with('sender')->find($id);
        if ($user->id != $email->from) {
            abort(403, trans('dashboard.unauthorized'));
        }
        $email = $this->supportRepository->with('receiver')->orderBy('id', 'desc')->find($id);

        return response()->json(compact('email'), 200);
    }

    public function getTickets(Request $request)
    {
        $received = $this->supportRepository->with('creator')->orderBy('id', 'desc')->findWhere([
            ['status', '=', 'open'],
        ])->makeHidden('message', 'updated_at');
        $received_count = $received->count();

        return response()->json(compact('received', 'received_count'), 200);
    }

    public function getClosedTickets(Request $request)
    {
        $received = $this->supportRepository->with('creator')->orderBy('id', 'desc')->findWhere([
            ['status', '=', 'closed'],
        ])->makeHidden('message', 'updated_at');
        $received_count = $received->count();

        return response()->json(compact('received', 'received_count'), 200);
    }

    public function postDelete(Request $request)
    {
        if ($ids = $request->get('ids')) {
            if (is_array($ids)) {
                $messages = $this->supportRepository->findWhereIn('id', $ids);
                foreach ($messages as $message) {
                    $message->delete_receiver = 1;
                    $message->save();
                }
            } else {
                $message = $this->supportRepository->find($ids);
                $message->delete_sender = 1;
                $message->save();
            }
        }
    }

    public function postComment($id, Request $request)
    {
        $orgTicket = $this->supportRepository->find($id);

        $request->merge([
            'user_id' => $this->userRepository->getUser()->id,
            'ticket_id' => $orgTicket->id,
        ]);
        $this->replyRepository->create($request->all());

        $user = $this->userRepository->find($request->user_id);
        $userTo = $this->userRepository->find($orgTicket->user_id);
        $site_email = $this->settingsRepository->getKey('site_email');
        if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($userTo->email)->send(new ReplyTicket([
                'from' => $user->email,
                'subject' => $orgTicket->subject,
                'message' => $request->comment,
                'userFrom' => $user,
                'tickets' => $orgTicket
            ]));
        }

        return 'success';
    }
}
