<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 05/05/2020
 * Time: 00:35
 */

namespace App\Models;

use App\Traits\SearchableTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Tenant extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract

{
    use HasApiTokens, Notifiable, Authenticatable, Authorizable, CanResetPassword, SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    protected $dates = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'agent_id',
            'tenant_type_id',
            'first_name',
            'middle_name',
            'last_name',
            'gender',
            'date_of_birth',
            'id_passport_number',
            'marital_status',
            'tenant_number',
            'phone',
            'email',
            'country',
            'state',
            'city',
            'postal_code',
            'postal_address',
            'physical_address',

            'business_name',
            'registration_number',
            'business_industry',
            'business_description',
            'business_address',

            'next_of_kin_name',
            'next_of_kin_phone',
            'next_of_kin_relation',

            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_email',
            'emergency_contact_relationship',
            'emergency_contact_postal_address',
            'emergency_contact_physical_address',

            'employment_status',
            'employment_position',
            'employer_contact_phone',
            'employer_contact_email',
            'employment_postal_address',
            'employment_physical_address',

            'rent_payment_contact',
            'rent_payment_contact_postal_address',
            'rent_payment_contact_physical_address',

            'profile_pic',
            'password_set',
            'password',
            'confirmed',
            'confirmation_code',

            'created_by',
            'updated_by',
            'deleted_by'
    ];

    /**
     * @param $date_of_birth
     */
    public function setDateOfBirthAttribute($date_of_birth)
    {
        $this->attributes['date_of_birth'] = date('Y-m-d', strtotime($date_of_birth));
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
            'tenants.tenant_number' => 2,
            'tenants.first_name' => 1,
            'tenants.last_name' => 1,
            'tenants.phone' => 1,
            'tenants.business_name' => 1,
            'tenants.email' => 1
        ]
    ];


    static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = $model->latest()->first();
			 $tenantSettings = TenantSetting::first();
                $tenantPrefix = 'TN';
                if (isset($tenantSettings))
                    $tenantPrefix = $tenantSettings->tenant_number_prefix;
            if ($latest) {
                $string = preg_replace("/[^0-9\.]/", '', $latest->tenant_number);
                $model->tenant_number =  $tenantPrefix . sprintf('%04d', $string+1);
            }else{
                $model->tenant_number = $tenantPrefix .'0001';
            }
        });
    }

    protected $hidden = [
        'password', 'remember_token', 'confirmation_code'
    ];

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
    public function tenant_type()
    {
        return $this->belongsTo(TenantType::class, 'tenant_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function leases()
    {
        return $this->belongsToMany(Lease::class, 'lease_tenants', 'tenant_id', 'lease_id')
            ->where('terminated_on', null)->with('property', 'units');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notices()
    {
        return $this->hasMany(VacationNotice::class, 'tenant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'lease_tenants', 'tenant_id', 'lease_id');
        return $this->leases();
       // return $this->hasManyThrough(Invoice::class, LeaseTenant::class, 'tenant_id', 'lease_id');
    }
}
