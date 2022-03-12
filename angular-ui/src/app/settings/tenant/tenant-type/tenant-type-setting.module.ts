import { NgModule } from '@angular/core';

import { SharedModule } from '../../../shared/shared.module';
import { TenantTypeSettingRoutingModule } from './tenant-type-setting-routing.module';
import { TenantTypeSettingComponent } from './tenant-type-setting.component';
import { AddTenantTypeComponent } from './add/add-tenant-type.component';


@NgModule({
    imports: [
        SharedModule,
        TenantTypeSettingRoutingModule,
    ],
    declarations: [
        TenantTypeSettingComponent,
        AddTenantTypeComponent
    ],
    entryComponents: [
        AddTenantTypeComponent
    ]
})

export class TenantTypeSettingModule {

}
