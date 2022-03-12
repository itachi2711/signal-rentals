import { NgModule } from '@angular/core';
import { InvoiceRoutingModule } from './invoice-routing.module';
import { InvoiceComponent } from './invoice.component';
import { AddInvoiceComponent } from './add/add-invoice.component';
import { SharedModule } from '../shared/shared.module';
import { ViewInvoiceGeneralComponent } from './view/general/view-invoice-general.component';
import { ViewInvoiceComponent } from './view/view-invoice.component';
import { UtilityBillUnitDetailsComponent } from './add/unit-details/utility-bill-unit-details.component';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { WaiveInvoiceComponent } from './view/waive/waive-invoice.component';

@NgModule({
    imports: [
        SharedModule,
        InvoiceRoutingModule,
        NgxMatSelectSearchModule
    ],
    declarations: [
        InvoiceComponent,
        AddInvoiceComponent,
        ViewInvoiceGeneralComponent,
        ViewInvoiceComponent,
        UtilityBillUnitDetailsComponent,
        WaiveInvoiceComponent
    ]
})

export class InvoiceModule {
    constructor () {
    }
}
