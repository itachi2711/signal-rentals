import { Routes, RouterModule } from '@angular/router';
import { TenantSettingComponent } from './tenant-setting.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: TenantSettingComponent,
        children: [
            {
                path: '',
                loadChildren: () => import('app/settings/tenant/general/tenant-general-setting.module')
                    .then(m => m.TenantGeneralSettingModule)
            },
            {
                path: 'tenant_type',
                loadChildren: () => import('app/settings/tenant/tenant-type/tenant-type-setting.module')
                    .then(m => m.TenantTypeSettingModule)
            },
        ]
    }
];

export const TenantSettingRoutingModule = RouterModule.forChild(ROUTES);
