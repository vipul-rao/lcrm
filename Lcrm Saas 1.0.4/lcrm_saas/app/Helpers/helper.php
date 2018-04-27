<?php

if (!function_exists('editEnv')) {
    function editEnv($list)
    {
        $path = base_path('.env');
        $env = file($path);
        \Artisan::call('config:clear');

        if ($env) {
            foreach ($list as $key => $value) {
                $env = str_replace($key.'='.env($key), $key.'='.$value, $env);
            }
        }
        file_put_contents($path, $env);
    }
}
