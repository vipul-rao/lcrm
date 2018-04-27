<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrganizationRepository.
 */
interface CompanySettingsRepository extends RepositoryInterface
{
    public function getAll();

    public function getKey($key, $default = null);

    public function setKey($key, $value, $company = null);

    public function forgetKey($key);

}
