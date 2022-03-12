import { Routes, RouterModule } from '@angular/router';
import { LandlordComponent } from './landlord.component';
import { AddLandlordComponent } from './add/add-landlord.component';
import { ViewLandlordComponent } from './view/view-landlord.component';
import { ViewLandlordGeneralComponent } from './view/general/view-landlord-general.component';
import { LandlordDocumentComponent } from './view/documents/landlord-document.component';
import { LandlordPropertyComponent } from './view/property/landlord-property.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: LandlordComponent
    },
    { path: 'create', component: AddLandlordComponent },
    {
        path: ':id',
        component: ViewLandlordComponent,
        children: [
            { path: '', component: ViewLandlordGeneralComponent },
            { path: 'documents', component: LandlordDocumentComponent },
            { path: 'properties', component: LandlordPropertyComponent }
        ]
    },
    { path: ':id/edit', component: AddLandlordComponent },
];


export const LandlordRoutingModule = RouterModule.forChild(ROUTES);
