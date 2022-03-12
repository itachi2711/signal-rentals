import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';

import { Observable } from 'rxjs';

import { NotificationService } from '../../../shared/notification.service';
import { FormBuilder, FormGroup } from '@angular/forms';
import { GeneralSettingService } from '../../general/data/general-setting.service';
import { select, Store } from '@ngrx/store';
import { selectSettings } from '../../../core/settings/settings.selectors';
import { SettingsState, State } from '../../../core/settings/settings.model';
import { ActivatedRoute } from '@angular/router';
import { PropertyGeneralSettingModel } from './model/property-general-setting.model';

@Component({
    selector: 'robi-property-general-setting',
    templateUrl: './property-general-setting.component.html',
    styleUrls: ['./property-general-setting.component.css']
})
export class PropertyGeneralSettingComponent implements OnInit {
    form: FormGroup;
    formErrors: any;
    loader = false;

    setting: PropertyGeneralSettingModel;
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
    dueON = Array.from({length: (29 - 1)}, (v, k) => k + 1);

    constructor(private store: Store<State>, private fb: FormBuilder, private route: ActivatedRoute,
                private generalSettingService: GeneralSettingService, private notification: NotificationService) {

        this.form = this.fb.group({
            default_commission: [''],
            commission_type: [''],
            property_prefix: [''],
            next_property_number: ['']
        });
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {

        this.settings$ = this.store.pipe(select(selectSettings));

        if (this.route.snapshot.data['propertySetting']) {
            this.setting = this.route.snapshot.data['propertySetting'].data;
            this.prePopulateForm(this.setting);
            this.settingId = this.setting.id;
            this.invoiceDay = this.setting.invoice_day;
        }
    }

    /**
     *
     * @param setting
     */
    prePopulateForm(setting: PropertyGeneralSettingModel) {
        this.setting = setting;

        this.form.patchValue({
            invoice_day: this.setting.invoice_day
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
        this.generalSettingService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    // notify success
                    this.notification.showNotification('success', 'Success !! Admin Setting has been updated.');
                    setTimeout(() => {
                        this.notification.showNotification('success', 'Action !! Login to continue ...');
                    }, 1000);
                },
                (error) => {
                    this.loader = false;

                    if (error.payment === 0) {
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.form) {
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }
}
