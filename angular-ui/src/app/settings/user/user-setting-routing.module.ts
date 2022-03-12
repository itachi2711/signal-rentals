import { Routes, RouterModule } from '@angular/router';
import { UserSettingComponent } from './user-setting.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: UserSettingComponent,
        children: [
            {
                path: '',
              //  loadChildren: 'app/settings/user/general/user-general-setting.module#UserGeneralSettingModule'
                loadChildren: () => import('app/settings/user/general/user-general-setting.module')
                    .then(m => m.UserGeneralSettingModule)
            },
            {
                path: 'roles',
              //  loadChildren: 'app/settings/user/roles/user-roles-setting.module#UserRolesSettingModule'
                loadChildren: () => import('app/settings/user/roles/user-roles-setting.module')
                    .then(m => m.UserRolesSettingModule)
            },
            {
                path: 'permissions',
              //  loadChildren: 'app/settings/user/permissions/user-permissions-setting.module#UserPermissionsSettingModule'
                loadChildren: () => import('app/settings/user/permissions/user-permissions-setting.module')
                    .then(m => m.UserPermissionsSettingModule)
            }
        ]
    }
];

export const UserSettingRoutingModule = RouterModule.forChild(ROUTES);
