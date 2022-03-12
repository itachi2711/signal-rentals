import { Routes, RouterModule } from '@angular/router';
import { TenantTypeSettingComponent } from './tenant-type-setting.component';

export const ROUTES: Routes = [
    { path: '', component: TenantTypeSettingComponent },
];

export const TenantTypeSettingRoutingModule = RouterModule.forChild(ROUTES);
