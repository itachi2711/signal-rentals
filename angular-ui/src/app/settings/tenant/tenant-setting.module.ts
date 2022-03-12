import { NgModule } from '@angular/core';

import { SharedModule } from '../../shared/shared.module';
import { TenantSettingRoutingModule } from './tenant-setting-routing.module';
import { TenantSettingComponent } from './tenant-setting.component';

@NgModule({
    imports: [
        SharedModule,
        TenantSettingRoutingModule,
    ],
    declarations: [
        TenantSettingComponent,
    ]
})

export class TenantSettingModule {}
