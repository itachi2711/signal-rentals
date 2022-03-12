import { Routes, RouterModule } from '@angular/router';
import { UserPermissionsSettingComponent } from './user-permissions-setting.component';

export const ROUTES: Routes = [
    { path: '', component: UserPermissionsSettingComponent },
];

export const UserPermissionsSettingRoutingModule = RouterModule.forChild(ROUTES);
