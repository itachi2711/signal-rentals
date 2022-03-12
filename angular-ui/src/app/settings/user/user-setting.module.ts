import { NgModule } from '@angular/core';

import { UserSettingRoutingModule } from './user-setting-routing.module';
import { UserSettingComponent } from './user-setting.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        UserSettingRoutingModule,
    ],
    declarations: [
        UserSettingComponent,
    ]
})

export class UserSettingModule {}
