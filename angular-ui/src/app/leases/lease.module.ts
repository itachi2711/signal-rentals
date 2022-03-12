import { NgModule } from '@angular/core';
import { LeaseRoutingModule } from './lease-routing.module';
import { LeaseComponent } from './lease.component';
import { AddLeaseComponent } from './add/add-lease.component';
import { SharedModule } from '../shared/shared.module';
import { ViewLeaseGeneralComponent } from './view/general/view-lease-general.component';
import { ViewLeaseComponent } from './view/view-lease.component';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { AngularMultiSelectModule } from 'angular2-multiselect-dropdown';
import { LeaseInvoiceComponent } from './view/invoice/lease-invoice.component';
import { LeaseDocumentComponent } from './view/document/lease-document.component';
import { TerminateLeaseComponent } from './view/terminate/terminate-lease.component';
import { LeaseStatementComponent } from './statement/lease-statement.component';
import { PdfInvoiceComponent } from '../accounting/pdf-invoice/pdf-invoice.component';

@NgModule({
    imports: [
        SharedModule,
        LeaseRoutingModule,
        NgxMatSelectSearchModule,
        AngularMultiSelectModule
    ],
    declarations: [
        LeaseComponent,
        AddLeaseComponent,
        ViewLeaseGeneralComponent,
        ViewLeaseComponent,
        LeaseInvoiceComponent,
        LeaseDocumentComponent,
        TerminateLeaseComponent,
        LeaseStatementComponent,
        PdfInvoiceComponent
    ]
})
export class LeaseModule {
    constructor () {
    }
}
