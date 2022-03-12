import { NgModule } from '@angular/core';
import { TenantRoutingModule } from './tenant-routing.module';
import { TenantComponent } from './tenant.component';
import { AddTenantComponent } from './add/add-tenant.component';
import { SharedModule } from '../shared/shared.module';
import { ViewTenantGeneralComponent } from './view/general/view-tenant-general.component';
import { ViewTenantComponent } from './view/view-tenant.component';
import { TenantLeaseComponent } from './view/lease/tenant-lease.component';
import { TenantInvoiceComponent } from './view/invoice/tenant-invoice.component';
import { TenantPaymentComponent } from './view/payment/tenant-payment.component';
import { TenantDocumentComponent } from './view/document/tenant-document.component';

@NgModule({
    imports: [
        SharedModule,
        TenantRoutingModule
    ],
    declarations: [
        TenantComponent,
        AddTenantComponent,
        ViewTenantGeneralComponent,
        ViewTenantComponent,
        TenantLeaseComponent,
        TenantInvoiceComponent,
        TenantPaymentComponent,
        TenantDocumentComponent
    ]
})

export class TenantModule {

    constructor () {}
}
