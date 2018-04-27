<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class InstallRepositoryEloquent implements InstallRepository
{
    public function getRequirements()
    {
        $requirements = [
            'PHP Version (>= 7.0.0)' => version_compare(phpversion(), '7.0.0', '>='),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('PDO'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'GD Extension' => extension_loaded('gd'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
        ];

        if (extension_loaded('xdebug')) {
            $requirements['Xdebug Max Nesting Level (>= 500)'] = (int) ini_get('xdebug.max_nesting_level') >= 500;
        }

        return $requirements;
    }

    public function allRequirementsLoaded()
    {
        $allLoaded = true;

        foreach ($this->getRequirements() as $loaded) {
            if (false == $loaded) {
                $allLoaded = false;
            }
        }

        return $allLoaded;
    }

    public function getPermissions()
    {
        return [
            'public/uploads/avatar' => is_writable(public_path('uploads/avatar')),
            'public/uploads/company' => is_writable(public_path('uploads/company')),
            'public/uploads/pdf' => is_writable(public_path('uploads/pdf')),
            'public/uploads/products' => is_writable(public_path('uploads/products')),
            'public/uploads/site' => is_writable(public_path('uploads/site')),
            'public/pdf' => is_writable(public_path('pdf')),
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/logs' => is_writable(storage_path('logs')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => is_writable(base_path('.env')),
        ];
    }

    public function allPermissionsGranted()
    {
        $allGranted = true;

        foreach ($this->getPermissions() as $permission => $granted) {
            if (false == $granted) {
                $allGranted = false;
            }
        }

        return $allGranted;
    }

    public function getDisablePermissions()
    {
        return [
            'Base Directory' => !is_writable(base_path('')),
        ];
    }

    public function allDisablePermissionsGranted()
    {
        $allNotGranted = true;

        foreach ($this->getDisablePermissions() as $permission => $granted) {
            if (true == $granted) {
                $allNotGranted = false;
            }
        }

        return $allNotGranted;
    }

    public function dbCredentialsAreValid()
    {
        try {
            DB::statement('SHOW TABLES');
        } catch (\Exception $e) {
            info($e->getMessage());

            return false;
        }

        return true;
    }
}
