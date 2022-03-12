<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:16 PM
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailConfigSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'protocol' => $this->protocol,
            'smpt_host' => $this->smpt_host,
            'smpt_username' => $this->smpt_username,
            'smpt_password' => $this->smpt_password,
            'smpt_port' => $this->smpt_port,
            'mail_gun_domain' => $this->mail_gun_domain,
            'mail_gun_secret' => $this->mail_gun_secret,
            'mandrill_secret' => $this->mandrill_secret,
            'from_name' => $this->from_name,
            'from_email' => $this->from_email,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
