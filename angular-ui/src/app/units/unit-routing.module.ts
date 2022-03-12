import { Routes, RouterModule } from '@angular/router';
import { UnitComponent } from './unit.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: UnitComponent
    }
];


export const UnitRoutingModule = RouterModule.forChild(ROUTES);
