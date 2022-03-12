<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/7/2021
 * Time: 8:16 AM
 */

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $templateName, $data, $invoiceData;

    /**
     * InvoiceEmail constructor.
     * @param $templateName
     * @param $data
     * @param $invoiceData
     */
    public function __construct($templateName, $data, $invoiceData)
    {
        $this->afterCommit = true;
        $this->templateName = $templateName;
        $this->data = $data;
        $this->invoiceData = $invoiceData;
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

        $invoice =  $this->invoiceData['invoice'];
        $settings =  $this->invoiceData['settings'];
        $local_path =  $this->invoiceData['local_path'];
        $pdf = PDF::loadView('invoices.invoice', compact('invoice', 'settings'), compact('local_path'));

        return (new MailMessage)
            ->subject($subject)
            ->line($body)
            ->attachData($pdf->output(), 'invoice.pdf');
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
