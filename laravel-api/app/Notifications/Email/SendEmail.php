<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/27/2021
 * Time: 2:42 PM
 */

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $templateName, $data;

    /**
     * SendEmail constructor.
     * @param $templateName
     * @param $data
     */
    public function __construct($templateName, $data)
    {
        $this->afterCommit = true;
        $this->templateName = $templateName;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $template = EmailTemplate::where('name', $this->templateName)->get()->first();
        $subject = $template['subject'];
        $body = $template['body'];
        $tags = explode(',', $template['tags']);

        $combinedData = $notifiable;
        if (!empty($this->data))
            $combinedData = array_merge($notifiable->toArray(), $this->data->toArray());
        foreach ($tags as $tag) {
            $subject = str_replace(trim($tag), $combinedData[trim($tag, ' {}')] ?? '', $subject);
            $body = str_replace(trim($tag), $combinedData[trim($tag, ' {}')] ?? '', $body);
        }
        return (new MailMessage)
            ->subject($subject)
            ->line($body);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
