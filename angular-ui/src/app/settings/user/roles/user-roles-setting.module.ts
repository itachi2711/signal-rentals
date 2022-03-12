import { NgModule } from '@angular/core';

import { UserRolesSettingRoutingModule } from './user-roles-setting-routing.module';
import { UserRolesSettingComponent } from './user-roles-setting.component';
import { AddRoleComponent } from './add/add-role.component';
import { EditRoleComponent } from './edit/edit-role.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        UserRolesSettingRoutingModule,
    ],
    declarations: [
        UserRolesSettingComponent,
        AddRoleComponent,
        EditRoleComponent
    ],
    entryComponents: [
        AddRoleComponent,
        EditRoleComponent
    ]
})

export class UserRolesSettingModule {}
