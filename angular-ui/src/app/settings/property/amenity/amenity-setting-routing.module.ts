import { Routes, RouterModule } from '@angular/router';
import { AmenitySettingComponent } from './amenity-setting.component';

export const ROUTES: Routes = [
    { path: '', component: AmenitySettingComponent },
];

export const AmenitySettingRoutingModule = RouterModule.forChild(ROUTES);
