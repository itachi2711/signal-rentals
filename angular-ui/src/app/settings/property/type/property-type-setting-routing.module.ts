import { Routes, RouterModule } from '@angular/router';
import { PropertyTypeSettingComponent } from './property-type-setting.component';

export const ROUTES: Routes = [
    { path: '', component: PropertyTypeSettingComponent },
];

export const PropertyTypeSettingRoutingModule = RouterModule.forChild(ROUTES);
