<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\QtemplateRequest;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QuotationTemplateRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;

class QtemplateController extends Controller
{
    /**
     * @var QuotationTemplateRepository
     */
    private $quotationTemplateRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    private $settingsRepository;

    private $organizationSettingsRepository;

    private $userRepository;

    public function __construct(QuotationTemplateRepository $quotationTemplateRepository,
                                ProductRepository $productRepository,
                                SettingsRepository $settingsRepository,
                                OrganizationSettingsRepository $organizationSettingsRepository,
                                UserRepository $userRepository)
    {
        parent::__construct();
        $this->quotationTemplateRepository = $quotationTemplateRepository;
        $this->productRepository = $productRepository;
        $this->settingsRepository = $settingsRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        $this->userRepository = $userRepository;

        view()->share('type', 'qtemplate');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->generateParams();
        $title = trans('qtemplate.qtemplates');

        return view('user.qtemplate.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        $title = trans('qtemplate.new');

        $this->generateParams();

        return view('user.qtemplate.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QtemplateRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(QtemplateRequest $request)
    {
        if ($request->quotation_duration==""){
            $request->merge(['quotation_duration'=>0]);
        }
        $this->quotationTemplateRepository->createQtemplate($request->all());

        return redirect('qtemplate');
    }

    public function edit($qtemplate)
    {
        $qtemplate = $this->quotationTemplateRepository->find($qtemplate);
        $title = trans('qtemplate.edit');

        $this->generateParams();

        return view('user.qtemplate.edit', compact('title', 'qtemplate'));
    }

    public function update(QtemplateRequest $request, $qtemplate)
    {
        if ($request->quotation_duration==""){
            $request->merge(['quotation_duration'=>0]);
        }
        $qtemplate_id = $qtemplate;
        $this->quotationTemplateRepository->updateQtemplate($request->all(),$qtemplate_id);
        return redirect('qtemplate');
    }

    public function delete($qtemplate)
    {
        $qtemplate = $this->quotationTemplateRepository->find($qtemplate);
        $title = trans('qtemplate.delete');

        return view('user.qtemplate.delete', compact('title', 'qtemplate'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($qtemplate)
    {
        $this->quotationTemplateRepository->deleteQtemplate($qtemplate);
        return redirect('qtemplate');
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $qtemplates = $this->quotationTemplateRepository->getAll()
            ->map(function ($qtemplates) {
                return [
                    'id' => $qtemplates->id,
                    'quotation_template' => $qtemplates->quotation_template,
                    'quotation_duration' => $qtemplates->quotation_duration,
                ];
            });

        return DataTables::of($qtemplates)
            ->addColumn('actions', '<a href="{{ url(\'qtemplate/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i>  </a>
                                     <a href="{{ url(\'qtemplate/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i></a>')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $products = $this->productRepository->orderBy('id', 'desc')->getAll();
        $sales_tax = $this->organizationSettingsRepository->getKey('sales_tax');
        view()->share('products', $products);
        view()->share('sales_tax', isset($sales_tax) ? floatval($sales_tax) : 1);
    }
}
