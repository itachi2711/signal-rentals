import { NgModule } from '@angular/core';

import { EmailConfigSettingRoutingModule } from './email-config-setting-routing.module';
import { EmailConfigSettingComponent } from './email-config-setting.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        EmailConfigSettingRoutingModule,
    ],
    declarations: [
        EmailConfigSettingComponent,
    ],
    entryComponents: [
    ]
})

export class EmailConfigSettingModule {}
