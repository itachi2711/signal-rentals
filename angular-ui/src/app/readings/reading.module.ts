import { NgModule } from '@angular/core';
import { ReadingRoutingModule } from './reading-routing.module';
import { ReadingComponent } from './reading.component';
import { AddReadingComponent } from './add/add-reading.component';
import { SharedModule } from '../shared/shared.module';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { ViewReadingComponent } from './view/view-reading.component';
import { ViewReadingGeneralComponent } from './view/general/view-reading-general.component';
import { EditReadingComponent } from './edit/edit-reading.component';

@NgModule({
    imports: [
        SharedModule,
        ReadingRoutingModule,
        NgxMatSelectSearchModule
    ],
    declarations: [
        ReadingComponent,
        AddReadingComponent,
        ViewReadingComponent,
        ViewReadingGeneralComponent,
        EditReadingComponent
    ]
})

export class ReadingModule {
    constructor () {
    }
}
