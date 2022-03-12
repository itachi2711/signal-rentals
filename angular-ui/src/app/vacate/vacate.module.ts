import { NgModule } from '@angular/core';
import { VacateRoutingModule } from './vacate-routing.module';
import { VacateComponent } from './vacate.component';
import { AddVacateComponent } from './add/add-vacate.component';
import { SharedModule } from '../shared/shared.module';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { ViewVacateComponent } from './view/view-vacate.component';
import { ViewVacateGeneralComponent } from './view/general/view-vacate-general.component';

@NgModule({
    imports: [
        SharedModule,
        VacateRoutingModule,
        NgxMatSelectSearchModule
    ],
    declarations: [
        VacateComponent,
        AddVacateComponent,
        ViewVacateComponent,
        ViewVacateGeneralComponent
    ],
    entryComponents: [
        AddVacateComponent
    ]
})

export class VacateModule {
    constructor () {
    }
}
