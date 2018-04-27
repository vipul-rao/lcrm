<?php

/**
 * Installation.
 */
Route::group(['prefix' => 'install', 'namespace' => 'Installation'], function () {
    Route::get('', 'InstallController@index');
    Route::get('requirements', 'InstallController@requirements');
    Route::get('permissions', 'InstallController@permissions');
    Route::get('database', 'InstallController@database');
    Route::post('database', 'InstallController@postDatabase');
    Route::get('start-installation', 'InstallController@installation');
    Route::post('start-installation', 'InstallController@installation');
    Route::get('install', 'InstallController@install');
    Route::post('install', 'InstallController@install');
    Route::get('settings', 'InstallController@settings');
    Route::post('settings', 'InstallController@settingsSave');
    Route::get('email_settings', 'InstallController@settingsEmail');
    Route::post('email_settings', 'InstallController@settingsEmailSave');
    Route::get('complete', 'InstallController@complete');
    Route::get('error', 'InstallController@error');
});
