import { NgModule } from '@angular/core';

import { LeaseGeneralSettingRoutingModule } from './lease-general-setting-routing.module';
import { LeaseGeneralSettingComponent } from './lease-general-setting.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        LeaseGeneralSettingRoutingModule,
    ],
    declarations: [
        LeaseGeneralSettingComponent,
    ],
    entryComponents: [
    ]
})

export class LeaseGeneralSettingModule {}
