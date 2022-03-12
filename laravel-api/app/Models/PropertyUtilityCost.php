<?php


namespace App\Models;


use App\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Model;

class PropertyUtilityCost extends Model
{
    use SearchableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'property_utility_costs';

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
            'property_utility_costs.id' => 2
        ]
    ];
}
