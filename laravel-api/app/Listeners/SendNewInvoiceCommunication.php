<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/6/2021
 * Time: 9:02 PM
 */

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Jobs\SendSMS;
use App\Models\SystemNotification;
use App\Notifications\Email\InvoiceEmail;
use App\Rental\Repositories\Contracts\AccountInterface;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\PaymentInterface;
use App\Rental\Repositories\Contracts\TenantInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendNewInvoiceCommunication
{
    /**
     * @var
     */
    protected $paymentRepository, $accountRepository, $leaseRepository, $tenantRepository, $invoiceRepository;

    /**
     * SendNewInvoiceCommunication constructor.
     * @param PaymentInterface $paymentInterface
     * @param AccountInterface $accountRepository
     * @param LeaseInterface $leaseRepository
     * @param InvoiceInterface $invoiceRepository
     * @param TenantInterface $tenantRepository
     */
    public function __construct(PaymentInterface $paymentInterface,
                                AccountInterface $accountRepository,
                                LeaseInterface $leaseRepository,
                                InvoiceInterface $invoiceRepository,
                                TenantInterface $tenantRepository)
    {
        $this->paymentRepository = $paymentInterface;
        $this->accountRepository = $accountRepository;
        $this->leaseRepository = $leaseRepository;
        $this->tenantRepository = $tenantRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @param InvoiceCreated $event
     */
    public function handle(InvoiceCreated $event)
    {
        $invoice = $event->invoice;
        $lease = $this->leaseRepository->getById($invoice['lease_id'], ['tenants']);

        if (isset($lease)) {
            $tenants = $lease->tenants;
            foreach ($tenants as $tenant) {
               // CommunicationMessage::send(NEW_INVOICE, $tenant, $invoice);
                try {
                $invoiceData = $this->invoiceRepository->invoiceData($invoice['id']);
                    $setting = SystemNotification::where('name', NEW_INVOICE)->first();
                    if (isset($setting) && $setting->send_email) {
                        Notification::send($tenant, new InvoiceEmail(NEW_INVOICE, $invoice, $invoiceData));
                    }
                    if (isset($setting) && $setting->send_sms) {
                        SendSMS::dispatch(NEW_INVOICE, $tenant, $invoice)->afterCommit();
                    }
                } catch (\Exception $exception) {
                    Log::info(json_encode('- ERROR - SendNewInvoiceCommunication - '. $exception->getMessage()));
                }
            }
        }
    }
}
