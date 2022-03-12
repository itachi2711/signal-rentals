import { Routes, RouterModule } from '@angular/router';
import { VacateComponent } from './vacate.component';
import { AddVacateComponent } from './add/add-vacate.component';
import { ViewVacateComponent } from './view/view-vacate.component';
import { ViewVacateGeneralComponent } from './view/general/view-vacate-general.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: VacateComponent
    },
    { path: 'create', component: AddVacateComponent },
    {
        path: ':id',
        component: ViewVacateComponent,
        children: [
            { path: '', component: ViewVacateGeneralComponent },
        ]
    },
];


export const VacateRoutingModule = RouterModule.forChild(ROUTES);
