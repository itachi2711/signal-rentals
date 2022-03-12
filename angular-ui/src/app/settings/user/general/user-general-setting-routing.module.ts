import { Routes, RouterModule } from '@angular/router';
import { UserGeneralSettingComponent } from './user-general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: UserGeneralSettingComponent },
];

export const UserGeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
