import { NgModule } from '@angular/core';

import { PropertySettingRoutingModule } from './property-setting-routing.module';
import { PropertySettingComponent } from './property-setting.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        PropertySettingRoutingModule,
    ],
    declarations: [
        PropertySettingComponent,
    ]
})

export class PropertySettingModule {}
