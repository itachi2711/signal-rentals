import { NgModule } from '@angular/core';

import { PropertyGeneralSettingRoutingModule } from './property-general-setting-routing.module';
import { PropertyGeneralSettingComponent } from './property-general-setting.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        PropertyGeneralSettingRoutingModule,
    ],
    declarations: [
        PropertyGeneralSettingComponent,
    ],
    entryComponents: [
    ]
})

export class PropertyGeneralSettingModule {}
