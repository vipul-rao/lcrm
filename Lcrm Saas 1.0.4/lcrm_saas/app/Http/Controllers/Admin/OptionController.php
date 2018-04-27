<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OptionRequest;
use App\Repositories\OptionRepository;
use DataTables;

class OptionController extends Controller
{
    private $categories;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * OptionController constructor.
     *
     * @param OptionRepository $optionRepository
     */
    public function __construct(OptionRepository $optionRepository)
    {
        parent::__construct();

        $this->categories = [
            'priority' => 'Priority',
            'titles' => 'Titles',
            'payment_methods' => 'Payment methods',
            'privacy' => 'Privacy',
            'show_times' => 'Show times',
            'stages' => 'Stages',
            'lost_reason' => 'Lost reason',
            'interval' => 'Interval',
            'currency' => 'Currency',
            'product_type' => 'Product type',
            'product_status' => 'Product status',
            'language' => 'Language'
        ];

        view()->share('type', 'admin/option');
        $this->optionRepository = $optionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('option.options');

        $this->generateParams();

        return View('admin.option.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('option.new');

        $this->generateParams();

        return view('admin.option.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OptionRequest $request)
    {
        $this->optionRepository->create($request->all());

        return redirect('admin/option');
    }


    public function edit($option)
    {
        $option = $this->optionRepository->find($option);
        $title = trans('option.edit');

        $this->generateParams();

        return view('admin.option.edit', compact('title', 'option'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OptionRequest $request, $option)
    {
        $option = $this->optionRepository->find($option);
        $option->update($request->all());

        return redirect('admin/option');
    }

    public function show($option)
    {
        $option = $this->optionRepository->find($option);
        $action = trans('action.show');
        $title = trans('option.show');

        return view('admin.option.show', compact('title', 'option', 'action'));
    }

    public function delete($option)
    {
        $option = $this->optionRepository->find($option);
        $title = trans('option.delete');

        return view('admin.option.delete', compact('title', 'option'));
    }


    public function destroy($option)
    {
        $option = $this->optionRepository->find($option);
        $option->delete();

        return redirect('admin/option');
    }

    /**
     * Get ajax datatables data.
     */
    public function data($category = '__')
    {
        $options = $this->optionRepository->getAll();

        if ('__' != $category) {
            $options = $options->where('category', $category);
        }
        $options = $options
            ->filter(function ($option) {
                return null === $option->user_id;
            })
            ->map(function ($option) {
                return [
                    'id' => $option->id,
                    'category' => $option->category,
                    'title' => $option->title,
                    'value' => $option->value,
                ];
            });

        return Datatables::of($options)
            ->addColumn('actions', '<a href="{{ url(\'admin/option/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     <a href="{{ url(\'admin/option/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i></a>
                                     <a href="{{ url(\'admin/option/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    private function generateParams()
    {
        view()->share('categories', $this->categories);
    }
}
