<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/26/2021
 * Time: 12:07 PM
 */

namespace App\Traits;

use App\Jobs\SendSMS;
use App\Models\SystemNotification;
use App\Notifications\Email\SendEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

trait CommunicationMessage
{
    public static function send($template, $notifiable, $data = array())
    {
        try {
            $setting = SystemNotification::where('name', $template)->first();
            if (isset($setting) && $setting->send_email) {
                Notification::send($notifiable, new SendEmail($template, $data));
            }
            if (isset($setting) && $setting->send_sms) {
                SendSMS::dispatch($template, $notifiable, $data)->afterCommit();
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
