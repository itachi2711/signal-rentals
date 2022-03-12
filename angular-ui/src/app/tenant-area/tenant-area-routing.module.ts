import { Routes, RouterModule } from '@angular/router';
import { TenantAreaComponent } from './tenant-area.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: TenantAreaComponent
    },
];


export const TenantAreaRoutingModule = RouterModule.forChild(ROUTES);
