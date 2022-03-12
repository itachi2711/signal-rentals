<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 9/5/2021
 * Time: 10:22 AM
 */

namespace App\Http\Controllers\Install;

/*
 * Increase PHP page execution time for this script.
 * NOTE: This function has no effect when PHP is running in safe mode (http://php.net/manual/en/ini.sect.safe-mode.php#ini.safe-mode).
 * There is no workaround other than turning off safe mode or changing the time limit (max_execution_time) in the php.ini.
 */
set_time_limit(0);

use App\Http\Controllers\Api\ApiController;
use App\Models\Permission;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class InstallController extends ApiController
{
    public function index()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('key:generate');
        Artisan::call('view:clear');
        return view('install.start');
    }

    /**
     * Server requirements are needed to run Laravel 6 app.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = [
            'GD Extension' => extension_loaded('gd'),
            'PHP Version (>= 7.3.0)' => version_compare(phpversion(), '7.3.0', '>='),
            'BCMath PHP Extension' => extension_loaded('bcmath'),
            'Ctype PHP Extension' => extension_loaded('ctype'),
            'JSON PHP Extension' => extension_loaded('json'),
            'Mbstring PHP Extension' => extension_loaded('mbstring'),
            'OpenSSL PHP Extension' => extension_loaded('openssl'),
            'PDO PHP Extension' => extension_loaded('PDO'),
            'Tokenizer PHP Extension' => extension_loaded('tokenizer'),
            'XML PHP Extension' => extension_loaded('xml'),
            'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
            'Fileinfo Extension' => extension_loaded('fileinfo')
        ];
        $allSet = true;
        foreach ($requirements as $requirement) {
            if ($requirement == false) {
                $allSet = false;
            }
        }
        return view('install.requirements', compact('requirements', 'allSet'));
    }

    /**
     * We need some directories writable for Laravel app to run. Others are image upload directories.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions()
    {
        $permissions = [
            'storage/app' => is_writable(storage_path('app')),
            'storage/framework/cache' => is_writable(storage_path('framework/cache')),
            'storage/framework/sessions' => is_writable(storage_path('framework/sessions')),
            'storage/framework/views' => is_writable(storage_path('framework/views')),
            'storage/logs' => is_writable(storage_path('logs')),
            'storage' => is_writable(storage_path('')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env file' => is_writable(base_path('.env'))
        ];
        $allSet = true;
        foreach ($permissions as $permission) {
            if ($permission == false) {
                $allSet = false;
            }
        }
        return view('install.permissions', compact('permissions', 'allSet'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function database(Request $request)
    {
        if ($request->isMethod('post')) {
            $credentials = array();
            $credentials["host"] = $request->host;
            $credentials["username"] = $request->username;
            $credentials["password"] = $request->password;
            $credentials["name"] = $request->name;
            $credentials["port"] = $request->port;
            $default = config('database.default');

            config([
                "database.connections.{$default}.host" => $credentials['host'],
                "database.connections.{$default}.database" => $credentials['name'],
                "database.connections.{$default}.username" => $credentials['username'],
                "database.connections.{$default}.password" => $credentials['password'],
                "database.connections.{$default}.port" => $credentials['port']
            ]);

            $path = base_path('.env');
            $env = file($path);

            $env = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST=' . $credentials['host'], $env);
            $env = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE=' . $credentials['name'], $env);
            $env = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME=' . $credentials['username'], $env);
            $env = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD=' . $credentials['password'], $env);
            $env = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT=' . $credentials['port'], $env);
            $bytesWritten = file_put_contents($path, $env);
            try {
                if ($bytesWritten == false) {
                    return redirect()->back()->with(["message" => 'Error !! Error connecting to database. Ensure its already created manually.']);
                }
                return redirect('install/installation');
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                Flash::warning('Error !! Could not connect to database');
                copy(base_path('.env.example'), base_path('.env'));
                return redirect()->back()->with(["message" => 'Error !! Error connecting to database. Ensure its already created manually.']);
            }

        }
        return view('install.database');
    }

    /**
     * Set up by migrating the database tables and seeding some needed starter data
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function installation(Request $request)
    {
        $alreadyInstalled = false;
        try {
            if (!is_null(Permission::first()))
                $alreadyInstalled = true;
        } catch (\PDOException $exception) {
            $alreadyInstalled = false;
        } catch (Exception $e) {
            $alreadyInstalled = false;
        }

        if ($alreadyInstalled) {
            return view('install.installation', compact('alreadyInstalled'));
        }

        if ($request->isMethod('post')) {
            try {
                /*Artisan::call('view:clear');
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
                Artisan::call('passport:install', ['--force' => true]);*/


                Artisan::call('view:clear');
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
              //  Artisan::call('key:generate');

                Artisan::call('migrate:fresh', [
                    '--force' => 'force',
                ]);
                Artisan::call('db:seed', [
                    '--force' => 'force',
                ]);
                Artisan::call('passport:keys', [
                    '--force' => true,
                    '--length' => '4096',
                ]);

                Artisan::call('passport:client', [
                    '--password' => true,
                    '--name' => config('app.name') . ' Admin Client',
                    '--provider' => 'users',
                ]);
                Artisan::call('passport:client', [
                    '--password' => true,
                    '--name' => config('app.name') . ' Landlord Client',
                    '--provider' => 'landlords',
                ]);
                Artisan::call('passport:client', [
                    '--password' => true,
                    '--name' => config('app.name') . ' Tenant Client',
                    '--provider' => 'tenants',
                ]);

               // Artisan::call('app:set');

                return redirect('install/complete');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
                Flash::warning('Error!! Setup failed, check logs - storage/logs/laravel.log');
                return redirect()->back();
            }
        }
        return view('install.installation', compact('alreadyInstalled'));
    }

    /**
     * Clean up
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function complete()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        return view('install.complete');
    }
}
