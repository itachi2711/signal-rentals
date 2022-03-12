import { NgModule } from '@angular/core';
import { LandlordAreaRoutingModule } from './landlord-area-routing.module';
import { LandlordAreaComponent } from './landlord-area.component';
import { SharedModule } from '../shared/shared.module';
import { ChartsModule } from 'ng2-charts';

@NgModule({
    imports: [
        SharedModule,
        LandlordAreaRoutingModule,
        ChartsModule
    ],
    declarations: [
        LandlordAreaComponent
    ]
})

export class LandlordAreaModule {

    constructor () {
    }
}
