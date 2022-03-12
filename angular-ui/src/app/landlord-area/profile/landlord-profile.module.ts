import { NgModule } from '@angular/core';
import { LandlordProfileRoutingModule } from './landlord-profile-routing.module';
import { LandlordProfileComponent } from './landlord-profile.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        LandlordProfileRoutingModule
    ],
    declarations: [
        LandlordProfileComponent
    ]
})

export class LandlordProfileModule {
    constructor () {
    }
}
