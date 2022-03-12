import { NgModule } from '@angular/core';

import { UserGeneralSettingRoutingModule } from './user-general-setting-routing.module';
import { UserGeneralSettingComponent } from './user-general-setting.component';
import { AddUserComponent } from './add/add-user.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        UserGeneralSettingRoutingModule,
    ],
    declarations: [
        UserGeneralSettingComponent,
        AddUserComponent
    ],
    entryComponents: [
        AddUserComponent
    ]
})

export class UserGeneralSettingModule {}
