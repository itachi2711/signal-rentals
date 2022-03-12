<?php

namespace App\Jobs;

use App\Rental\Repositories\Contracts\SmsTemplateInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $smsTemplateRepository, $event, $notifiable, $data;

    /**
     * SendSMS constructor.
     * @param $event
     * @param $notifiable
     * @param $data
     */
    public function __construct($event, $notifiable, $data)
    {
        $this->event = $event;
        $this->notifiable = $notifiable;
        $this->data = $data;
    }

    /**
     * @param SmsTemplateInterface $smsTemplateRepository
     */
    public function handle(SmsTemplateInterface $smsTemplateRepository)
    {
        $this->smsTemplateRepository = $smsTemplateRepository;
        $smsContent = $this->smsTemplateRepository->makeSMSBody($this->event, $this->notifiable, $this->data);
        $send = send_sms($smsContent['body'], $smsContent['number']);
    }
}
