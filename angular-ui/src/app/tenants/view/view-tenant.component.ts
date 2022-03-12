import { AfterViewInit, Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { TenantModel } from '../models/tenant-model';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { AddTenantComponent } from '../add/add-tenant.component';
import { TenantService } from '../data/tenant.service';

@Component({
    selector: 'robi-view-tenant',
    styleUrls: ['./view-tenant.component.scss'],
    templateUrl: './view-tenant.component.html'
})
export class ViewTenantComponent implements OnInit, AfterViewInit  {

    form: FormGroup;

    formErrors: any;

    loader = false;

    tenantID: string;
    tenantData$: Observable<TenantModel>;
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private notification: NotificationService,
                private tenantService: TenantService,
                private router: Router, private route: ActivatedRoute) {
    }

    ngOnInit() {
        this.tenantID = this.route.snapshot.paramMap.get('id');
        this.tenantService.selectedTenantChanges$.subscribe(data => {
            if (data) {
                this.tenantData$ = of(data);
            }
            if (!data) {
                this.tenantService.getById(this.tenantID).subscribe(tenant => {
                    this.tenantData$ = of(tenant);
                    this.tenantService.changeSelectedTenant(tenant);
                });
            }
        });
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, property?: TenantModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {property,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddTenantComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

    onOutletActivated(componentReference) {
    }

    ngAfterViewInit(): void {}

    onSelected(tenant: TenantModel) {
        this.tenantService.changeSelectedTenant(tenant);
    }
}
