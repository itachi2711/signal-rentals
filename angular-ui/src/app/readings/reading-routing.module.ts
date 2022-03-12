import { Routes, RouterModule } from '@angular/router';
import { ReadingComponent } from './reading.component';
import { AddReadingComponent } from './add/add-reading.component';
import { ViewReadingComponent } from './view/view-reading.component';
import { ViewReadingGeneralComponent } from './view/general/view-reading-general.component';

export const ROUTES: Routes = [
    {
        path: '',
        component: ReadingComponent
    },
    { path: 'create', component: AddReadingComponent },
    {
        path: ':id',
        component: ViewReadingComponent,
        children: [
            { path: '', component: ViewReadingGeneralComponent },
        ]
    },
];


export const ReadingRoutingModule = RouterModule.forChild(ROUTES);
