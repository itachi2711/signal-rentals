import { NgModule } from '@angular/core';

import { SharedModule } from '../../shared/shared.module';
import { LeaseSettingRoutingModule } from './lease-setting-routing.module';
import { LeaseSettingComponent } from './lease-setting.component';

@NgModule({
    imports: [
        SharedModule,
        LeaseSettingRoutingModule,
    ],
    declarations: [
        LeaseSettingComponent,
    ]
})

export class LeaseSettingModule {}
