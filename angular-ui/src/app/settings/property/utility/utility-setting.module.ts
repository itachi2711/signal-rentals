import { NgModule } from '@angular/core';
import { UtilitySettingRoutingModule } from './utility-setting-routing.module';
import { UtilitySettingComponent } from './utility-setting.component';
import { SharedModule } from '../../../shared/shared.module';
import { AddUtilityComponent } from './add/add-utility.component';


@NgModule({
    imports: [
        SharedModule,
        UtilitySettingRoutingModule,
    ],
    declarations: [
        UtilitySettingComponent,
        AddUtilityComponent
    ],
    entryComponents: [
        AddUtilityComponent
    ]
})

export class UtilitySettingModule {
}
