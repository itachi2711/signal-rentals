<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 01/01/2020
 * Time: 16:31
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_clients';

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
    protected $fillable = [];
}
