<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface OrganizationSettingsRepository extends RepositoryInterface
{
    public function getAll();

    public function getKey($key, $default = null);

    public function setKey($key, $value, $organization = null);

    public function forgetKey($key);
}
