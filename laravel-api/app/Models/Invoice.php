<?php

namespace App\Models;

use App\Traits\SearchableTrait;

class Invoice extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

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
        'property_id',
        'lease_id',
        'period_id',
        'period_name',

        'currency',
        'invoice_number',
        'status',

        'invoice_date',
        'due_date',
        'paid_on',
        'late_fee_charged_on',

        'terms',
        'notes',
        'total_items',
        'sub_total',
        'total_tax',
        'total_discount',
        'invoice_amount',
        'created_by',
        'updated_by',
    ];

    public function getInvoiceDateAttribute($value)
    {
        if (!isset($value))
            return null;
        return date('Y-m-d', strtotime($value));
    }

    public function getDueDateAttribute($value)
    {
        if (!isset($value))
            return null;
        return date('Y-m-d', strtotime($value));
    }

    public function getPaidOnAttribute($value)
    {
        if (!isset($value))
            return null;
        return date('Y-m-d', strtotime($value));
    }

    /**
     * @param $invoice_date
     */
    public function setInvoiceDateAttribute($invoice_date)
    {
        $this->attributes['invoice_date'] = date('Y-m-d', strtotime($invoice_date));
    }

    /**
     * @param $due_date
     */
    public function setDueDateAttribute($due_date)
    {
        $this->attributes['due_date'] = date('Y-m-d', strtotime($due_date));
    }

    /**
     * @param $paid_on
     */
    public function setPaidOnAttribute($paid_on)
    {
        $this->attributes['paid_on'] = date('Y-m-d', strtotime($paid_on));
    }

    /**
     * @param $late_fee_charged_on
     */
    public function setLateFeeChargedCnAttribute($late_fee_charged_on)
    {
        $this->attributes['late_fee_charged_on'] = date('Y-m-d', strtotime($late_fee_charged_on));
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
            'invoices.invoice_number' => 1,
            'invoices.invoice_date' => 1,
            'invoices.due_date' => 1
        ]
    ];

    static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = $model->latest()->first();
            $leaseSettings = LeaseSetting::first();
            $invoicePrefix = 'INV-';
            if (isset($leaseSettings))
                $invoicePrefix = $leaseSettings->invoice_number_prefix;
            if ($latest) {
                $string = preg_replace("/[^0-9\.]/", '', $latest->invoice_number);
                $model->invoice_number =  $invoicePrefix . sprintf('%04d', $string+1);
            }else{
                $model->invoice_number = $invoicePrefix.'0001';
            }
        });
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lease()
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payment_transactions() {
        return $this->hasManyThrough(Transaction::class, InvoiceItem::class,
            'invoice_id', 'invoice_item_id', 'id', 'id')
            ->where('transaction_type',PAYMENT);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function waiver_transactions() {
        return $this->hasManyThrough(Transaction::class, InvoiceItem::class,
            'invoice_id', 'invoice_item_id', 'id', 'id')
            ->where('transaction_type',WAIVER);
    }
}
