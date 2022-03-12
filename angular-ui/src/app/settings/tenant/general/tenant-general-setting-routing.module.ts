import { Routes, RouterModule } from '@angular/router';
import { TenantGeneralSettingComponent } from './tenant-general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: TenantGeneralSettingComponent },
];

export const TenantGeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
