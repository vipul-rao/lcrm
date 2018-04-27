<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesteamRequest;
use App\Repositories\ExcelRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\Request;
use DataTables;

class SalesteamController extends Controller
{
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;
    /**
     * @var ExcelRepository
     */
    private $excelRepository;

    protected $user;

    private $invoiceRepository;

    /**
     * @param SalesTeamRepository    $salesTeamRepository
     * @param OrganizationRepository $organizationRepository
     * @param ExcelRepository        $excelRepository
     */
    public function __construct(
        SalesTeamRepository $salesTeamRepository,
        OrganizationRepository $organizationRepository,
        ExcelRepository $excelRepository,
        InvoiceRepository $invoiceRepository
    ) {
        parent::__construct();

        $this->salesTeamRepository = $salesTeamRepository;
        $this->organizationRepository = $organizationRepository;
        $this->excelRepository = $excelRepository;
        $this->invoiceRepository = $invoiceRepository;

        view()->share('type', 'salesteam');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_team.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('salesteam.salesteams');

        $graphics = [];
        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $salesteam = $this->salesTeamRepository->getMonthYear($monthno, $year);
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'salesteams' => $salesteam->count(),
                'invoice_target' => round($salesteam->sum('invoice_target'),3),
                'actual_invoice' => $this->invoiceRepository->getMonthYear($monthno,$year)->sum('final_price'),
            ];
        }
        return view('user.salesteam.index', compact('title','graphics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('salesteam.new');

        return view('user.salesteam.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SalesteamRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $this->salesTeamRepository->createTeam($request->all());

        return redirect('salesteam');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($salesteam)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('salesteam.edit');
        $salesteam = $this->salesTeamRepository->findTeam($salesteam);

        return view('user.salesteam.edit', compact('title', 'salesteam', 'salesteam_stafs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SalesteamRequest $request, $salesteam)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $salesteam_id = $salesteam;
        $request->merge([
            'quotations' => isset($request->quotations) ? 1 : 0,
            'leads' => isset($request->leads) ? 1 : 0,
            'opportunities' => isset($request->opportunities) ? 1 : 0,
        ]);
        $this->salesTeamRepository->updateTeam($request->all(), $salesteam_id);

        return redirect('salesteam');
    }

    public function show($salesteam)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $salesteam = $this->salesTeamRepository->findTeam($salesteam);
        $title = trans('salesteam.show');
        $action = trans('action.show');

        return view('user.salesteam.show', compact('title', 'salesteam', 'action'));
    }

    public function delete($salesteam)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $salesteam = $this->salesTeamRepository->findTeam($salesteam);
        $title = trans('salesteam.delete');

        return view('user.salesteam.delete', compact('title', 'salesteam'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($salesteam)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_team.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $this->salesTeamRepository->deleteTeam($salesteam);

        return redirect('salesteam');
    }

    public function data()
    {
        $orgRole = $this->getUser()->orgRole;
        $salesteam = $this->salesTeamRepository->getAll()
            ->map(function ($salesteam) use ($orgRole){
                return [
                    'id' => $salesteam->id,
                    'salesteam' => $salesteam->salesteam,
                    'target' => $salesteam->invoice_target,
                    'invoice_forecast' => $salesteam->invoice_forecast,
                    'actual_invoice' => $salesteam->actualInvoice->sum('grand_total'),
                    'orgRole' => $orgRole,
                ];
            });

        return DataTables::of($salesteam)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'sales_team.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'salesteam/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_team.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'salesteam/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_team.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'salesteam/\' . $id . \'/delete\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $staff = $this->organizationRepository->getStaff()->get()->pluck('full_name', 'id')->prepend(trans('salesteam.team_leader'), '');
        view()->share('staff', $staff);
    }

    public function downloadExcelTemplate()
    {
        ob_end_clean();
        $path = base_path('resources/excel-templates/sales-teams.xlsx');

        if (file_exists($path)) {
            return response()->download($path);
        }

        return 'File not found!';
    }

    public function getImport()
    {
        $title = trans('salesteam.salesteams');

        return view('user.salesteam.import', compact('title'));
    }

    public function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv|max:5000',
        ]);

        $reader = $this->excelRepository->load($request->file('file'));
        $data = [
            'salesteams' => $reader->all(),
            'staff' => $this->organizationRepository->getStaff()->get()->map(function ($user) {
                return [
                    'text' => $user->full_name,
                    'id' => $user->id,
                ];
            })->values(),
        ];
        return response()->json(compact('data'), 200);
    }

    public function postAjaxStore(SalesteamRequest $request)
    {
        $this->salesTeamRepository->createTeam($request->except('created', 'errors', 'selected'));

        return response()->json([], 200);
    }
}
