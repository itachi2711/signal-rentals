<?php

namespace App\Providers;

use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(Carbon::now()->addMinutes(300));

        Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(300));

        $data = [];

        try{
            if(Schema::hasTable('permissions')){
                //Fetch all available permissions to be used for tokensCan
                $permissions = Permission::all();
                if(!is_null($permissions)){
                    foreach ($permissions->toArray() as $key => $value)
                        $data[trim($value['name'])] =  trim($value['display_name'] );
                }
                // We add member scopes to differentiate the tokens in the client side.
                // Remember we use the same login url.
                // In LoginProxy we fix 'member' as only scope for non admin user.
                if (!is_null($data)) {
                    $data['am-tenant'] = 'am-tenant';
                    $data['am-landlord'] = 'am-landlord';
                    $data['view-dashboard'] = 'view-dashboard';
                    Passport::tokensCan($data);
                }
            }
        }catch (\PDOException $exception ){
            Passport::tokensCan([]);
        }catch (\Exception $exception){
            Passport::tokensCan([]);
        }
    }
}
