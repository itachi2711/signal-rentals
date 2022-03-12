<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/12/2021
 * Time: 10:36 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Resources\VacationNoticeResource;
use App\Models\Property;
use App\Rental\Repositories\Contracts\PropertyInterface;
use App\Rental\Repositories\Contracts\VacationNoticeInterface;

class PropertyNoticesController extends ApiController
{
    /**
     * @var VacationNoticeInterface
     */
    protected $propertyRepository, $load;

    /**
     * NoticeController constructor.
     * @param PropertyInterface $propertyInterface
     */
    public function __construct(PropertyInterface $propertyInterface)
    {
        $this->propertyRepository = $propertyInterface;
        $this->load = [];
    }

    /**
     * @param Property $property
     * @return mixed
     */
    public function index(Property $property)
    {
        $limit = $this->propertyRepository->limit();
        $notices = $property->notices()->with([])->paginate($limit);
        if (isset($notices))
            return $this->respondWithData(VacationNoticeResource::collection($notices));

        return $this->respondNotFound('Notice not found.');
    }

}
