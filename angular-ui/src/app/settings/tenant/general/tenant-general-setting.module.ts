import { NgModule } from '@angular/core';

import { TenantGeneralSettingRoutingModule } from './tenant-general-setting-routing.module';
import { TenantGeneralSettingComponent } from './tenant-general-setting.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        TenantGeneralSettingRoutingModule,
    ],
    declarations: [
        TenantGeneralSettingComponent,
    ],
    entryComponents: [
    ]
})

export class TenantGeneralSettingModule {}
