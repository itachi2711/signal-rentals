import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';
import { NotificationService } from '../../../shared/notification.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { GeneralSettingService } from '../../general/data/general-setting.service';
import { TenantGeneralService } from './data/tenant-general.service';
import { Store } from '@ngrx/store';
import { State } from '../../../core/settings/settings.model';
import { ActivatedRoute } from '@angular/router';
import { TenantGeneralSettingModel } from './model/tenant-general-setting.model';

@Component({
    selector: 'robi-sms-general-setting',
    templateUrl: './tenant-general-setting.component.html',
    styleUrls: ['./tenant-general-setting.component.css']
})
export class TenantGeneralSettingComponent implements OnInit {
    form: FormGroup;
    formErrors: any;
    loader = false;
    setting: TenantGeneralSettingModel;
    dialogRef: MatDialogRef<ConfirmationDialogComponent>;
    // Search field
    @ViewChild('search', {static: true}) search: ElementRef;
    // pagination
    @ViewChild(MatPaginator, {static: true}) paginator: MatPaginator;
    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    constructor(private store: Store<State>, private fb: FormBuilder, private route: ActivatedRoute,
                private tenantSettingService: TenantGeneralService, private notification: NotificationService) {
        this.form = this.fb.group({
            tenant_number_prefix: ['', [Validators.required]]
        });
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        if (this.route.snapshot.data['tenantSetting']) {
            this.setting = this.route.snapshot.data['tenantSetting'];
            this.prePopulateForm(this.setting);
        }
    }

    /**
     *
     * @param setting
     */
    prePopulateForm(setting: TenantGeneralSettingModel) {
        this.form.patchValue({
            tenant_number_prefix: setting?.tenant_number_prefix,
            next_tenant_number: setting?.next_tenant_number
        });
    }

    /**
     * Update settings
     */
    update() {
        const body = Object.assign({}, this.setting, this.form.value);

        const formData = new FormData();
        formData.append('id', body.id);

        this.loader = true;
        this.tenantSettingService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Setting has been updated.');
                },
                (error) => {
                    this.loader = false;
                    if (error.payment === 0) {
                        return;
                    }
                    this.formErrors = error;
                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            if (this.form) {
                                this.form.controls[prop]?.markAsTouched();
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }
}
