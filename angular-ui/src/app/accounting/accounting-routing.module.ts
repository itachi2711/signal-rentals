import { Routes, RouterModule } from '@angular/router';
import { AccountingComponent } from './accounting.component';
import { LedgerComponent } from './ledger/ledger.component';
import { JournalComponent } from './journal/journal.component';
import { PropertyReportComponent } from './property-report/property-report.component';

export const ROUTES: Routes = [
    {
        path: '', component: AccountingComponent,
        children: [
            { path: '', component: PropertyReportComponent},
            { path: 'ledger', component: LedgerComponent},
              { path: 'journal', component: JournalComponent }
        ]
    }
];
export const AccountingRoutingModule = RouterModule.forChild(ROUTES);
