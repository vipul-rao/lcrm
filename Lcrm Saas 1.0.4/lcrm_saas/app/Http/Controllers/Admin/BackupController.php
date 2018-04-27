<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(

    ) {
        parent::__construct();

        view()->share('type', 'admin/backup');
    }

    public function index()
    {
        $title = trans('backup.backup');

        return view('admin.backup.index', compact('title'));
    }

    public function store()
    {
        Artisan::call('backup:run');
        flash(trans('backup.stored_successfully'), 'success');
        return redirect('admin/backup');
    }

    public function clean()
    {
        Artisan::call('backup:clean');
        flash(trans('backup.cleaned_successfully'), 'success');
        return redirect('admin/backup');
    }
}
