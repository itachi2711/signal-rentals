<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 13/05/2020
 * Time: 14:44
 */

namespace App\Rental\Repositories\Eloquent;

use App\Rental\Repositories\Contracts\UnitInterface;
use App\Models\Unit;

class UnitRepository extends BaseRepository implements UnitInterface
{
    protected $model;

    /**
     * GuestRepository constructor.
     * @param Unit $model
     */
    function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function limit(){
        return (int)(request()->query('limit')) ? : 3;
    }

    public function isVacant($unitID)
    {
        if ($this->unitLease($unitID)) {
            return false;
        }
        return false;



        /// is there a an active lease for this unit
        /// lease_units
        ///
        /// active leases that belong to the property where the unit if from

    }

    public function unitLease($unitID) {
        $unit = $this->getById($unitID);

        if (isset($unit)) {
            $propertyID = $unit['property_id'];

            $leases =  $this->model
                ->where('terminated_on', null)
                ->where('property_id', $propertyID)
                ->join('lease_units', 'lease_units.unit_id', $unitID)
                ->get();

        }
    }

    /**
     * @param array $load
     * @param string $landlordID
     * @param string $propertyID
     * @return mixed
     */
    public function getVacantUnits($load = array(), $landlordID = '', $propertyID = '')
    {
        if (strlen ($this->whereField()) > 0) {
            if(strlen ($this->whereValue()) < 1) {
                return $this->model
                    ->with($load)
                    ->whereNull($this->whereField())
                    ->withCount('leases')
                    ->search($this->searchFilter(), null, true, true)
                    ->orderBy($this->sortField(), $this->sortDirection())
                   ->having('leases_count', '<', 1)
                    ->paginate($this->limit());
            }
            return $this->model
                ->with($load)
                ->where($this->whereField(), $this->whereValue())
                ->withCount('leases')
                ->search($this->searchFilter(), null, true, true)
                ->orderBy($this->sortField(), $this->sortDirection())
                ->having('leases_count', '<', 1)
                ->paginate($this->limit());
        }else {
            return $this->model
                ->with($load)
                ->withCount('leases')
                ->search($this->searchFilter(), null, true, true)
                ->orderBy($this->sortField(), $this->sortDirection())
                ->having('leases_count', '<', 1)
                ->paginate($this->limit());
        }
    }
}
