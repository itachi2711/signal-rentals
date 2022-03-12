import { NgModule } from '@angular/core';

import { PaymentMethodSettingRoutingModule } from './payment-method-setting-routing.module';
import { PaymentMethodSettingComponent } from './payment-method-setting.component';
import { AddPaymentMethodComponent } from './add/add-payment-method.component';
import { SharedModule } from '../../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        PaymentMethodSettingRoutingModule,
    ],
    declarations: [
        PaymentMethodSettingComponent,
        AddPaymentMethodComponent
    ],
    entryComponents: [
        AddPaymentMethodComponent
    ]
})

export class PaymentMethodSettingModule {}
