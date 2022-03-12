import { Routes, RouterModule } from '@angular/router';
import { TenantComponent } from './tenant.component';
import { AddTenantComponent } from './add/add-tenant.component';
import { ViewTenantComponent } from './view/view-tenant.component';
import { ViewTenantGeneralComponent } from './view/general/view-tenant-general.component';
import { TenantLeaseComponent } from './view/lease/tenant-lease.component';
import { TenantInvoiceComponent } from './view/invoice/tenant-invoice.component';
import { TenantPaymentComponent } from './view/payment/tenant-payment.component';
import { TenantDocumentComponent } from './view/document/tenant-document.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: TenantComponent,
    },
    { path: 'create', component: AddTenantComponent },
    {
        path: ':id',
        component: ViewTenantComponent,
        children: [
            { path: '', component: ViewTenantGeneralComponent },
            { path: 'leases', component: TenantLeaseComponent },
            { path: 'invoices', component: TenantInvoiceComponent },
            { path: 'payments', component: TenantPaymentComponent },
            { path: 'documents', component: TenantDocumentComponent },
        ]
    },
    { path: ':id/edit', component: AddTenantComponent }
];


export const TenantRoutingModule = RouterModule.forChild(ROUTES);
