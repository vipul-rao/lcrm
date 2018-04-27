<?php

namespace App\Models;

use App\Repositories\OrganizationSettingsRepositoryEloquent;
use App\Repositories\SettingsRepositoryEloquent;
use Illuminate\Database\Eloquent\Model;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Mpociot\VatCalculator\Traits\BillableWithinTheEU;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class Organization extends Model implements Transformable
{
    use SoftDeletes,Billable, TransformableTrait, RevisionableTrait;
    use BillableWithinTheEU{
        BillableWithinTheEU::getTaxPercent insteadof Billable;
    }

    private $settingsRepository;
    private $organizationSettingsRepository;

    protected $dates = ['deleted_at', 'trial_ends_at'];
    protected $fillable = ['name', 'email', 'phone_number', 'fax', 'logo', 'user_id', 'trial_ends_at', 'generic_trial_plan', 'is_deleted', 'created_by_admin'];
    protected $appends = ['trial_ends'];


    //override the field name
    protected $revisionFormattedFieldNames = [
        'trial_ends_at' => 'Due date',
        'generic_trial_plan' => 'Plan',
        'phone_number' => 'Phone Number',
        'card_brand' => 'Card',
        'card_last_four' => 'Card Number'
    ];



    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(OrganizationRole::class, 'roles');
    }

    public function staff()
    {
        $role_id = OrganizationRole::where('slug', 'staff')->first()->id;

        return $this->belongsToMany(User::class)->withPivot('role_id', 'permissions')->wherePivot('role_id', $role_id);
    }

    public function staffWithUser()
    {
        $role_id = OrganizationRole::whereIn('slug', ['admin', 'staff'])->pluck('id');

        return $this->belongsToMany(User::class)->withPivot('role_id', 'permissions')->wherePivotIn('role_id', $role_id);
    }

    public function UserStaffCustomers()
    {
        $role_id = OrganizationRole::whereIn('slug', ['admin', 'staff', 'customer'])->pluck('id');

        return $this->belongsToMany(User::class)->withPivot('role_id', 'permissions')->wherePivotIn('role_id', $role_id);
    }

    public function salesteams()
    {
        return $this->hasMany(Salesteam::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'to');
    }

    public function supports()
    {
        return $this->hasMany(Support::class, 'to');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function roleCustomers()
    {
        $role_id = OrganizationRole::where('slug', 'customer')->first()->id;

        return $this->belongsToMany(User::class)->wherePivot('role_id', $role_id);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function calls()
    {
        return $this->hasMany(Call::class);
    }

    public function qtemplates()
    {
        return $this->hasMany(Qtemplate::class);
    }

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(Saleorder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function invoiceReceivePayments()
    {
        return $this->hasMany(InvoiceReceivePayment::class);
    }

    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function date_time_format()
    {
        return config('settings.date_time_format');
    }

    public function getTrialEndsAttribute()
    {
        if ('0000-00-00' == $this->trial_ends_at || '' == $this->trial_ends_at) {
            return '';
        } else {
            return date($this->date_time_format(), strtotime($this->trial_ends_at));
        }
    }

    public function genericPlan()
    {
        return $this->belongsTo(PayPlan::class, 'generic_trial_plan');
    }

    public function paypalTransactions()
    {
        return $this->hasMany(PaypalTransaction::class);
    }

    public function taxPercentage() {
        $this->settingsRepository = new SettingsRepositoryEloquent(app());
        $this->organizationSettingsRepository = new OrganizationSettingsRepositoryEloquent(app());
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        $countryCode = config('settings.country_code');
        $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
        $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
        if ($europian_tax=='true'){
            if ($vat_number!=''){
                return 0;
            }else{
                return $taxRate*100;
            }
        }else{
            return 0;
        }
    }

}
