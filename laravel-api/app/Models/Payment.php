<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\SearchableTrait;

class Payment extends BaseModel
{
    use  SearchableTrait, HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payments';

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
        'payment_method_id',
        'currency_id',
        'tenant_id',
        'lease_id',
        'property_id',
        'payment_date',
        'amount',
        'notes',
        'attachment',
        'receipt_number',
        'paid_by',
        'reference_number',
        'lease_number',
        'payment_status',
        'cancel_notes',
        'cancelled_by',
        'approved_by',
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
            'payments.amount' => 1,
            'payments.payment_date' => 1,
            'payments.reference_number' => 1,
            'payments.receipt_number' => 1
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
                if ($latest) {
                    $string = preg_replace("/[^0-9\.]/", '', $latest->receipt_number);
                    $model->receipt_number =  'RCT-' . sprintf('%04d', $string+1);
                }else{
                    $model->receipt_number = 'RCT-0001';
                }
            });
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $payment_date
     */
    public function setPaymentDateAttribute($payment_date)
    {
        $this->attributes['payment_date'] = date('Y-m-d H:i:s', strtotime($payment_date));
    }

	 /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

	/**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lease()
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cancel_user()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approve_user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payment_id');
    }
}
