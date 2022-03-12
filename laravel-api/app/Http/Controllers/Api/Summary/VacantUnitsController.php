<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 7/16/2021
 * Time: 9:18 PM
 */

namespace App\Http\Controllers\Api\Summary;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UnitResource;
use App\Rental\Repositories\Contracts\UnitInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VacantUnitsController extends ApiController
{
    /**
     * @var UnitInterface
     */
    protected $unitRepository;

    /**
     * VacantUnitsController constructor.
     * @param UnitInterface $unitRepository
     */
    public function __construct(UnitInterface $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $allUnits = $this->unitRepository->getAll();

        $vacantUnits = [];
        $properties = [];
        foreach ($allUnits as $unit) {
            if ($unit->leases_total == 0) {
                $properties[] = $unit->property;
                $vacantUnits[] = $unit;
            }
        }

       $propertyIDs = collect($properties)->map(function ($property){
            return $property->id;
        })->unique();

        //TODo pagination

        $data = [
            'units' => UnitResource::collection($vacantUnits),
            'total_properties' => $propertyIDs->count(),
            'total_units' => $allUnits->count(),
            'total_vacant' => collect($vacantUnits)->count()
        ];

        return PaginationHelper::paginate(collect(UnitResource::collection($vacantUnits)), 3);
    }
}
