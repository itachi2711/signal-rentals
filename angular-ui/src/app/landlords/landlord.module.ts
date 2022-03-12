import { NgModule } from '@angular/core';
import { LandlordRoutingModule } from './landlord-routing.module';
import { LandlordComponent } from './landlord.component';
import { AddLandlordComponent } from './add/add-landlord.component';
import { SharedModule } from '../shared/shared.module';
import { LandlordDocumentComponent } from './view/documents/landlord-document.component';
import { ViewLandlordGeneralComponent } from './view/general/view-landlord-general.component';
import { LandlordPropertyComponent } from './view/property/landlord-property.component';
import { ViewLandlordComponent } from './view/view-landlord.component';

@NgModule({
    imports: [
        SharedModule,
        LandlordRoutingModule
    ],
    declarations: [
        LandlordComponent,
        AddLandlordComponent,
        LandlordDocumentComponent,
        ViewLandlordGeneralComponent,
        LandlordPropertyComponent,
        ViewLandlordComponent
    ],
    entryComponents: [
        AddLandlordComponent
    ]
})

export class LandlordModule {
    constructor () {
    }
}
