import { NgModule } from '@angular/core';

import { EmailGeneralSettingRoutingModule } from './email-general-setting-routing.module';
import { EmailGeneralSettingComponent } from './email-general-setting.component';
import { SharedModule } from '../../../shared/shared.module';
import { EditEmailGeneralComponent } from './edit/edit-email-general.component';

@NgModule({
    imports: [
        SharedModule,
        EmailGeneralSettingRoutingModule,
    ],
    declarations: [
        EmailGeneralSettingComponent,
        EditEmailGeneralComponent
    ],
    entryComponents: [
    ]
})

export class EmailGeneralSettingModule {}
