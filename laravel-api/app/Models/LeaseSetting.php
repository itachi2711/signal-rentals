<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:06 PM
 */

namespace App\Models;

use App\Traits\SearchableTrait;

class LeaseSetting extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lease_settings';

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
        'lease_number_prefix',
        'invoice_number_prefix',
        'invoice_footer',
        'invoice_terms',
        'show_payment_method_on_invoice',
        'generate_invoice_on',
        'next_period_billing',
        'skip_starting_period',
        'waive_penalty'
    ];

    /**
     * The attributes that should be cast to native types.
     * @var string[]
     */
    protected $casts = [
        'next_period_billing'  => 'boolean',
        'skip_starting_period'  => 'boolean',
        'waive_penalty'         => 'boolean',
        'show_payment_method_on_invoice'         => 'boolean',
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
            'lease_settings.lease_number_prefix' => 1,
            'lease_settings.invoice_number_prefix' => 1,
        ]
    ];
}
