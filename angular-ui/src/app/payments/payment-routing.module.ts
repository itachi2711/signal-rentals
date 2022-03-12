import { Routes, RouterModule } from '@angular/router';
import { PaymentComponent } from './payment.component';
import { AddPaymentComponent } from './add/add-payment.component';
import { ViewPaymentComponent } from './view/view-payment.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: PaymentComponent,
       /* resolve: {
            landlords: PropertyResolverService
        }*/
    },
    { path: 'create', component: AddPaymentComponent },
    {
        path: ':id',
        component: ViewPaymentComponent
    }
];


export const PaymentRoutingModule = RouterModule.forChild(ROUTES);
