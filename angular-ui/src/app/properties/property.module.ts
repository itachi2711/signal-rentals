import { NgModule } from '@angular/core';
import { PropertyRoutingModule } from './property-routing.module';
import { PropertyComponent } from './property.component';
import { AddPropertyComponent } from './add/add-property.component';
import { SharedModule } from '../shared/shared.module';
import { ViewPropertyGeneralComponent } from './view/general/view-property-general.component';
import { ViewPropertyComponent } from './view/view-property.component';
import { PropertyUnitDetailsComponent } from './add/unit-details/property-unit-details.component';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { NgxChartsModule } from '@swimlane/ngx-charts';
import { ChartistModule } from 'ng-chartist';
import { PropertyLeaseComponent } from './view/lease/property-lease.component';
import { PropertyInvoiceComponent } from './view/invoice/property-invoice.component';
import { PropertyNoticeComponent } from './view/notice/property-notice.component';
import { PropertyUnitComponent } from './view/unit/property-unit.component';

@NgModule({
    imports: [
        SharedModule,
        PropertyRoutingModule,
        NgxMatSelectSearchModule,
        NgxChartsModule,
        ChartistModule
    ],
    declarations: [
        PropertyComponent,
        AddPropertyComponent,
        ViewPropertyGeneralComponent,
        ViewPropertyComponent,
        PropertyUnitDetailsComponent,
        PropertyLeaseComponent,
        PropertyInvoiceComponent,
        PropertyNoticeComponent,
        PropertyUnitComponent
    ]
})

export class PropertyModule {

    constructor () {
    }
}
