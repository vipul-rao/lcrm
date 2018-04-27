<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationSettingRequest;
use App\Repositories\CompanySettingsRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Mpociot\VatCalculator\Facades\VatCalculator;

class SettingsController extends Controller
{
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    /**
     * @var OrganizationSettingsRepository
     */
    private $organizationSettingsRepository;
    private $organizationRepository;
    private $settingsRepository;
    private $companySettingsRepository;

    /**
     * SettingsController constructor.
     *
     * @param OptionRepository               $optionRepository
     * @param OrganizationSettingsRepository $OrganizationSettingsRepository
     */
    public function __construct(
        OptionRepository $optionRepository,
        OrganizationSettingsRepository $organizationSettingsRepository,
        CompanySettingsRepository $companySettingsRepository,
        OrganizationRepository $organizationRepository,
        SettingsRepository $settingsRepository
    )
    {
        parent::__construct();

        view()->share('type', 'customers/setting');

        $this->optionRepository = $optionRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        $this->organizationRepository = $organizationRepository;
        $this->settingsRepository = $settingsRepository;
        $this->companySettingsRepository = $companySettingsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('settings.settings');
        $europian_tax = $this->organizationSettingsRepository->getKey('europian_tax');
        $companySettings = $this->companySettingsRepository->getAll();

        return view('customers.setting.index', compact('title','europian_tax','companySettings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrganizationSettingRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     *
     * @internal param int $id
     */
    public function update(Request $request)
    {
        $validVAT = VatCalculator::isValidVATNumber($request->vat_number);
        $validVAT = json_decode($validVAT, true);
        $europian_tax = $this->organizationSettingsRepository->getKey('europian_tax');
        if ($europian_tax =='true' && $request->vat_number!=''){
            if (!isset($validVAT)){
                flash('Vat Number is not valid.')->error();
                return redirect()->back();
            }
        }
        $orgRole = $this->getUser()->orgRole;
        if ('customer' != $orgRole) {
            return redirect('dashboard');
        }
        $this->user = $this->getUser();

        foreach ($request->except('_token') as $key => $value) {
            $this->companySettingsRepository->setKey($key, $value);
        }

        return redirect()->back();
    }
}
