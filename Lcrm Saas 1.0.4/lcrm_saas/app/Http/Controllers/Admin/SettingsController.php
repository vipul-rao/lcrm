<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Repositories\OptionRepository;
use Illuminate\Http\Request;
use App\Repositories\SettingsRepository;

class SettingsController extends Controller
{
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $settingsRepository;

    /**
     * SettingsController constructor.
     *
     * @param OptionRepository $optionRepository
     */
    public function __construct(
        OptionRepository $optionRepository,
        SettingsRepository $settingsRepository
    ) {
        parent::__construct();
        view()->share('type', 'admin/setting');
        $this->optionRepository = $optionRepository;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('settings.settings');
        $max_upload_file_size = [
            '1000' => '1MB',
            '2000' => '2MB',
            '3000' => '3MB',
            '4000' => '4MB',
            '5000' => '5MB',
            '6000' => '6MB',
            '7000' => '7MB',
            '8000' => '8MB',
            '9000' => '9MB',
            '10000' => '10MB',
        ];

        $currency = $this->optionRepository->getAll()
            ->where('category', 'currency')
            ->map(
                function ($title) {
                    return [
                        'text' => $title->title,
                        'id' => $title->value,
                    ];
                }
            )->pluck('text', 'id')->toArray();

        $backup_type = $this->optionRepository->getAll()
            ->where('category', 'backup_type')
            ->map(
                function ($title) {
                    return [
                        'text' => $title->value,
                        'id' => $title->title,
                    ];
                }
            );

        $languages = $this->optionRepository->getAll()->where('category','language')->pluck('title','value');

        return view('admin.setting.index', compact('title', 'max_upload_file_size', 'currency', 'backup_type','languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request)
    {

        if ('' != $request->hasFile('site_logo_file')) {
            $file = $request->file('site_logo_file');
            $file = $this->settingsRepository->uploadLogo($file);

            $request->merge([
                'site_logo' => $file->getFileInfo()->getFilename(),
            ]);
            $this->settingsRepository->generateThumbnail($file);
        }

        $request->date_format = $request->date_format_custom;
        $request->time_format = $request->time_format_custom;
        if ('' == $request->date_format) {
            $request->date_format = 'd-m-Y';
        }
        if ('' == $request->time_format) {
            $request->time_format = 'H:i';
        }
        $request->merge([
            'date_time_format' => $request->date_format.' '.$request->time_format,
        ]);
        foreach ($request->except('_token', 'site_logo_file', 'date_format_custom', 'time_format_custom') as $key => $value) {
            $this->settingsRepository->setKey($key, $value);
        }

        return redirect()->back();
    }
}
