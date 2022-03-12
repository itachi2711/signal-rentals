<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 06/05/2020
 * Time: 12:47
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

class Landlord extends BaseModel implements
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
    protected $table = 'landlords';

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
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'email',
        'registration_date',
        'id_number',
        'country',
        'state',
        'city',
        'postal_address',
        'physical_address',
        'residential_address',

        'password',
        'confirmed',
        'confirmation_code',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

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
            'landlords.first_name' => 1,
            'landlords.middle_name' => 1,
            'landlords.last_name' => 1,
            'landlords.id_number' => 1,
            'landlords.email' => 1,
            'landlords.phone' => 1,
            'landlords.residential_address' => 2,
            'landlords.physical_address' => 2,
            'landlords.postal_address' => 2,
        ]
    ];

    protected $hidden = [
        'password', 'confirmation_code'
    ];

    /**
     * @param $registration_date
     */
  /*  public function setRegistrationDateAttribute($registration_date)
    {
        $this->attributes['registration_date'] = date('Y-m-d', strtotime($registration_date));
    }*/

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function units()
    {
        return $this->hasManyThrough(Unit::class, Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function leases()
    {
        return $this->hasManyThrough(Lease::class, Property::class)->orderBy('lease_number')
            ->where('terminated_on', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function notices()
    {
        return $this->hasManyThrough(VacationNotice::class, Property::class);
    }

    /**
     * @return int
     */
    public function getUnitTotalAttribute()
    {
        return $this->units()->count();
    }

    /**
     * @return int
     */
    public function getPropertyTotalAttribute()
    {
        return $this->properties()->count();
    }
}
