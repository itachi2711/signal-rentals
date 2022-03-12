import { Routes, RouterModule } from '@angular/router';
import { PaymentMethodSettingComponent } from './payment-method-setting.component';

export const ROUTES: Routes = [
    { path: '', component: PaymentMethodSettingComponent },
];

export const PaymentMethodSettingRoutingModule = RouterModule.forChild(ROUTES);
