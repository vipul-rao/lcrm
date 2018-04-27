<?php

namespace App\Helpers;

use App;
use App\Repositories\OrganizationRolesRepositoryEloquent;
use App\Repositories\OrganizationSettingsRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\SettingsRepositoryEloquent;
use App\Repositories\OrganizationRepositoryEloquent;
use Mpociot\VatCalculator\Facades\VatCalculator;

class InitialGenerators
{
    private $userRepository;
    private $settingsRepository;
    private $organizationRepository;
    private $organizationRolesRepository;
    private $organizationSettingsRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepositoryEloquent(app());

        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        $this->organizationRepository = new OrganizationRepositoryEloquent(app());

        $this->organizationRolesRepository = new OrganizationRolesRepositoryEloquent(app());

        $this->organizationSettingsRepository = new OrganizationSettingsRepositoryEloquent(app());
    }

    public function generateData()
    {
        $user = $this->userRepository->getUser();

        $organization = $this->userRepository->getOrganization();
        $menu_role = '';

        if ($this->userRepository->inRole('admin')) {
            $menu_role = 'admin';
            $countryCode = VatCalculator::getIPBasedCountry();
            $this->settingsRepository->setKey('country_code',$countryCode);
            $language = $this->settingsRepository->getKey('language');
            App::setLocale($language??'en');
        }
        $settings = $this->settingsRepository->getAll();
        $settings['site_logo'] = isset($settings['site_logo']) ? 'uploads/site/thumb_'.$settings['site_logo'] : 'uploads/site/logo.png';
        $settings['app_logo'] = $settings['site_logo'] ?? '';
        config(['app.name' => $settings['site_name'] ?? '']);
        config(['settings.site_email' => $settings['site_email'] ?? '']);
        config(['settings.date_format' => $settings['date_format'] ?? 'd-m-Y']);
        config(['settings.date_time_format' => $settings['date_time_format'] ?? 'd-m-Y H:i']);
        config(['settings.country_code' => $settings['country_code'] ?? '']);
        if (!$this->userRepository->inRole('admin') && $organization) {
            $settings['site_logo'] = isset($organization->logo) ? 'uploads/organizations/thumb_'.$organization->logo : 'uploads/site/logo.png';

            $settings['site_name'] = $organization->name;

            $countryCode = VatCalculator::getIPBasedCountry();
            $this->organizationSettingsRepository->setKey('country_code',$countryCode);
            $organizationSettings = $this->organizationSettingsRepository->getAll();
            $language = $organizationSettings['language']??'en';
            App::setLocale($language);
            config(['settings.country_code' => $settings['country_code'] ?? '']);
            config(['settings.address1' => $organizationSettings['address1'] ?? '']);
            config(['settings.address2' => $organizationSettings['address2'] ?? '']);
            config(['settings.phone' => $organizationSettings['phone'] ?? '']);
            config(['settings.fax' => $organizationSettings['fax'] ?? '']);
            config(['settings.site_email' => $organizationSettings['site_email'] ?? '']);

            config(['settings.date_format' => $organizationSettings['date_format'] ?? 'd-m-Y']);
            config(['settings.time_format' => $organizationSettings['time_format'] ?? 'H:i']);
            config(['settings.date_time_format' => $organizationSettings['date_time_format'] ?? 'd-m-Y H:i']);

            config(['settings.payment_term1' => $organizationSettings['payment_term1'] ?? '7']);
            config(['settings.payment_term2' => $organizationSettings['payment_term2'] ?? '15']);
            config(['settings.payment_term3' => $organizationSettings['payment_term3'] ?? '30']);

            config(['settings.quotation_start_number' => $organizationSettings['quotation_start_number'] ?? '1']);
            config(['settings.quotation_prefix' => $organizationSettings['quotation_prefix'] ?? 'Q_']);
            config(['settings.quotation_template' => $organizationSettings['quotation_template'] ?? 'quotation_blue']);

            config(['settings.sales_start_number' => $organizationSettings['sales_start_number'] ?? '1']);
            config(['settings.sales_prefix' => $organizationSettings['sales_prefix'] ?? 'S_']);
            config(['settings.saleorder_template' => $organizationSettings['saleorder_template'] ?? 'saleorder_blue']);

            config(['settings.invoice_start_number' => $organizationSettings['invoice_start_number'] ?? '1']);
            config(['settings.invoice_prefix' => $organizationSettings['invoice_prefix'] ?? 'I_']);
            config(['settings.invoice_template' => $organizationSettings['invoice_template'] ?? 'invoice_blue']);

            $orgRole = $this->organizationRolesRepository->getRole($organization, $user);
            if ('admin' == $orgRole || 'staff' == $orgRole) {
                $menu_role = 'user';
            } elseif ('customer' == $orgRole) {
                $menu_role = 'customer';
                $countryCode = $this->organizationSettingsRepository->getKey('country_code');
                config(['settings.country_code' => $countryCode ?? '']);
            }
            view()->share('orgRole', $orgRole);
            view()->share('organizationSettings', $organizationSettings);
        }
        config(['app.name' => $settings['site_name'] ?? null]);
        view()->share('settings', $settings);
        view()->share('user', $user);
        view()->share('organization', $organization);
        view()->share('menu_role', $menu_role);

        return true;
    }
}
