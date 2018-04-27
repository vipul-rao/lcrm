<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\InstallRepository::class,\App\Repositories\InstallRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CallRepository::class, \App\Repositories\CallRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrganizationRepository::class, \App\Repositories\OrganizationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrganizationRolesRepository::class, \App\Repositories\OrganizationRolesRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SettingsRepository::class, \App\Repositories\SettingsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrganizationSettingsRepository::class, \App\Repositories\OrganizationSettingsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SubscriptionRepository::class, \App\Repositories\SubscriptionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InviteUserRepository::class, \App\Repositories\InviteUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesTeamRepository::class, \App\Repositories\SalesTeamRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CompanyRepository::class, \App\Repositories\CompanyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CountryRepository::class, \App\Repositories\CountryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StateRepository::class, \App\Repositories\StateRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CityRepository::class, \App\Repositories\CityRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EmailRepository::class, \App\Repositories\EmailRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class, \App\Repositories\CategoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProductRepository::class, \App\Repositories\ProductRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CustomerRepository::class, \App\Repositories\CustomerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\LeadRepository::class, \App\Repositories\LeadRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\QuotationTemplateRepository::class, \App\Repositories\QuotationTemplateRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OpportunityRepository::class, \App\Repositories\OpportunityRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MeetingRepository::class, \App\Repositories\MeetingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\QuotationRepository::class, \App\Repositories\QuotationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesOrderRepository::class, \App\Repositories\SalesOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InvoiceRepository::class, \App\Repositories\InvoiceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InvoicePaymentRepository::class, \App\Repositories\InvoicePaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StaffPermissionsRepository::class, \App\Repositories\StaffPermissionsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PayPlanRepository::class, \App\Repositories\PayPlanRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\EmailTemplateRepository::class, \App\Repositories\EmailTemplateRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OptionRepository::class, \App\Repositories\OptionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TaskRepository::class, \App\Repositories\TaskRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SupportRepository::class, \App\Repositories\SupportRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ContactUsRepository::class,\App\Repositories\ContactUsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReplyRepository::class, \App\Repositories\ReplyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaypalTransactionRepository::class, \App\Repositories\PaypalTransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CompanySettingsRepository::class, \App\Repositories\CompanySettingsRepositoryEloquent::class);
        //:end-bindings:
    }
}
