<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ExcelRepository;
use App\Repositories\ExcelRepositoryDefault;
use App\Repositories\SettingsRepositoryEloquent;
use Maatwebsite\Excel\Excel;

class AppServiceProvider extends ServiceProvider
{
    private $settingsRepository;

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->setDbConfigurations();
        if (!$this->app->environment('production')) {
            $this->app->register('Laravel\Dusk\DuskServiceProvider');
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->register(RepositoryServiceProvider::class);

        $this->app->bind(ExcelRepository::class, function ($app) {
            $excel = new Excel(
                $app['phpexcel'],
                $app['excel.reader'],
                $app['excel.writer'],
                $app['excel.parsers.view']
            );

            return new ExcelRepositoryDefault($excel);
        });
    }

    private function setDbConfigurations()
    {
        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        try {
            /**
             * get all settings.
             */
            $settings = $this->settingsRepository->getAll();

            config(['app.name' => $settings['site_name'] ?? null]);
            //Pusher
            config(['broadcasting.connections.pusher.key' => $settings['pusher_key'] ?? null]);
            config(['broadcasting.connections.pusher.secret' => $settings['pusher_secret'] ?? null]);
            config(['broadcasting.connections.pusher.app_id' => $settings['pusher_app_id'] ?? null]);
            //Backup Manager
            config(['backup.backup.destination.disks' => $settings['backup_type'] ?? null]);
            config(['backup.monitorBackups.disks' => $settings['backup_type'] ?? null]);

            //DISK Amazon S3
            config(['filesystems.disks.s3.key' => $settings['disk_aws_key'] ?? null]);
            config(['filesystems.disks.s3.secret' => $settings['disk_aws_secret'] ?? null]);
            config(['filesystems.disks.s3.region' => $settings['disk_aws_region'] ?? null]);
            config(['filesystems.disks.s3.bucket' => $settings['disk_aws_bucket'] ?? null]);

            //DISK Dropbox
            config(['filesystems.disks.dropbox.accessToken' => $settings['disk_dbox_token'] ?? null]);

            //Stripe
            config(['services.stripe.secret' => $settings['stripe_secret'] ?? null]);
            config(['services.stripe.key' => $settings['stripe_publishable'] ?? null]);

            //Paypal
            config(['paypal.mode' => $settings['paypal_mode'] ?? null]);
            config(['paypal.sandbox.username' => $settings['paypal_sandbox_username'] ?? null]);
            config(['paypal.sandbox.password' => $settings['paypal_sandbox_password'] ?? null]);
            config(['paypal.sandbox.secret' => $settings['paypal_sandbox_signature'] ?? null]);

            config(['paypal.live.username' => $settings['paypal_live_username'] ?? null]);
            config(['paypal.live.password' => $settings['paypal_live_password'] ?? null]);
            config(['paypal.live.secret' => $settings['paypal_live_signature'] ?? null]);

            //Mailserver
            config(['mail.driver' => $settings['email_driver'] ?? null]);
            config(['mail.host' => $settings['email_host'] ?? null]);
            config(['mail.port' => $settings['email_port'] ?? null]);
            config(['mail.username' => $settings['email_username'] ?? null]);
            config(['mail.password' => $settings['email_password'] ?? null]);

            config(['services.gmaps_key' => $settings['google_maps_key'] ?? null]);
        } catch (\Illuminate\Database\QueryException $e) {
            logger()->error('check your database connection.');
        } catch (\Exception $e) {
            logger()->error($e);
        }
    }
}
