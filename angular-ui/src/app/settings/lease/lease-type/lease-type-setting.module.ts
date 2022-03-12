import { NgModule } from '@angular/core';

import { SharedModule } from '../../../shared/shared.module';
import { LeaseTypeSettingRoutingModule } from './lease-type-setting-routing.module';
import { LeaseTypeSettingComponent } from './lease-type-setting.component';
import { AddLeaseTypeComponent } from './add/add-lease-type.component';


@NgModule({
    imports: [
        SharedModule,
        LeaseTypeSettingRoutingModule,
    ],
    declarations: [
        LeaseTypeSettingComponent,
        AddLeaseTypeComponent
    ],
    entryComponents: [
        AddLeaseTypeComponent
    ]
})

export class LeaseTypeSettingModule {

}
