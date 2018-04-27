<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Repositories\MeetingRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;

class CalendarController extends Controller
{
    /**
     * @var QuotationRepository
     */
    private $quotationRepository;
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var OpportunityRepository
     */
    private $opportunityRepository;

    private $settingsRepository;

    private $userRepository;

    public function __construct(QuotationRepository $quotationRepository,
                                MeetingRepository $meetingRepository,
                                InvoiceRepository $invoiceRepository,
                                OpportunityRepository $opportunityRepository,
                                SettingsRepository $settingsRepository,
                                UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->quotationRepository = $quotationRepository;
        $this->meetingRepository = $meetingRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->opportunityRepository = $opportunityRepository;
        $this->settingsRepository = $settingsRepository;
        $this->userRepository = $userRepository;
    }

    public $events = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('calendar.calendar');

        return view('user.calendar.index', compact('title'));
    }

    public function events()
    {
        $quotations = $this->quotationRepository->getAll()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'title' => $quotation->quotations_number,
                    'start_date' => $quotation->date,
                    'end_date' => $quotation->exp_date,
                    'type' => 'quotation',
                ];
            });
        $this->add_events_to_list($quotations);
        $meetings = $this->meetingRepository->with('responsible')->getAll()
            ->filter(function ($meeting) {
                $user = $this->userRepository->getUser();
                return 'Everyone' == $meeting->privacy || ('Only me' == $meeting->privacy && $meeting->user_id==$user->id);
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
        $this->add_events_to_list($meetings);

        $invoices = $this->invoiceRepository->getAll()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'title' => $invoice->invoice_number,
                    'start_date' => $invoice->invoice_date,
                    'end_date' => $invoice->due_date,
                    'type' => 'invoice',
                ];
            });
        $this->add_events_to_list($invoices);

        $opportunities = $this->opportunityRepository->getAll()
            ->map(function ($opportunity) {
                return [
                    'id' => $opportunity->id,
                    'title' => $opportunity->opportunity,
                    'start_date' => $opportunity->next_action,
                    'end_date' => $opportunity->expected_closing,
                    'type' => 'opportunity',
                ];
            });
        $this->add_events_to_list($opportunities);

        return json_encode($this->events);
    }

    /**
     * @param $events_data
     */
    public function add_events_to_list($events_data)
    {
        foreach ($events_data as $d) {
            $event = [];
            $start_date = date('Y-m-d', (is_numeric($d['start_date']) ? $d['start_date'] : strtotime($d['start_date'])));
            $end_date = date('Y-m-d', (is_numeric($d['end_date']) ? $d['end_date'] : strtotime($d['end_date'].'+1 day')));
            $event['title'] = $d['title'];
            $event['id'] = $d['id'];
            $event['start'] = $start_date;
            $event['end'] = $end_date;
            $event['allDay'] = true;
            $event['description'] = $d['title'].'&nbsp;<a href="'.url($d['type'].'/'.$d['id'].'/edit').'" class="btn btn-sm btn-success"><i class="fa fa-pencil-square-o">&nbsp;</i></a>';
            array_push($this->events, $event);
        }
    }
}
