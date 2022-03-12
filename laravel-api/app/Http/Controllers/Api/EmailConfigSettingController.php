<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 6/19/2021
 * Time: 1:16 PM
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmailConfigSettingRequest;
use App\Http\Resources\EmailConfigSettingResource;
use App\Rental\Repositories\Contracts\EmailConfigSettingInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class EmailConfigSettingController extends ApiController
{
    /**
     * @var EmailConfigSettingInterface
     */
    protected $emailSettingRepository;

    /**
     * EmailSettingController constructor.
     * @param EmailConfigSettingInterface $emailSettingInterface
     */
    public function __construct(EmailConfigSettingInterface $emailSettingInterface)
    {
        $this->emailSettingRepository = $emailSettingInterface;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $configData = [
            'driver' => Config::get('mail.mailers.smtp.transport'),
            'host' => Config::get('mail.mailers.smtp.host'),
            'username' => Config::get('mail.mailers.smtp.username'),
            'password' => Config::get('mail.mailers.smtp.password'),
            'port' => Config::get('mail.mailers.smtp.port'),
            'from_address' => Config::get('mail.from.address'),
            'from_name' => Config::get('mail.from.name'),
        ];

        return $configData;
    }

    /**
     * @param EmailConfigSettingRequest $request
     * @return mixed
     */
    public function store(EmailConfigSettingRequest $request)
    {
        $data = $request->all();

        $driver = empty($data['driver']) ? '' : $data['driver'];
        $host = empty($data['host']) ? null : $data['host'];
        $username = empty($data['username']) ? null : $data['username'];
        $password = empty($data['password']) ? null : $data['password'];
        $port = empty($data['port']) ? null : $data['port'];
        $from_address = empty($data['from_address']) ? null : $data['from_address'];
        $from_name = empty($data['from_name']) ? null : $data['from_name'];

        $settings = compact('driver', 'host', 'username', 'password', 'port', 'from_address', 'from_name');

        if ($this->updateEnvironmentFile($settings)) {
            return $this->respondWithSuccess('Success !! EmailSetting has been created.');
        }
        return $this->respondNotSaved('Error, Settings now updated');
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function show($uuid)
    {
        $emailSetting = $this->emailSettingRepository->getById($uuid);

        if (!$emailSetting) {
            return $this->respondNotFound('EmailSetting not found.');
        }
        return $this->respondWithData(new EmailConfigSettingResource($emailSetting));

    }

    /**
     * @param EmailConfigSettingRequest $request
     * @param $uuid
     * @return mixed
     */
    public function update(EmailConfigSettingRequest $request, $uuid)
    {
        $save = $this->emailSettingRepository->update($request->all(), $uuid);

        if (!is_null($save) && $save['error']) {
            return $this->respondNotSaved($save['message']);
        } else

            return $this->respondWithSuccess('Success !! EmailSetting has been updated.');

    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function destroy($uuid)
    {
        if ($this->emailSettingRepository->delete($uuid)) {
            return $this->respondWithSuccess('Success !! EmailSetting has been deleted');
        }
        return $this->respondNotFound('EmailSetting not deleted');
    }


    /**
     * @param $settings
     * @return bool|mixed
     */
    private function updateEnvironmentFile($settings)
    {
        try {
            $env_path = base_path('.env');
            DB::purge(DB::getDefaultConnection());

            foreach ($settings as $key => $value) {
                $key = 'MAIL_' . strtoupper($key);
                //  $line = $value ? ($key . '=' . $value) : $key;
                $line = $value ? ($key . '=' . '"' . $value . '"') : $key;
                putenv($line);
                file_put_contents($env_path, preg_replace(
                    '/^' . $key . '.*/m',
                    $line,
                    file_get_contents($env_path)
                ));
            }

            config(['mail.driver' => $settings['driver']]);
            config(['mail.host' => $settings['host']]);
            config(['mail.username' => $settings['username']]);
            config(['mail.password' => $settings['password']]);
            config(['mail.port' => $settings['port']]);
            config(['mail.from_address' => $settings['from_address']]);
            config(['mail.from_name' => $settings['from_name']]);

        } catch (\Exception $exception) {
            return $this->respondNotSaved($exception->getMessage());
        }
        return true;
    }
}
