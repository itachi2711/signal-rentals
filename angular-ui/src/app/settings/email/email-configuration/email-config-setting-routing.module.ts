import { Routes, RouterModule } from '@angular/router';
import { EmailConfigSettingComponent } from './email-config-setting.component';

export const ROUTES: Routes = [
    { path: '', component: EmailConfigSettingComponent },
];

export const EmailConfigSettingRoutingModule = RouterModule.forChild(ROUTES);
