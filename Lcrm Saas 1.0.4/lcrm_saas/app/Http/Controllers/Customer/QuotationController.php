<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use DataTables;

class QuotationController extends Controller
{

    private $userRepository;

    private $quotationRepository;

    private $organizationRepository;

    private $customerRepository;

    public function __construct(
        QuotationRepository $quotationRepository,
        UserRepository $userRepository,
        OrganizationRepository $organizationRepository,
        CustomerRepository $customerRepository
    ) {
        parent::__construct();

        $this->quotationRepository = $quotationRepository;
        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;
        view()->share('type', 'customers/quotation');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('quotation.quotations');
        $this->generateParams();

        return view('customers.quotation.index', compact('title'));
    }


    public function show($quotation)
    {
        $quotation = $this->quotationRepository->getAll()->find($quotation);
        $title = trans('quotation.show');
        return view('customers.quotation.show', compact('title', 'quotation'));
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $company_id =$this->getUser()->customer->company->id;
        $dateFormat = config('settings.date_format');
        $quotations = $this->quotationRepository->getAll()->where('company_id',$company_id)
            ->map(function ($quotation) use ($dateFormat) {
                    return [
                        'id' => $quotation->id,
                        'quotations_number' => $quotation->quotations_number,
                        'company_id' => $quotation->companies->name ?? null,
                        'sales_team_id' => $quotation->salesTeam->salesteam ?? null,
                        'date' => date($dateFormat, strtotime($quotation->date)),
                        'exp_date' => date($dateFormat, strtotime($quotation->exp_date)),
                        'final_price' => $quotation->final_price,
                        'payment_term' => $quotation->payment_term,
                        'status' => $quotation->status
                    ];
                }
            );

        return DataTables::of($quotations)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'quotation.quotation_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'quotation.quotation_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn('actions', '<a href="{{ url(\'customers/quotation/\' . $id . \'/show\' ) }}"  title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i>  </a>
                                            @if($status == \'Send Quotation\' 
                                             && strtotime(date("m/d/Y"))<= strtotime("+".$payment_term." ",strtotime($exp_date)) )
                                            <a href="{{ url(\'customers/quotation/\' . $id . \'/accept_quotation\' ) }}" title="{{ trans(\'quotation.accept_quotation\') }}">
                                            <i class="fa fa-fw fa-check text-primary"></i> </a>
                                            <a href="{{ url(\'customers/quotation/\' . $id . \'/reject_quotation\' ) }}" title="{{ trans(\'quotation.reject_quotation\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                    @endif
                                         
                                     <a href="{{ url(\'customers/quotation/\' . $id . \'/print_quot\' ) }}"  title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-warning"></i>  </a>')
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->make();
    }

    public function printQuot($quotation)
    {
        $quotation = $this->quotationRepository->find($quotation);
        $quotation_template = config('settings.quotation_template');
        $this->generateParams();
        $filename = trans('quotation.quotation').'-'.$quotation->quotations_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('quotation_template.'.$quotation_template, compact('quotation'));

        return $pdf->download($filename.'.pdf');
    }

    public function ajaxCreatePdf($quotation)
    {
        $quotation = $this->quotationRepository->find($quotation);
        $quotation_template = config('settings.quotation_template');
        $filename = trans('quotation.quotation').'-'.$quotation->quotations_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('quotation_template.'.$quotation_template, compact('quotation'));
        $pdf->save('./pdf/'.$filename.'.pdf');
        $pdf->stream();
        echo url('pdf/'.$filename.'.pdf');
    }

    public function generateParams()
    {

        view()->share('type', 'customers/quotation');
    }

    function acceptQuotation($quotation){
        $quotation = $this->quotationRepository->find($quotation);
        $quotation->update(['status' => trans('quotation.quotation_accepted')]);
        return redirect('customers/quotation')->with('success_message',trans('quotation.quotation_accepted'));
    }
    function rejectQuotation($quotation){
        $quotation = $this->quotationRepository->find($quotation);
        $quotation->update(['status' => trans('quotation.quotation_rejected')]);
        return redirect('customers/quotation')->with('quotation_rejected',trans('quotation.quotation_rejected'));
    }
}
