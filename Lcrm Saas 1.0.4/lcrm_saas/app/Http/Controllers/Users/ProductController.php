<?php

namespace App\Http\Controllers\Users;

use App\Helpers\Thumbnail;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ExcelRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ExcelRepository
     */
    private $excelRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $userRepository;

    private $organizationRepository;

    protected $user;

    /**
     * @param ProductRepository  $productRepository
     * @param CategoryRepository $categoryRepository
     * @param ExcelRepository    $excelRepository
     * @param OptionRepository   $optionRepository
     */
    public function __construct(ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                ExcelRepository $excelRepository,
                                OptionRepository $optionRepository,
                                UserRepository $userRepository,
                                OrganizationRepository $organizationRepository)
    {
        parent::__construct();

        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->excelRepository = $excelRepository;
        $this->optionRepository = $optionRepository;
        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;

        view()->share('type', 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['products.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('product.products');

        $statuses = $this->optionRepository->findByField('category','product_status')
            ->map(function ($title) {
                return [
                    'title' => $title->title,
                    'value'   => $title->value,
                ];
            })->toArray();
        $colors = ['#3295ff','#2daf57','#fc4141','#fcb410','#17a2b8','#3295ff','#2daf57','#fc4141','#fcb410','#17a2b8'];
        foreach ($statuses as $key=>$status){
            $statuses[$key]['color'] = isset($colors[$key])?$colors[$key]:"";
            $statuses[$key]['products'] = $this->productRepository->getAll()->where('status', $status['value'])->count();
        }

        $graphics = [];

        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'products' => $this->productRepository->getMonthYear($monthno, $year)->count(),
            ];
        }

        return view('user.product.index', compact('title','statuses','graphics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['products.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('product.new');

        return view('user.product.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['products.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id' => $user->id, 'organization_id' => $organization->id]);

        if ($request->hasFile('product_image_file')) {
            $file = $request->file('product_image_file');
            $file = $this->productRepository->uploadProductImage($file);

            $request->merge([
                'product_image' => $file->getFileInfo()->getFilename(),
            ]);

            $this->generateProductThumbnail($file);
        }
        $this->productRepository->create($request->except('product_image_file'));

        return redirect('product');
    }

    public function edit($product)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['products.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $product = $this->productRepository->find($product);
        $title = trans('product.edit');

        return view('user.product.edit', compact('title', 'product'));
    }

    public function update(ProductRequest $request, $product)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['products.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $product = $this->productRepository->find($product);
        if ($request->hasFile('product_image_file')) {
            $file = $request->file('product_image_file');
            $file = $this->productRepository->uploadProductImage($file);

            $request->merge([
                'product_image' => $file->getFileInfo()->getFilename(),
            ]);

            $this->generateProductThumbnail($file);
        }

        $product->update($request->except('product_image_file'));

        return redirect('product');
    }

    public function show($product)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['products.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $product = $this->productRepository->find($product);
        $action = trans('action.show');
        $title = trans('product.details');

        return view('user.product.show', compact('title', 'product', 'action'));
    }

    public function delete($product)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['products.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $product = $this->productRepository->find($product);
        $title = trans('product.delete');

        return view('user.product.delete', compact('title', 'product'));
    }

    public function destroy($product)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['products.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $product = $this->productRepository->find($product);
        $product->delete();

        return redirect('product');
    }

    public function data()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['products.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $products = $this->productRepository
            ->getAll()
            ->map(function ($product) use ($orgRole){
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'name' => isset($product->category) ? $product->category->name : '',
                    'product_type' => $product->product_type,
                    'status' => $product->status,
                    'quantity_on_hand' => $product->quantity_on_hand,
                    'quantity_available' => $product->quantity_available,
                    'orgRole' => $orgRole,
                    'count_uses' => $product->qtemplates->count(),
                ];
            });

        return DataTables::of($products)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'products.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'product/\' . $id . \'/edit\' ) }}"  title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i> </a>
                                     @endif
                                     <a href="{{ url(\'product/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i></a>
                                     @if((Sentinel::getUser()->hasAccess([\'products.delete\']) || $orgRole=="admin") && $count_uses==0)
                                        <a href="{{ url(\'product/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif')
            ->removeColumn('id')
            ->removeColumn('count_uses')
            ->rawColumns(['actions'])
            ->make();
    }

    /**
     * @param $file
     */
    private function generateProductThumbnail($file)
    {
        $sourcePath = $file->getPath().'/'.$file->getFilename();
        $thumbPath = $file->getPath().'/thumb_'.$file->getFilename();
        Thumbnail::generate_image_thumbnail($sourcePath, $thumbPath);
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $statuses = $this->optionRepository->getAll()
            ->where('category', 'product_status')->pluck('title', 'value')->prepend(trans('product.status'), '');

        $product_types = $this->optionRepository->getAll()
            ->where('category', 'product_type')->pluck('title', 'value')->prepend(trans('product.product_type'), '');

        $categories = $this->categoryRepository->orderBy('id', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('product.category_id'), '');

        view()->share('statuses', $statuses);
        view()->share('product_types', $product_types);
        view()->share('categories', $categories);
    }

    public function getImport()
    {
        $title = trans('product.import');

        return view('user.product.import', compact('title'));
    }

    public function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls,csv|max:5000',
        ]);

        $reader = $this->excelRepository->load($request->file('file'));

        $data = [
            'salesteams' => $reader->all()->map(function ($product) {
                return [
                    'product_name' => $product->product_name,
                    'product_type' => $product->product_type,
                    'status' => $product->status,
                    'quantity_on_hand' => $product->quantity_on_hand,
                    'quantity_available' => $product->quantity_available,
                    'sale_price' => $product->sale_price,
                    'description' => $product->description,
                    'description_for_quotations' => $product->description_for_quotations,
                    'variants' => $this->getProductVariants($product->variants),
                ];
            }),

            'staff' => $this->organizationRepository->getStaff()->get()->map(function ($user) {
                return [
                    'text' => $user->full_name,
                    'id' => $user->id,
                ];
            })->values(),
        ];
        $categories = $this->categoryRepository->orderBy('id', 'asc')->getAll()
            ->map(function ($category) {
                return [
                    'title' => $category->name,
                    'id' => $category->id,
                ];
            });
        return response()->json(compact('data','categories'), 200);
    }

    public function postAjaxStore(ProductRequest $request)
    {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $request->merge(['user_id' => $user->id, 'organization_id' => $organization->id]);
        $this->productRepository->create($request->except('created', 'errors', 'selected','variants'));

        return response()->json([], 200);
    }

    public function downloadExcelTemplate()
    {
        ob_end_clean();
        $path = base_path('resources/excel-templates/products.xlsx');

        if (file_exists($path)) {
            return response()->download($path);
        }

        return 'File not found!';
    }

    private function getProductVariants($variants = [])
    {
        if (isset($variants)) {
            $variants = array_map(
                function ($v) {
                    return explode(':', $v);
                },
                explode(',', $variants)
            );
        }

        return $variants;
    }
}
