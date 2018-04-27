<?php

namespace App\Http\Controllers\Api;

use App\Models\Option;
use App\Models\Setting;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class SettingsController extends Controller
{
    // Get all countries
    public function countries()
    {
        $countries= Country::orderBy("name", "asc")->pluck('name', 'id')->toArray();

        return response()->json(['countries' => $countries], 200);
    }

    // Get particular states of given country
    public function states(Request $request)
    {
        $states=State::where('country_id', $request->id)->orderBy("name", "asc")->pluck('name', 'id')->toArray();

        return response()->json(['states' => $states], 200);
    }

    // Get particular cities of given state
    public function cities(Request $request)
    {
        $cities=City::where('state_id', $request->id)->orderBy("name", "asc")->pluck('name', 'id')->toArray();

        return response()->json(['cities' => $cities], 200);

    }

    // settings
    public function settings()
    {
        $max_upload_file_size = array(
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
        );

        $currency = Option::where('category', 'currency')
            ->get()
            ->map(
                function ($title) {
                    return [
                        'text' => $title->title,
                        'id' => $title->value,
                    ];
                }
            )->pluck('text', 'id')->toArray();

        $backup_type = Option::where('category', 'backup_type')
            ->get()
            ->map(
                function ($title) {
                    return [
                        'text' => $title->value,
                        'id' => $title->title,
                    ];
                }
            );
        $settings = Settings::getAll();
        
        return response()->json(['settings'=>$settings,'max_upload_file_size' => $max_upload_file_size,'currency'=>$currency,
            'backup_type'=>$backup_type], 200);
    }

    public function updateSettings(Request $request)
    {
        if ($request->hasFile('site_logo_file') != "") {
            $file = $request->file('site_logo_file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $picture = Str::slug(substr($filename, 0, strrpos($filename, "."))) . '_' . time() . '.' . $extension;

            $destinationPath = public_path().'/uploads/site/';
            $file->move($destinationPath, $picture);
            $request->merge(['site_logo' => $picture]);
        }
        if ($request->hasFile('pdf_logo_file') != "") {
            $file = $request->file('pdf_logo_file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $picture = Str::slug(substr($filename, 0, strrpos($filename, "."))).'_'.time().'.'.$extension;

            $destinationPath = public_path().'/uploads/site/';
            $file->move($destinationPath, $picture);
            $request->merge(['pdf_logo' => $picture]);
        }

        Settings::set('modules', []);
        $request->date_format = $request->date_format_custom;
        $request->time_format = $request->time_format_custom;
        if ($request->date_format == "") {
            $request->date_format = 'd.m.Y';
        }
        if ($request->time_format == "") {
            $request->time_format = 'H:i';
        }
        $request->merge([
            'date_time_format' =>$request->date_format . ' ' . $request->time_format,
        ]);

        foreach ($request->except('_token', 'site_logo_file','pdf_logo_file', 'date_format_custom', 'time_format_custom', 'pages') as $key => $value) {
            Settings::set($key, $value);
        }
        return response()->json(['success' => 'success'], 200);

    }
}
