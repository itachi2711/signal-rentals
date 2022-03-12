<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 23/05/2020
 * Time: 19:47
 */

namespace App\Models;

use App\Traits\SearchableTrait;
use Ramsey\Uuid\Uuid;

class Lease extends BaseModel
{
    use SearchableTrait;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leases';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agent_id',
        'landlord_id',
        'property_id',
        'lease_type_id',
        'lease_mode_id',
        'lease_number',
        'start_date',
        'end_date',
        'due_date', // used for penalty calculations?
        'rent_amount',
        'rent_deposit',
     //   'billing_frequency', /// I decided to have the system have monthly as default and only billing frequency
        'billed_on',
        'terminated_on',
        'terminated_by',
        'next_billing_date',
        'due_on',
        'waive_penalty',

        'invoice_number_prefix',
        'invoice_footer',
        'invoice_terms',
        'show_payment_method_on_invoice',

        'agreement_doc',

        'skip_starting_period', // should we bill the same month as starting date, or make invoice for next period
        'generate_invoice_on', // day of month when invoices are generated
        'next_period_billing'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var string[]
     */
    protected $casts = [
        'next_period_billing'   => 'boolean',
        'skip_starting_period'  => 'boolean',
        'waive_penalty'         => 'boolean',
    ];

    /**
     * @param $billed_on
     *
     */
    public function setBilledOnAttribute($billed_on)
    {
        $this->attributes['billed_on'] = date('Y-m-d', strtotime($billed_on));
    }

    /**
     * @param $terminated_on
     */
    public function setTerminatedOnAttribute($terminated_on)
    {
        $this->attributes['terminated_on'] = date('Y-m-d', strtotime($terminated_on));
    }

    /**
     * @param $end_date
     */
    public function setEndDateAttribute($end_date)
    {
        $this->attributes['end_date'] = $end_date ? date('Y-m-d', strtotime($end_date)) : null;
    }

    /**
     * @param $next_billing_date
     */
    public function setNextBillingDateAttribute($next_billing_date)
    {
        $this->attributes['next_billing_date'] = date('Y-m-d', strtotime($next_billing_date));
    }

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'leases.lease_number' => 1
        ]
    ];

    /**
     * @return bool|void
     */
    static function boot()
    {
        try {
            parent::boot();
            static::creating(function ($model) {
                $latest = $model->latest()->first();
                $leaseSettings = LeaseSetting::first();
                $leasePrefix = 'LS';
                if (isset($leaseSettings))
                    $leasePrefix = $leaseSettings->lease_number_prefix;
                if ($latest) {
                    $string = preg_replace("/[^0-9\.]/", '', $latest->lease_number);
                    $model->lease_number =  $leasePrefix . sprintf('%04d', $string+1);
                }else{
                    $model->lease_number = $leasePrefix.'0001';
                }

                Account::create([
                    'id'                => Uuid::uuid4(),
                    'lease_id'          => $model['id'],
                    'property_id'       => $model['property_id'],
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => LEASE_ACCOUNT,
                    'account_name'      => LEASE_ACCOUNT,
                    'account_number'    => LEASE_ACCOUNT_CODE.'-'.$model['lease_number']
                ]);

                Account::create([
                    'id'                => Uuid::uuid4(),
                    'lease_id'          => $model['id'],
                    'property_id'       => $model['property_id'],
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => LEASE_SUSPENSE,
                    'account_name'      => LEASE_SUSPENSE,
                    'account_number'    => LEASE_SUSPENSE_CODE.'-'.$model['lease_number']
                ]);
            });
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'lease_units',
            'lease_id', 'unit_id')
            ->withPivot('unit_name')
            ->orderBy('unit_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'lease_tenants',
            'lease_id', 'tenant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lease_type()
    {
        return $this->belongsTo(LeaseType::class, 'lease_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lease_mode()
    {
        return $this->belongsTo(LeaseMode::class, 'lease_mode_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function utility_deposits()
    {
        return $this->belongsToMany(Utility::class, 'lease_utility_deposits',
            'lease_id', 'utility_id')
            ->withPivot('deposit_amount');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function utility_charges()
    {
        return $this->belongsToMany(Utility::class, 'lease_utility_charges',
            'lease_id', 'utility_id')
            ->withPivot('utility_unit_cost', 'utility_base_fee');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function extra_charges()
    {
        return $this->belongsToMany(ExtraCharge::class, 'lease_extra_charges',
            'lease_id', 'extra_charge_id')
            ->withPivot('extra_charge_value', 'extra_charge_type', 'extra_charge_frequency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function late_fees()
    {
        return $this->belongsToMany(LateFee::class, 'lease_late_fees',
            'lease_id', 'late_fee_id')
            ->withPivot('late_fee_value', 'late_fee_type', 'late_fee_frequency', 'grace_period');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account()
    {
        return $this->hasOne(Account::class, 'account_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'lease_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payment_methods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_leases',
            'lease_id', 'payment_method_id')
            ->withPivot('payment_method_description');
    }

	  /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function terminate_user()
    {
        return $this->belongsTo(User::class, 'terminated_by');
    }
}
