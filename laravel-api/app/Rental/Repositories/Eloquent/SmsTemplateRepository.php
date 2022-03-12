<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:28 PM
 */

namespace App\Rental\Repositories\Eloquent;

use App\Models\SmsTemplate;
use App\Rental\Repositories\Contracts\SmsTemplateInterface;

class SmsTemplateRepository extends BaseRepository implements SmsTemplateInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param SmsTemplate $model
     */
    function __construct(SmsTemplate $model)
    {
        $this->model = $model;
    }

    /**
     * @param $templateName
     * @param $notifiable
     * @param array $data
     * @return array
     */
    public function makeSMSBody($templateName, $notifiable, $data = array())
    {
        $template = $this->model->where('name', $templateName)->get()->first();
        $body = $template['body'];
        $tags = explode(',', $template['tags']);

        $combinedData = $notifiable;
        if (!empty($data))
            $combinedData = array_merge($notifiable->toArray(), $data->toArray());
        foreach ($tags as $tag) {
            $body = str_replace(trim($tag), $combinedData[trim($tag, ' {}')] ?? '', $body);
        }

        return [
            'body'      => $body,
            'number'    => $notifiable['phone'],
        ];
    }
}
