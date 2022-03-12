<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/17/2021
 * Time: 3:05 PM
 */

namespace App\Models;

use App\Traits\SearchableTrait;

class PropertySetting extends BaseModel
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'property_settings';

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
        'default_commission',
        'commission_type',
        'property_prefix',
        'next_property_number'
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
            'property_settings.default_commission' => 1,
            'property_settings.commission_type' => 1,
            'property_settings.property_prefix' => 1,
            'property_settings.next_property_number' => 1,
        ]
    ];
}

