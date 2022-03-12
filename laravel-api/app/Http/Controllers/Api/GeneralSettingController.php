<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/6/2021
 * Time: 7:28 AM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\GeneralSettingRequest;
use App\Http\Resources\GeneralSettingResource;
use App\Rental\Repositories\Contracts\GeneralSettingInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneralSettingController extends ApiController
{
    /**
     * @var GeneralSettingInterface
     */
    protected $generalSettingRepository;

    /**
     * GeneralSettingController constructor.
     * @param GeneralSettingInterface $generalSettingInterface
     */
    public function __construct(GeneralSettingInterface $generalSettingInterface)
    {
        $this->generalSettingRepository = $generalSettingInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $generalSetting = $this->generalSettingRepository->getFirst();

        if (!$generalSetting) {
            return null;
        }

        $datesData = [];
        $dateFormats = [
            'd/m/Y' => date('d/m/Y'),
            'm/d/Y' => date('m/d/Y'),
            'Y/m/d' => date('Y/m/d'),
            'F j, Y' => date('F j, Y'),
            'm.d.y' => date('m.d.y'),
            'd-m-Y' => date('d-m-Y'),
            'D M j Y' => date('D M j Y')
        ];
        foreach ($dateFormats as $key => $value) {
            $x = new \stdClass();
            $x->name = $key;
            $x->display_name = $value;
            $datesData[] = $x;
        }

        $amountThousandSeparatorData = [];
        $amountThousandSeparator = [
            ',' => '1,000 - Comma Separator',
            '.' => '1.000 - Dot Separator'
        ];
        foreach ($amountThousandSeparator as $key => $value) {
            $x = new \stdClass();
            $x->name = $key;
            $x->display_name = $value;
            $amountThousandSeparatorData[] = $x;
        }

        $amountDecimalsData = [];
        $amountDecimals = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4'
        ];
        foreach ($amountDecimals as $key => $value) {
            $x = new \stdClass();
            $x->name = $key;
            $x->display_name = $value;
            $amountDecimalsData[] = $x;
        }

        $amountDecimalSeparatorData = [];
        $amountDecimalSeparator = [
            '.' => '1000.00 - Dot',
            ',' => '1000,00 - Comma'
        ];
        foreach ($amountDecimalSeparator as $key => $value) {
            $x = new \stdClass();
            $x->name = $key;
            $x->display_name = $value;
            $amountDecimalSeparatorData[] = $x;
        }

        $generalSetting['date_formats'] = $datesData;
        $generalSetting['amount_thousand_separators'] = $amountThousandSeparatorData;
        $generalSetting['amount_decimals'] = $amountDecimalsData;
        $generalSetting['amount_decimal_separators'] = $amountDecimalSeparatorData;

        return $this->respondWithData(new GeneralSettingResource($generalSetting));
    }

    /**
     * @param GeneralSettingRequest $request
     * @return mixed
     */
    public function store(GeneralSettingRequest $request)
    {
        $data = $request->all();
        $save = $this->generalSettingRepository->create($data);

        // Upload logo
        $data['logo'] = null;
        if ($request->hasFile('logo')) {
            $filenameWithExt = $request->file('logo')->getClientOriginalName();

            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            // Get just ext
            $extension = $request->file('logo')->getClientOriginalExtension();

            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $path = $request->file('logo')->storeAs('logos', $fileNameToStore);

            $data['logo'] = $fileNameToStore;
        }

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! GeneralSetting has been created.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $generalSetting = $this->generalSettingRepository->getById($uuid);

        if (!$generalSetting) {
            return $this->respondNotFound('GeneralSetting not found.');
        }
        return $this->respondWithData(new GeneralSettingResource($generalSetting));
    }

    /**
     * @param GeneralSettingRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(GeneralSettingRequest $request, $uuid)
    {
        $data = $request->all();
        $save = $this->generalSettingRepository->update($data, $uuid);
        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else {
            return $this->respondWithSuccess('Success !! GeneralSetting has been updated.');
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->generalSettingRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! GeneralSetting has been deleted');
        }
        return $this->respondNotFound('GeneralSetting not deleted');
    }

    /**
     * @param Request $request
     */
    public function uploadLogo(Request $request) {
        $setting = $this->generalSettingRepository->getFirst();
        $oldLogo = $setting->logo;

        $data = $request->all();

        // Upload logo
        if($request->hasFile('logo')) {
            $filenameWithExt = $request->file('logo')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('logo')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('logo')->storeAs('logos', $fileNameToStore);
            $data['logo'] = $fileNameToStore;
            $this->generalSettingRepository->update($data, $data['id']);
            if($oldLogo != '')
                Storage::delete('logos/'.$oldLogo);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fetchLogo(Request $request)
    {
        $data = $request->all();
        $setting = $this->generalSettingRepository->getById($data['id']);

        $file_path = $setting->logo;
        $local_path = config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR .'logos'.DIRECTORY_SEPARATOR. $file_path;
        return response()->file($local_path);
    }
}
