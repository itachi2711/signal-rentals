<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{

    public function run()
    {
        DB::table('permissions')->delete();

        $permissions = [
			[
                'name'           => 'view-landlord',
                'display_name'   => 'view-landlord',
                'description'    => 'view-landlord',
            ],
			[
                'name'           => 'create-landlord',
                'display_name'   => 'create-landlord',
                'description'    => 'create-landlord',
            ],
			[
                'name'           => 'edit-landlord',
                'display_name'   => 'edit-landlord',
                'description'    => 'edit-landlord',
            ],
			[
                'name'           => 'delete-landlord',
                'display_name'   => 'delete-landlord',
                'description'    => 'delete-landlord',
            ],
			[
                'name'           => 'view-property',
                'display_name'   => 'view-property',
                'description'    => 'view-property',
            ],
			[
                'name'           => 'create-property',
                'display_name'   => 'create-property',
                'description'    => 'create-property',
            ],
			[
                'name'           => 'edit-property',
                'display_name'   => 'edit-property',
                'description'    => 'edit-property',
            ],
			[
                'name'           => 'delete-property',
                'display_name'   => 'delete-property',
                'description'    => 'delete-property',
            ],
			[
                'name'           => 'view-tenant',
                'display_name'   => 'view-tenant',
                'description'    => 'view-tenant',
            ],
			[
                'name'           => 'create-tenant',
                'display_name'   => 'create-tenant',
                'description'    => 'create-tenant',
            ],
			[
                'name'           => 'edit-tenant',
                'display_name'   => 'edit-tenant',
                'description'    => 'edit-tenant',
            ],
			[
                'name'           => 'delete-tenant',
                'display_name'   => 'delete-tenant',
                'description'    => 'delete-tenant',
            ],
			[
                'name'           => 'view-lease',
                'display_name'   => 'view-lease',
                'description'    => 'view-lease',
            ],
			[
                'name'           => 'create-lease',
                'display_name'   => 'create-lease',
                'description'    => 'create-lease',
            ],
			[
                'name'           => 'edit-lease',
                'display_name'   => 'edit-lease',
                'description'    => 'edit-lease',
            ],
			[
                'name'           => 'delete-lease',
                'display_name'   => 'delete-lease',
                'description'    => 'delete-lease',
            ],
			[
                'name'           => 'view-reading',
                'display_name'   => 'view-reading',
                'description'    => 'view-reading',
            ],
			[
                'name'           => 'create-reading',
                'display_name'   => 'create-reading',
                'description'    => 'create-reading',
            ],
			[
                'name'           => 'edit-reading',
                'display_name'   => 'edit-reading',
                'description'    => 'edit-reading',
            ],
			[
                'name'           => 'delete-reading',
                'display_name'   => 'delete-reading',
                'description'    => 'delete-reading',
            ],
			[
                'name'           => 'view-notice',
                'display_name'   => 'view-notice',
                'description'    => 'view-notice',
            ],
			[
                'name'           => 'create-notice',
                'display_name'   => 'create-notice',
                'description'    => 'create-notice',
            ],
			[
                'name'           => 'edit-notice',
                'display_name'   => 'edit-notice',
                'description'    => 'edit-notice',
            ],
			[
                'name'           => 'delete-notice',
                'display_name'   => 'delete-notice',
                'description'    => 'delete-notice',
            ],
			[
                'name'           => 'view-invoice',
                'display_name'   => 'view-invoice',
                'description'    => 'view-invoice',
            ],
			[
                'name'           => 'manage-setting',
                'display_name'   => 'manage-setting',
                'description'    => 'manage-setting'
            ],
			[
                'name'           => 'view-report',
                'display_name'   => 'view-report',
                'description'    => 'view-report'
            ],
			[
                'name'           => 'edit-profile',
                'display_name'   => 'edit-profile',
                'description'    => 'edit-profile'
            ],
            [
                'name'           => 'create-payment',
                'display_name'   => 'create-payment',
                'description'    => 'create-payment'
            ],
			[
                'name'           => 'approve-payment',
                'display_name'   => 'approve-payment',
                'description'    => 'approve-payment'
            ],
			[
                'name'           => 'cancel-payment',
                'display_name'   => 'cancel-payment',
                'description'    => 'cancel-payment'
            ],
			[
                'name'           => 'view-payment',
                'display_name'   => 'view-payment',
                'description'    => 'view-payment'
            ],
            [
                'name'           => 'waive-invoice',
                'display_name'   => 'waive-invoice',
                'description'    => 'waive-invoice'
            ],
            [
                'name'           => 'terminate-lease',
                'display_name'   => 'terminate-lease',
                'description'    => 'terminate-lease'
            ]
        ];

        foreach ($permissions as $key => $value){
            Permission::create($value);
        }
    }

}
