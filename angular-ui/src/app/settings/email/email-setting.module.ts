import { NgModule } from '@angular/core';

import { SharedModule } from '../../shared/shared.module';
import { EmailSettingRoutingModule } from './email-setting-routing.module';
import { EmailSettingComponent } from './email-setting.component';

@NgModule({
    imports: [
        SharedModule,
        EmailSettingRoutingModule,
    ],
    declarations: [
        EmailSettingComponent,
    ]
})

export class EmailSettingModule {}
