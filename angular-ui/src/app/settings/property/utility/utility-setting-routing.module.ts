import { Routes, RouterModule } from '@angular/router';
import { UtilitySettingComponent } from './utility-setting.component';

export const ROUTES: Routes = [
    { path: '', component: UtilitySettingComponent },
];

export const UtilitySettingRoutingModule = RouterModule.forChild(ROUTES);
