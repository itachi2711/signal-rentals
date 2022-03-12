import { Routes, RouterModule } from '@angular/router';
import { LeaseSettingComponent } from './lease-setting.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: LeaseSettingComponent,
        children: [
            {
                path: '',
                loadChildren: () => import('app/settings/lease/general/lease-general-setting.module')
                    .then(m => m.LeaseGeneralSettingModule)
            },
            {
                path: 'lease_type',
                loadChildren: () => import('app/settings/lease/lease-type/lease-type-setting.module')
                    .then(m => m.LeaseTypeSettingModule)
            }
        ]
    }
];

export const LeaseSettingRoutingModule = RouterModule.forChild(ROUTES);
