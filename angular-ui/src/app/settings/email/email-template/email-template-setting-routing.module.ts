import { Routes, RouterModule } from '@angular/router';
import { EmailTemplateSettingComponent } from './email-template-setting.component';

export const ROUTES: Routes = [
    { path: '', component: EmailTemplateSettingComponent },
];

export const EmailTemplateSettingRoutingModule = RouterModule.forChild(ROUTES);
