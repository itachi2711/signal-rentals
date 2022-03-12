<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/7/2021
 * Time: 11:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Oauth\LoginProxy;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\TenantInterface;

class TenantInvoicesController extends ApiController
{
    /**
     * @var TenantInterface
     */
    protected $tenantRepository, $load, $loginProxy;

    /**
     * LandlordPropertiesController constructor.
     * @param TenantInterface $tenantRepository
     * @param LoginProxy $loginProxy
     */
    public function __construct(TenantInterface $tenantRepository, LoginProxy $loginProxy)
    {
        $this->tenantRepository = $tenantRepository;
        $this->loginProxy = $loginProxy;
        $this->load = [];
    }

    /**
     * @param Tenant $tenant
     * @return mixed
     */
    public function index(Tenant $tenant)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $limit = $this->tenantRepository->limit();
            $invoices = $tenant->invoices()->with([])->paginate($limit);

            if (isset($invoices))
                return $this->respondWithData(InvoiceResource::collection($invoices));

            return $this->respondNotFound('Invoice not found.');
        }
        return $this->respondNotFound('Invoice not found.');
    }

    /**
     * @param Tenant $tenant
     * @param Invoice $invoice
     * @return mixed
     */
    public function show(Tenant $tenant, Invoice $invoice)
    {
        if ($this->loginProxy->checkTenant($tenant)) {
            $data = $tenant->invoices()->where('invoices.id', $invoice->id)->first();

            if (isset($data))
                return $this->respondWithData(InvoiceResource::make($data));

            return $this->respondNotFound('Invoice not found.');
        }
        return $this->respondNotFound('Invoice not found.');
    }

}





