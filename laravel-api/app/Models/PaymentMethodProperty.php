<?php

namespace App\Models;

use App\Traits\SearchableTrait;

class PaymentMethodProperty extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_method_properties';

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
        'utility_id',
        'property_id',

        'utility_type',
        'utility_unit_cost',
        'utility_standard_fee'
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
            'payment_method_properties.id' => 2
        ]
    ];
}
