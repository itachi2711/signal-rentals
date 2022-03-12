import { Routes, RouterModule } from '@angular/router';
import { InvoiceComponent } from './invoice.component';
import { AddInvoiceComponent } from './add/add-invoice.component';
import { ViewInvoiceComponent } from './view/view-invoice.component';
import { ViewInvoiceGeneralComponent } from './view/general/view-invoice-general.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: InvoiceComponent
    },
    { path: 'create', component: AddInvoiceComponent },
    {
        path: ':id',
        component: ViewInvoiceComponent,
        children: [
            { path: '', component: ViewInvoiceGeneralComponent }
        ]
    },
];


export const InvoiceRoutingModule = RouterModule.forChild(ROUTES);
