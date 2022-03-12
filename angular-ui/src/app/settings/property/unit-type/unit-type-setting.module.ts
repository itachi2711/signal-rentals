import { NgModule } from '@angular/core';
import { UnitTypeSettingRoutingModule } from './unit-type-setting-routing.module';
import { UnitTypeSettingComponent } from './unit-type-setting.component';
import { SharedModule } from '../../../shared/shared.module';
import { AddUnitTypeComponent } from './add/add-unit-type.component';


@NgModule({
    imports: [
        SharedModule,
        UnitTypeSettingRoutingModule,
    ],
    declarations: [
        UnitTypeSettingComponent,
        AddUnitTypeComponent
    ],
    entryComponents: [
        AddUnitTypeComponent
    ]
})

export class UnitTypeSettingModule {
}
