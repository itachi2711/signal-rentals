import { Routes, RouterModule } from '@angular/router';
import { PropertyComponent } from './property.component';
import { AddPropertyComponent } from './add/add-property.component';
import { ViewPropertyComponent } from './view/view-property.component';
import { ViewPropertyGeneralComponent } from './view/general/view-property-general.component';
import { PropertyLeaseComponent } from './view/lease/property-lease.component';
import { PropertyInvoiceComponent } from './view/invoice/property-invoice.component';
import { PropertyNoticeComponent } from './view/notice/property-notice.component';
import { PropertyUnitComponent } from './view/unit/property-unit.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: PropertyComponent
    },
    {
        path: 'create',
        component: AddPropertyComponent
    },
    {
        path: ':id',
        component: ViewPropertyComponent,
        children: [
            { path: '', component: ViewPropertyGeneralComponent },
            { path: 'units', component: PropertyUnitComponent },
            { path: 'leases', component: PropertyLeaseComponent },
            { path: 'invoices', component: PropertyInvoiceComponent },
            { path: 'notices', component: PropertyNoticeComponent },
        ]
    },
    {
        path: ':id/edit',
        component: AddPropertyComponent
    },
    {
        path: ':id/edit/units',
        loadChildren: () => import('../units/unit.module').then(m => m.UnitModule),
    },
];


export const PropertyRoutingModule = RouterModule.forChild(ROUTES);
