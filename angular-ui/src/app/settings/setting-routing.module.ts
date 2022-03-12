import { Routes, RouterModule } from '@angular/router';
import { SettingComponent } from './setting.component';
import { GeneralSettingResolverService } from './general/data/general-setting-resolver.service';
import { LeaseGeneralSettingResolverService } from './lease/general/data/lease-general-setting-resolver.service';
import { TenantGeneralSettingResolverService } from './tenant/general/data/tenant-general-setting-resolver.service';
import { PropertyGeneralSettingResolverService } from './property/general/data/property-general-setting-resolver.service';
export const ROUTES: Routes = [
    {
        path: '',
        component: SettingComponent,
        children: [
            {
                path: '',
                loadChildren: () => import('app/settings/general/general-setting.module').then(m => m.GeneralSettingModule),
                resolve : { setting: GeneralSettingResolverService}
            },
            {
                path: 'property',
                loadChildren: () => import('app/settings/property/property-setting.module')
                    .then(m => m.PropertySettingModule),
                resolve : { leaseSetting: PropertyGeneralSettingResolverService}
            },
            {
                path: 'lease',
                loadChildren: () => import('app/settings/lease/lease-setting.module')
                    .then(m => m.LeaseSettingModule),
                resolve : { leaseSetting: LeaseGeneralSettingResolverService}
            },
            {
                path: 'tenant',
                loadChildren: () => import('app/settings/tenant/tenant-setting.module')
                    .then(m => m.TenantSettingModule),
                resolve : { tenantSetting: TenantGeneralSettingResolverService}
            },
            {
                path: 'email',
                loadChildren: () => import('app/settings/email/email-setting.module')
                    .then(m => m.EmailSettingModule)
            },
            {
                path: 'payments',
                loadChildren: () => import('app/settings/payment/payment-setting.module')
                    .then(m => m.PaymentSettingModule)
            },
            {
                path: 'user',
                loadChildren: () => import('app/settings/user/user-setting.module').then(m => m.UserSettingModule)
            }
        ]
    }
];

export const SettingRoutingModule = RouterModule.forChild(ROUTES);
