import { NgModule } from '@angular/core';

import { UserPermissionsSettingRoutingModule } from './user-permissions-setting-routing.module';
import { UserPermissionsSettingComponent } from './user-permissions-setting.component';
import { EditPermissionComponent } from './edit/edit-permission.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        UserPermissionsSettingRoutingModule,
    ],
    declarations: [
        UserPermissionsSettingComponent,
        EditPermissionComponent
    ],
    entryComponents: [
        EditPermissionComponent
    ]
})

export class UserPermissionsSettingModule {}
