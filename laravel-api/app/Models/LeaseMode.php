<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 22/05/2020
 * Time: 13:20
 */

namespace App\Models;

use App\Traits\SearchableTrait;

class LeaseMode extends BaseModel
{
    use SearchableTrait;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lease_modes';

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
        'lease_mode_name',
        'lease_mode_display_name',
        'lease_mode_description'
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
            'lease_modes.id' => 2
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
}