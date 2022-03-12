import { NgModule } from '@angular/core';
import { AccountingRoutingModule } from './accounting-routing.module';
import { AccountingComponent } from './accounting.component';
import { LedgerComponent } from './ledger/ledger.component';
import { JournalComponent } from './journal/journal.component';
import { SharedModule } from '../shared/shared.module';
import { PropertyReportComponent } from './property-report/property-report.component';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';

@NgModule({
    imports: [
        SharedModule,
        AccountingRoutingModule,
        NgxMatSelectSearchModule
    ],
    declarations: [
        AccountingComponent,
        LedgerComponent,
        JournalComponent,
        PropertyReportComponent
    ],
    entryComponents: [
    ]
})

export class AccountingModule {}
