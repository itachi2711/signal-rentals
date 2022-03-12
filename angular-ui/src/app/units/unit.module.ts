import { NgModule } from '@angular/core';
import { UnitRoutingModule } from './unit-routing.module';
import { UnitComponent } from './unit.component';
import { SharedModule } from '../shared/shared.module';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { NgxChartsModule } from '@swimlane/ngx-charts';
import { ChartistModule } from 'ng-chartist';
import { AddUnitComponent } from './add/add-unit.component';

@NgModule({
    imports: [
        SharedModule,
        UnitRoutingModule,
        NgxMatSelectSearchModule,
        NgxChartsModule,
        ChartistModule
    ],
    declarations: [
        UnitComponent,
        AddUnitComponent
    ]
})

export class UnitModule {

    constructor () {
    }
}
