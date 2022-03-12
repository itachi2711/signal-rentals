import { Routes, RouterModule } from '@angular/router';
import { EmailSettingComponent } from './email-setting.component';
import { EmailConfigSettingResolverService } from './email-configuration/data/email-config-setting-resolver.service';

export const ROUTES: Routes = [
    {
        path: '',
        component: EmailSettingComponent,
        children: [
            {
                path: '',
                loadChildren: () => import('app/settings/email/general/email-general-setting.module')
                    .then(m => m.EmailGeneralSettingModule)
            },
            {
                path: 'email_template',
                loadChildren: () => import('app/settings/email/email-template/email-template-setting.module')
                    .then(m => m.EmailTemplateSettingModule)
            },
            {
                path: 'email_config',
                loadChildren: () => import('app/settings/email/email-configuration/email-config-setting.module')
                    .then(m => m.EmailConfigSettingModule),
                resolve : { setting: EmailConfigSettingResolverService}
            }
        ]
    }
];

export const EmailSettingRoutingModule = RouterModule.forChild(ROUTES);
