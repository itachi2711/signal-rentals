import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';

import { Observable } from 'rxjs';

import { NotificationService } from '../../../shared/notification.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { GeneralSettingService } from '../../general/data/general-setting.service';
import { Store } from '@ngrx/store';
import { SettingsState, State } from '../../../core/settings/settings.model';
import { ActivatedRoute } from '@angular/router';
import { EmailConfigSettingModel } from './model/email-config-setting.model';
import { EmailConfigService } from './data/email-config.service';

@Component({
    selector: 'robi-email-general-setting',
    templateUrl: './email-config-setting.component.html',
    styleUrls: ['./email-config-setting.component.css']
})
export class EmailConfigSettingComponent implements OnInit {
    form: FormGroup;
    smsConfigForm: FormGroup;
    formErrors: any;
    loader = false;

    setting: EmailConfigSettingModel;
    settings$: Observable<SettingsState>;

    settingId: string;
    invoiceDay: any;


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

    // Data for the list table display

    roles: any = [];
    employees: any = [];
    branches: any = [];

    constructor(private store: Store<State>, private fb: FormBuilder, private route: ActivatedRoute,
                private emailConfigService: EmailConfigService, private notification: NotificationService) {
        this.form = this.fb.group({
            driver: ['', [Validators.required,
                Validators.minLength(2)]],
            host: ['', [Validators.required,
                Validators.minLength(2)]],
            username: [''],
            password: [''],
            port: [''],
            from_address: [''],
            from_name: ['']
        });

        this.smsConfigForm = this.fb.group({
            gateway: [{value: '', disabled: true}],
            template: ['', [Validators.required,
                Validators.minLength(2)]],
            body: [''],
            tags: [{value: '', disabled: true}]
        });
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {

        if (this.route.snapshot.data['setting']) {
            this.prePopulateForm(this.route.snapshot.data['setting']);
        }
    }

    /**
     *
     * @param setting
     */
    prePopulateForm(setting: EmailConfigSettingModel) {
        this.setting = setting;

        this.form.patchValue({
            driver: this.setting.driver,
            host: this.setting.host,
            username: this.setting.username,
            password: this.setting.password,
            port: this.setting.port,
            from_address: this.setting.from_address,
            from_name: this.setting.from_name
        });
    }

    onSubmit() {}

    /**
     * Update settings
     */
    update() {
        const body = Object.assign({}, this.setting, this.form.value);

        const formData = new FormData();
        formData.append('id', body.id);

        this.loader = true;
        this.emailConfigService.create(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Email Config Setting has been updated.');
                },
                (error) => {
                    this.loader = false;

                    if (error.payment === 0) {
                        return;
                    }
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
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
