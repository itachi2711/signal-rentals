import { Routes, RouterModule } from '@angular/router';
import { UnitTypeSettingComponent } from './unit-type-setting.component';

export const ROUTES: Routes = [
    { path: '', component: UnitTypeSettingComponent },
];

export const UnitTypeSettingRoutingModule = RouterModule.forChild(ROUTES);
