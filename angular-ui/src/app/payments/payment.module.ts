import { NgModule } from '@angular/core';
import { PaymentRoutingModule } from './payment-routing.module';
import { PaymentComponent } from './payment.component';
import { AddPaymentComponent } from './add/add-payment.component';
import { SharedModule } from '../shared/shared.module';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { PaymentDetailComponent } from './details/payment-detail.component';
import { StatusChangeComponent } from './status-change/status-change.component';
import { ViewPaymentComponent } from './view/view-payment.component';

@NgModule({
    imports: [
        SharedModule,
        PaymentRoutingModule,
        NgxMatSelectSearchModule
    ],
    declarations: [
        PaymentComponent,
        AddPaymentComponent,
        PaymentDetailComponent,
        StatusChangeComponent,
        ViewPaymentComponent
    ],
    entryComponents: [
    ]
})

export class PaymentModule {

    constructor () {}
}
