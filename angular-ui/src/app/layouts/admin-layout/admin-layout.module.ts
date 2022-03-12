import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AdminLayoutRoutes } from './admin-layout.routing';
import { DashboardComponent } from '../../dashboard/dashboard.component';

import { MatButtonModule } from '@angular/material/button';
import { MatRippleModule } from '@angular/material/core';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatTooltipModule } from '@angular/material/tooltip';
import { MaterialModule } from '../../shared/material.module';
import { SharedModule } from '../../shared/shared.module';
import { NgxMatSelectSearchModule } from 'ngx-mat-select-search';
import { ChartsModule } from 'ng2-charts';
@NgModule({
  imports: [
    /*  CommonModule,
    FormsModule,
   MatButtonModule,
    MatRippleModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatTooltipModule,
    MaterialModule,*/
    SharedModule,
    RouterModule.forChild(AdminLayoutRoutes),
    NgxMatSelectSearchModule,
    ChartsModule
  ],
  declarations: [
    DashboardComponent
  ]
})

export class AdminLayoutModule {}
