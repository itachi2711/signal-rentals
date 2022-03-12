import { NgModule } from '@angular/core';
import { TenantProfileRoutingModule } from './tenant-profile-routing.module';
import { TenantProfileComponent } from './tenant-profile.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        TenantProfileRoutingModule
    ],
    declarations: [
        TenantProfileComponent
    ]
})

export class TenantProfileModule {
    constructor () {
    }
}
