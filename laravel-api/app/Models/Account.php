<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/29/2020
 * Time: 12:01 PM
 */

namespace App\Models;

use App\Traits\SearchableTrait;

class Account extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

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
        'property_id',
        'lease_id',
        'account_class_id',
        'account_type',
        'account_number',
        'account_name', // Will be member_id (For deposit accounts) // loan_id (For loan accounts)
        'other_details',
        'status',
        'closed_on',
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
            'accounts.account_number' => 1,
            'accounts.account_name' => 1
        ]
    ];

    /**
     * Generate account numbers
     * Branch code, year, month, day and three random numbers
     */
    static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
           /* if (empty($model->account_number)) {
                $branchCode = 'AC-';
                $random = substr(uniqid('', true), -3);
                $model->account_number = $branchCode . now()->year . now()->month . now()->day . $random;
            }*/
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalDebitEntries()
    {
        return $this->hasMany(Journal::class, 'debit_account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journalCreditEntries()
    {
        return $this->hasMany(Journal::class, 'credit_account_id');
    }
}
