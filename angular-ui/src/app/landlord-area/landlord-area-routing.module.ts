import { Routes, RouterModule } from '@angular/router';
import { LandlordAreaComponent } from './landlord-area.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: LandlordAreaComponent,
       /* children: [
            {
                path: 'landlord',
                loadChildren: () => import('./property/landlord-area-property.module').then(m => m.LandlordAreaPropertyModule)
            },
            {
                path: 'properties',
                loadChildren: () => import('./property/landlord-area-property.module').then(m => m.LandlordAreaPropertyModule)
            },
            {
                path: 'payments',
                // loadChildren: () => import('app/settings/expense/expense-setting.module').then(m => m.ExpenseSettingModule)
            }
        ]*/
    }
];


export const LandlordAreaRoutingModule = RouterModule.forChild(ROUTES);
