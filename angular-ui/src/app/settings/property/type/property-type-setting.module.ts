import { NgModule } from '@angular/core';

import { PropertyTypeSettingRoutingModule } from './property-type-setting-routing.module';
import { PropertyTypeSettingComponent } from './property-type-setting.component';
import { AddPropertyTypeComponent } from './add/add-property-type.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        PropertyTypeSettingRoutingModule,
    ],
    declarations: [
        PropertyTypeSettingComponent,
        AddPropertyTypeComponent,
    ],
    entryComponents: [
        AddPropertyTypeComponent
    ]
})

export class PropertyTypeSettingModule {

}
