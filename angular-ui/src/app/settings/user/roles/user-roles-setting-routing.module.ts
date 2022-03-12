import { Routes, RouterModule } from '@angular/router';
import { UserRolesSettingComponent } from './user-roles-setting.component';

export const ROUTES: Routes = [
    { path: '', component: UserRolesSettingComponent },
];

export const UserRolesSettingRoutingModule = RouterModule.forChild(ROUTES);
