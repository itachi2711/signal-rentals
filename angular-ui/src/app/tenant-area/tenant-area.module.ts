import { NgModule } from '@angular/core';
import { TenantAreaRoutingModule } from './tenant-area-routing.module';
import { TenantAreaComponent } from './tenant-area.component';
import { SharedModule } from '../shared/shared.module';
import { ChartsModule } from 'ng2-charts';

@NgModule({
    imports: [
        SharedModule,
        TenantAreaRoutingModule,
        ChartsModule
    ],
    declarations: [
        TenantAreaComponent
    ]
})

export class TenantAreaModule {

    constructor () {
    }
}
