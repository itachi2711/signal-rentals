import { Routes, RouterModule } from '@angular/router';
import { PropertyGeneralSettingComponent } from './property-general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: PropertyGeneralSettingComponent },
];

export const PropertyGeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
