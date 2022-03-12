import { Routes, RouterModule } from '@angular/router';
import { GeneralSettingComponent } from './general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: GeneralSettingComponent },
];

export const GeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
