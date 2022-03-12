<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/14/2021
 * Time: 10:05 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\UnitResource;
use App\Models\Property;
use App\Rental\Repositories\Contracts\InvoiceInterface;
use App\Rental\Repositories\Contracts\PropertyInterface;

class PropertyUnitsController extends ApiController
{
    /**
     * @var InvoiceInterface
     */
    protected $propertyRepository, $load;

    /**
     * InvoiceController constructor.
     * @param PropertyInterface $propertyRepository
     */
    public function __construct(PropertyInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
        $this->load = [];
    }

    /**
     * @param Property $property
     * @return mixed
     */
    public function index(Property $property)
    {
        $limit = $this->propertyRepository->limit();
        $units = $property->units()->with([])->paginate($limit);
        if (isset($units))
            return $this->respondWithData(UnitResource::collection($units));

        return $this->respondNotFound('Unit not found.');
    }

}
