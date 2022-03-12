import { Routes, RouterModule } from '@angular/router';
import { PaymentSettingComponent } from './payment-setting.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: PaymentSettingComponent,
        children: [
            {
                path: '',
                loadChildren: () => import('app/settings/payment/payment-method/payment-method-setting.module')
                    .then(m => m.PaymentMethodSettingModule)
            }
        ]
    }
];

export const PaymentSettingRoutingModule = RouterModule.forChild(ROUTES);
