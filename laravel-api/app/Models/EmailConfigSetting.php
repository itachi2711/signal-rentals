<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:15 PM
 */

namespace App\Models;

class EmailConfigSetting extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_config_settings';

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
        'protocol',
        'smpt_host',
        'smpt_username',
        'smpt_password',
        'smpt_port',
        'mail_gun_domain',
        'mail_gun_secret',
        'mandrill_secret',
        'from_name',
        'from_email'
    ];
}
