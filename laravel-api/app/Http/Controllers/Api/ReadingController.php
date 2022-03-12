<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/20/2021
 * Time: 12:34 PM
 */

namespace App\Http\Controllers\Api;

use App\Exports\ExcelTemplateExport;
use App\Http\Requests\ReadingRequest;
use App\Http\Resources\ReadingResource;
use App\Imports\ReadingsImport;
use App\Rental\Repositories\Contracts\PropertyInterface;
use App\Rental\Repositories\Contracts\ReadingInterface;
use App\Rental\Repositories\Contracts\UtilityInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReadingController extends ApiController
{
    /**
     * @var ReadingInterface
     */
    protected $readingRepository, $load, $propertyRepository, $utilityRepository;

    /**
     * ReadingController constructor.
     * @param ReadingInterface $utilityInterface
     * @param PropertyInterface $propertyRepository
     * @param UtilityInterface $utilityRepository
     */
    public function __construct(ReadingInterface $utilityInterface,
                                PropertyInterface $propertyRepository, UtilityInterface $utilityRepository)
    {
        $this->readingRepository = $utilityInterface;
        $this->propertyRepository = $propertyRepository;
        $this->utilityRepository = $utilityRepository;
        $this->load = ['unit', 'property', 'utility'];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if ($select = request()->query('list')) {
            return $this->readingRepository->listAll($this->formatFields($select), []);
        } else
            $data = ReadingResource::collection(
                $this->readingRepository->getAllPaginate($this->load)
            );
        return $this->respondWithData($data);
    }

    /**
     * @param ReadingRequest $request
     */
    public function store(ReadingRequest $request)
    {
        $data = $request->all();
        if (array_key_exists('unitReadings', $data)) {
            $readingsData = $data['unitReadings'];
            if (isset($readingsData)) {
                foreach ($readingsData as $key => $value) {
                    $this->readingRepository->create([
                        'property_id'       => $data['property_id'],
                        'utility_id'        => $data['utility_id'],
                        'unit_id'           => $value['unit_id'],
                        'reading_date'      => $value['reading_date'],
                        'current_reading'   => $value['current_reading']
                    ]);
                }
            }
        }
    }

	   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $reading = $this->readingRepository->getById($uuid, []);

        if (!$reading) {
            return $this->respondNotFound('Reading not found.');
        }
        return $this->respondWithData(new ReadingResource($reading));
    }

    /**
     * Update the specified resource in storage.
     * @param ReadingRequest $request
     * @param $id
     * @return array|mixed
     */
    public function update(ReadingRequest $request, $id)
    {
        $save = $this->readingRepository->update($request->all(), $id);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else
            return $this->respondWithSuccess('Success !! Reading has been updated.');
    }

    /**
     * @param $uuid
     * @return array
     * @throws \Exception
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            if (auth()->user()->tokenCan('delete-reading')) {
                $reading = $this->readingRepository->getById($uuid);
                if (!isset($reading))
                    throw new \Exception('Reading not found.');
                $reading->delete();
                DB::commit();
                return $this->respondWithSuccess('Success !! Reading has been deleted.');
            }
            throw new \Exception('Action is not allowed.');
        }catch (\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function previousReading(Request $request) {
        $data = $request->all();
        $utilityID = $data['utility_id'];
        $unitID = $data['unit_id'];
       return $this->readingRepository->getLastReading($unitID, $utilityID);
    }

    /**
     * @param $propertyID
     * @param $utilityID
     * @return array
     */
    private function utilityTemplate($propertyID, $utilityID) {
        $property = $this->propertyRepository->getById($propertyID, ['units']);
        $units = $property['units'];

        $propertyIDHeader = new \stdClass();
        $propertyIDHeader->utility_id = $utilityID;
        $propertyIDHeader->property_id = $propertyID;
        $propertyIDHeader->property_code = $property['property_code'];

        $unitNames = [];
        $unitNames[] = $propertyIDHeader;
        foreach ($units as $unit) {
            $item = new \stdClass();
            $item->unit_name = $unit['unit_name'];
            $item->date = format_date(date('Y-m-d'));
            $unitNames[] = $item;
        }
        return $unitNames;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function csvTemplate(Request $request) {
        $data = $request->all();
        $units = $this->utilityTemplate($data['property_id'], $data['utility_id']);

        $filename = time().'-template.csv';
        Excel::store(new ExcelTemplateExport(new Collection($units)), $filename, 'reading_templates');

        if (Storage::disk('reading_templates')->exists($filename)) {
            return Storage::disk('reading_templates')->download($filename);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function excelTemplate(Request $request) {
        $data = $request->all();
        $units = $this->utilityTemplate($data['property_id'], $data['utility_id']);

        $filename = time().'-template.xlsx';
      //  return Excel::store(new ExcelTemplateExport(new Collection($units)), $filename);

        Excel::store(new ExcelTemplateExport(new Collection($units)), $filename, 'reading_templates');

        if (Storage::disk('reading_templates')->exists($filename)) {
            return Storage::disk('reading_templates')->download($filename);
        }
        return null;
    }

    /**
     * @param Request $request
     */
    public function uploadReadings(Request $request) {

		 if($request->hasFile('readings')) {
            $path = $request->file('readings')->getRealPath();
             Excel::import(new ReadingsImport, $path);
         }
    }
}



