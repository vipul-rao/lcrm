<?php

namespace App\Repositories;

use App\Models\CompanySetting;
use App\Models\OrganizationSetting;
use Illuminate\Support\Facades\Cache;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class CompanySettingsRepositoryEloquent extends BaseRepository implements CompanySettingsRepository
{
    private $organization;

    private $userRepository;

    private $organizationRepository;
    private $company;

    public function getCompany()
    {
        $this->userRepository = new UserRepositoryEloquent(app());

        $this->organizationRepository = new OrganizationRepositoryEloquent(app());
        $this->organization = $this->userRepository->getOrganization();
        $this->company = $this->userRepository->getUser()->customer->company;
    }

    /**
     * Specify Model class name.
     */
    public function model()
    {
        return CompanySetting::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getAll()
    {
        $this->getCompany();

        $values = $this->findByField('company_id', $this->company->id)->pluck('value', 'key');

        return $values;
    }

    public function getKey($key, $default = null)
    {
        $values = $this->getAll();

        return $values[$key] ?? $default;
    }

    public function setKey($key, $value, $company = null)
    {
        $this->getCompany();

        $company_id = $company ?? $this->company->id;

        if (!$company_id) {
            return;
        }

        CompanySetting::updateOrCreate([
            'company_id' => $company_id,
            'key' => $key,
        ], [
            'value' => $value,
            'organization_id' => $this->organization->id
        ]);
        return;
    }

    public function forgetKey($key)
    {
        $this->getCompany();
        $values = $this->findWhere([
            'company_id', $this->company->id,
            'key', $key,
        ])->delete();

        return;
    }
}
