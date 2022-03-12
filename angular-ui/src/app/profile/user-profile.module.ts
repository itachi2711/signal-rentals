import { NgModule } from '@angular/core';
import { UserProfileRoutingModule } from './user-profile-routing.module';
import { SharedModule } from '../shared/shared.module';
import { UserProfileComponent } from './user-profile.component';

@NgModule({
    imports: [
        SharedModule,
        UserProfileRoutingModule
    ],
    declarations: [
        UserProfileComponent
    ]
})

export class UserProfileModule {
    constructor () {
    }
}
