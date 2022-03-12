import { Routes, RouterModule } from '@angular/router';
import { EmailGeneralSettingComponent } from './email-general-setting.component';

export const ROUTES: Routes = [
    { path: '', component: EmailGeneralSettingComponent },
];

export const EmailGeneralSettingRoutingModule = RouterModule.forChild(ROUTES);
