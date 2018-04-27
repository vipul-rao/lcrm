<?php

namespace App\Repositories;

interface InstallRepository
{
    public function getRequirements();

    public function allRequirementsLoaded();

    public function getPermissions();

    public function allPermissionsGranted();

    public function getDisablePermissions();

    public function allDisablePermissionsGranted();

    public function dbCredentialsAreValid();
}
