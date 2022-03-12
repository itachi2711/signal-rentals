<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:55 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Models\Tenant;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\LeaseInterface;
use App\Rental\Repositories\Contracts\TenantInterface;

class LeaseDocumentsController extends ApiController
{
    /**
     * @var InvoiceInterface
     */
    protected $leaseRepository, $load;

    /**
     * InvoiceController constructor.
     * @param LeaseInterface $leaseRepository
     */
    public function __construct(LeaseInterface $leaseRepository)
    {
        $this->leaseRepository = $leaseRepository;
        $this->load = [];
    }

    /**
     * @param Tenant $tenant
     * @return mixed
     */
    public function index(Tenant $tenant)
    {
        $limit = $this->leaseRepository->limit();
        $documents = $tenant->documents()->with([])->paginate($limit);
        if (isset($documents))
            return $this->respondWithData(InvoiceResource::collection($documents));

        return $this->respondNotFound('Document not found.');
    }

}

