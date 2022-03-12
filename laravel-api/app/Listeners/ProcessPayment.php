<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Rental\Repositories\Contracts\PaymentInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessPayment
{
    /**
     * @var
     */
    protected $paymentRepository;

    /**
     * ProcessPayment constructor.
     * @param PaymentInterface $paymentInterface
     */
    public function __construct(PaymentInterface $paymentInterface)
    {
        $this->paymentRepository = $paymentInterface;
    }

    /**
     * Handle the event.
     *
     * @param  PaymentReceived  $event
     * @return void
     */
    public function handle(PaymentReceived $event)
    {
      //  $today = date('Y-m-d');
        $this->paymentRepository->processPayment($event->payment);
    }
}
