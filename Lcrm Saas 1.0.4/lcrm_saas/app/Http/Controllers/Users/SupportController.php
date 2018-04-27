<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportRequest;
use App\Mail\CreateTicket;
use App\Mail\ReplyTicket;
use App\Repositories\EmailTemplateRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\SupportRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Repositories\ReplyRepository;

class SupportController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EmailTemplateRepository
     */
    private $emailTemplateRepository;
    private $supportRepository;
    private $organizationRepository;
    private $replyRepository;
    private $organizationRolesRepository;

    public function __construct(
        UserRepository $userRepository,
        EmailTemplateRepository $emailTemplateRepository,
        SupportRepository $supportRepository,
        OrganizationRepository $organizationRepository,
        ReplyRepository $replyRepository,
        OrganizationRolesRepository $organizationRolesRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->supportRepository = $supportRepository;
        $this->organizationRepository = $organizationRepository;
        $this->replyRepository = $replyRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;

        view()->share('type', 'support');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('mailbox.support');

        return view('user.support.index', compact('title'));
    }

    public function getAllData()
    {
        $user = $this->getUser();

        return response()->json(compact('user'), 200);
    }

    public function getTicket($id)
    {
        $user = $this->userRepository->getUser();
        $ticket = $this->supportRepository->with(['creator', 'replies.creator'])->find($id);
        if ($user->id != $ticket->user_id) {
            abort(403, trans('dashboard.unauthorized'));
        }

        return response()->json(compact('ticket'), 200);
    }

    public function getMailTemplate($id)
    {
        $template = $this->emailTemplateRepository->find($id);

        return response()->json(compact('template'), 200);
    }

    public function createTicket(SupportRequest $request)
    {
        $user = $this->getUser();

        try {
            $request->merge(['user_id' => $user->id]);

            $tickets = $this->supportRepository->create($request->except('recipients'));

            $userTo = $this->userRepository->findRoleBySlug('admin')->users()->first();
            $site_email = config('settings.site_email');

            if (false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($userTo->email)->send(new CreateTicket([
                    'from' => $user->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'userFrom' => $user,
                    'tickets' => $tickets
                ]));
            }
            return response(trans('mailbox.success'), 200);
        } catch (\Exception $e) {
        }

        return response(trans('mailbox.danger'), 500);
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
        $emails_list = $this->supportRepository->with('sender')
            ->orderBy('id', 'desc')->findWhere([
                ['read', 0],
                ['delete_receiver', 0],
            ]);

        $total = $emails_list->count();
        $emails = $emails_list->latest()->take(5)->get();

        return response()->json(compact('total', 'emails'), 200);
    }

    public function markAsSolved($id)
    {
        $user = $this->userRepository->getUser();
        $ticket = $this->supportRepository->find($id);
        if ($user->id != $ticket->user_id) {
            abort(403, trans('dashboard.unauthorized'));
        }
        $ticket->status = 'closed';
        $ticket->save();

        return response()->json(compact('ticket'), 200);
    }

    public function postMarkAsSolved(Request $request)
    {
        if ($request->ids) {
            $ids = $request->ids;
            if (is_array($ids)) {
                $messages = $this->supportRepository->markAsSolved($ids);

                return 'success';
            }
        }

        return;
    }

    public function getSent()
    {
        $user = $this->getUser();

        $sent = $this->supportRepository->with('receiver')->orderBy('id', 'desc')->findWhere([
            ['from', '=', $user->id],
            ['delete_sender', '=', 0],
        ]);

        return response()->json(compact('sent'), 200);
    }

    public function getSentMail($id)
    {
        $user = $this->getUser();
        $email = $this->supportRepository->with('receiver')->orderBy('id', 'desc')->find($id);
        if ($user->id != $email->from) {
            abort(403, trans('dashboard.unauthorized'));
        }

        return response()->json(compact('email'), 200);
    }

    public function getTickets(Request $request)
    {
        $user = $this->getUser();
        $received = $this->supportRepository->with('creator')->orderBy('id', 'desc')->findWhere([
            ['user_id', '=', $user->id],
            ['status', '=', 'open'],
        ])->makeHidden('message', 'updated_at');
        $received_count = $received->count();

        return response()->json(compact('received', 'received_count'), 200);
    }

    public function getClosedTickets(Request $request)
    {
        $user = $this->getUser();
        $received = $this->supportRepository->with('creator')->orderBy('id', 'desc')->findWhere([
            ['user_id', '=', $user->id],
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
        if ($this->getUser()->id != $orgTicket->user_id) {
            abort(403, 'Unauthorized access');
        }
        $request->merge([
            'user_id' => $this->getUser()->id,
            'ticket_id' => $orgTicket->id,
        ]);
        $this->replyRepository->create($request->all());

        $user = $this->getUser();
        $userTo = $this->userRepository->findRoleBySlug('admin')->users()->first();
        $site_email = config('settings.site_email');

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
