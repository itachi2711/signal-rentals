import { Routes, RouterModule } from '@angular/router';
import { LeaseTypeSettingComponent } from './lease-type-setting.component';

export const ROUTES: Routes = [
    { path: '', component: LeaseTypeSettingComponent },
];

export const LeaseTypeSettingRoutingModule = RouterModule.forChild(ROUTES);
