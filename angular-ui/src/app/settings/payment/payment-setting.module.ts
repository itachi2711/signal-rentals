import { NgModule } from '@angular/core';

import { PaymentSettingRoutingModule } from './payment-setting-routing.module';
import { PaymentSettingComponent } from './payment-setting.component';
import { SharedModule } from '../../shared/shared.module';

@NgModule({
    imports: [
        SharedModule,
        PaymentSettingRoutingModule,
    ],
    declarations: [
        PaymentSettingComponent,
    ]
})

export class PaymentSettingModule {}
