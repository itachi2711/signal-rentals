import { Routes, RouterModule } from '@angular/router';
import { LeaseGeneralSettingComponent } from './lease-general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: LeaseGeneralSettingComponent },
];

export const LeaseGeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
