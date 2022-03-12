<?php

namespace App\Http\Controllers\Api\Oauth;

use App\Rental\Repositories\Contracts\GeneralSettingInterface;
use App\Rental\Repositories\Contracts\LandlordInterface;
use App\Rental\Repositories\Contracts\RoleInterface;
use App\Rental\Repositories\Contracts\TenantInterface;
use App\Rental\Repositories\Contracts\UserInterface;
use App\Models\OauthClient;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LoginProxy
{
    const REFRESH_TOKEN = 'refreshToken';
    protected $app;
    private $auth, $cookie, $db, $request, $userRepository, $roleRepository,
        $generalSettingRepository, $landlordRepository, $tenantRepository;

    /**
     * LoginProxy constructor.
     * @param Application $app
     * @param UserInterface $userRepository
     * @param RoleInterface $roleRepository
     * @param TenantInterface $tenantRepository
     * @param LandlordInterface $landlordRepository
     * @param GeneralSettingInterface $generalSettingRepository
     */
    public function __construct(Application  $app,
                                UserInterface $userRepository,
                                RoleInterface $roleRepository,
                                TenantInterface $tenantRepository,
                                LandlordInterface $landlordRepository,
                                GeneralSettingInterface $generalSettingRepository) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->generalSettingRepository = $generalSettingRepository;
        $this->landlordRepository = $landlordRepository;
        $this->tenantRepository = $tenantRepository;
        $this->app = $app;
        $this->auth = $app->make('auth');
        $this->cookie = $app->make('cookie');
        $this->db = $app->make('db');
        $this->request = $app->make('request');
    }

    /**
     * @param $email
     * @param $password
     * @return array|null
     */
    private function getLandlord($email, $password) {
        $user = $this->landlordRepository->getViaEmail($email);
        $validPassword = false;
        if (isset($user))
            $validPassword = Hash::check($password, $user->getAuthPassword());

        if (!is_null($user) && $validPassword) {
            $client = OauthClient::where('password_client', 1)
                ->where('provider', 'landlords')
                ->latest()
                ->first();

            return [
                'user'      => $user,
                'client'    => $client,
                'scope'     => 'am-landlord'
            ];
        }
        return null;
    }

    /**
     * @param $email
     * @param $password
     * @return array|null
     */
    private function getTenant($email, $password) {
        $user = $this->tenantRepository->getViaEmail($email);
        $validPassword = false;
        if (isset($user))
            $validPassword = Hash::check($password, $user->getAuthPassword());

        if (!is_null($user) && $validPassword) {
            $client = OauthClient::where('password_client', 1)
                ->where('provider', 'tenants')
                ->latest()
                ->first();

            return [
                'user'      => $user,
                'client'    => $client,
                'scope'     => 'am-tenant'
            ];
        }
        return null;
    }

    /**
     * Admin has a role, which in turn got permissions.
     * So we fetch this user's role permissions and assign them to scopes variable
     * @param $email
     * @param $password
     * @return array|null
     */
    private function getAdmin($email, $password) {
        $user = $this->userRepository->getWhere('email', $email);
        $validPassword = false;
        if (isset($user))
            $validPassword = Hash::check($password, $user->getAuthPassword());

        if (!is_null($user) && $validPassword) {
            $scope = trim($this->checkPermissions($user->role_id));

            $client = OauthClient::where('password_client', 1)
                ->where('provider', 'users')
                ->latest()
                ->first();

            return [
                'user'      => $user,
                'client'    => $client,
                'scope'     => $scope
            ];
        }
        return null;
    }

    /**
     * We guess the user is admin, if that fails, we check if they are landlord, then finally if they are tenant.
     * @param $email
     * @param $password
     * @return array
     */
    public function attemptLogin($email, $password)
    {
        try {
            $user = $this->getAdmin($email, $password);

            if (is_null($user))
                $user = $this->getLandlord($email, $password);

            if (is_null($user))
                $user = $this->getTenant($email, $password);

            if (!is_null($user)){

                $client         = $user['client'];
                $userDetails    = $user['user'];
                $scope          = $user['scope'];

                $clientId = !is_null($client) ? $client->id : null;
                $clientSecret = !is_null($client) ? $client->secret : null;

                return $this->proxy([
                    'username'      => $email,
                    'password'      => $password,
                    'scope'         => $scope,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type'    => 'password'
                ], $userDetails);
            }else {
                throw new UnauthorizedHttpException('', Exception::class, null, 0);
            }
        } catch (\Exception $exception) {
            throw new UnauthorizedHttpException($exception->getMessage(), Exception::class, null, 0);
        }
    }

    /**
     * Attempt to refresh the access token used a refresh token that
     * has been saved in a cookie
     */
    public function attemptRefresh()
    {
        try {
            $refreshToken = $this->request->cookie(self::REFRESH_TOKEN);
            return $this->proxy('refresh_token', [
                'refresh_token' => decrypt($refreshToken)
            ]);
        } catch (DecryptException $e) {
            throw new DecryptException($e);
        }
    }

    /**
     * @param $data
     * @param array $user
     * @return array
     */
    public function proxy($data, $user = array())
    {
        // Make internal POST request
        $request = Request::create('/oauth/token', 'POST', $data, [], [], [
            'HTTP_Accept' => 'application/json',
        ]);

        try {
            $response = $this->app->handle($request);
        } catch (\Exception $e) {
            throw new UnauthorizedHttpException("", $e->getMessage(), null, 0);
        }

        if (!$response->isSuccessful()) {
            throw new UnauthorizedHttpException("", Exception::class, null, 0);
        }

        $decodedResponse = json_decode($response->getContent());

        // Create a refresh token cookie
        $this->cookie->queue(
            self::REFRESH_TOKEN,
            $decodedResponse->refresh_token,
            864000, // 10 days
            null,
            null,
            false,
            true // HttpOnly
        );

        return [
            'access_token' => $decodedResponse->access_token,
            'expires_in' => $decodedResponse->expires_in,
            'g_settings' => $this->generalSettingRepository->getFirst(),
            'agent_id' => $user ? $user['agent_id'] : null,
            'first_name' => $user ? $user['first_name'] : null,
            'middle_name' => $user ? $user['middle_name'] : null,
            'last_name' => $user ? $user['last_name'] : null,
             'scope' 		=> $data['scope']
        ];
    }

    /**
     * @param $roleId
     * @return string
     */
    private function checkPermissions($roleId)
    {
        $role = $this->roleRepository->getWhere('id', $roleId, ['permissions']);
        if (!$role)
            return '';
        $role_permissions = $role->permissions()->get()->toArray();
        $data = [];
        foreach ($role_permissions as $key => $value) {
            $data[] = trim($value['name']);
        }
        array_push($data, 'view-dashboard');
        return implode(' ', $data);
    }

    /**
     * @param $roleId
     * @return string
     */
    private function checkRole($roleId)
    {
        $role = $this->roleRepository->getWhere('id', $roleId, ['permissions']);
        $data[] = trim(strtolower($role->role_name));
        return implode(' ', $data);
    }

    /**
     * Logs out the user. Revokes access token.
     */
    public function logout()
    {
        if (auth('api')->check()) {
            $user = auth('api')->user();
            $user->token()->revoke();
        } elseif (auth('landlords')->check()){
            $user = auth('landlords')->user();
            $user->token()->revoke();
        } elseif (auth('tenants')->check()){
            $user = auth('tenants')->user();
            $user->token()->revoke();
        }
    }

    /**
     * @param $landlord
     * @return bool
     */
    public function checkLandlord($landlord)
    {
        if (auth('landlords')->check()) {
            if ($landlord->id != auth()->user()->id) {
                $this->logout();
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * @param $tenant
     * @return bool
     */
    public function checkTenant($tenant)
    {
        if (auth('tenants')->check()) {
            if ($tenant->id != auth()->user()->id) {
                $this->logout();
                return false;
            }
            return true;
        }
        return true;
    }
}
