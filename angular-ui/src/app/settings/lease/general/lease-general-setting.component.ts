import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';
import { Observable } from 'rxjs';
import { NotificationService } from '../../../shared/notification.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { select, Store } from '@ngrx/store';
import { selectSettings } from '../../../core/settings/settings.selectors';
import { SettingsState, State } from '../../../core/settings/settings.model';
import { ActivatedRoute } from '@angular/router';
import { LeaseGeneralSettingModel } from './model/lease-general-setting.model';
import { ThemePalette } from '@angular/material/core';
import { LeaseSettingService } from './data/lease-setting.service';

@Component({
    selector: 'robi-lease-general-setting',
    templateUrl: './lease-general-setting.component.html',
    styleUrls: ['./lease-general-setting.component.css']
})
export class LeaseGeneralSettingComponent implements OnInit {
    form: FormGroup;
    formErrors: any;
    loader = false;

    setting: LeaseGeneralSettingModel;
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

    color: ThemePalette = 'accent';
    nextPeriodBilling: boolean;

    constructor(private store: Store<State>, private fb: FormBuilder, private route: ActivatedRoute,
                private leaseSettingService: LeaseSettingService, private notification: NotificationService) {

        this.form = this.fb.group({
            lease_number_prefix: ['', [Validators.required]],
            next_lease_number: [''],
            invoice_number_prefix: [''],
            invoice_footer: [''],
            invoice_terms: [''],
            show_payment_method_on_invoice: [''],
            generate_invoice_on: [''],
            next_period_billing: [''],
            skip_starting_period: ['']
        });
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.settings$ = this.store.pipe(select(selectSettings));

        if (this.route.snapshot.data['leaseSetting']) {
            this.setting = this.route.snapshot.data['leaseSetting'];
            this.prePopulateForm(this.setting);
            this.settingId = this.setting.id;
        }
    }

    /**
     *
     * @param setting
     */
    prePopulateForm(setting: LeaseGeneralSettingModel) {
        this.setting = setting;
        this.form.patchValue({
            lease_number_prefix: this.setting?.lease_number_prefix,
            next_lease_number: this.setting?.next_lease_number,
            invoice_number_prefix: this.setting?.invoice_number_prefix,
            invoice_footer: this.setting?.invoice_footer,
            invoice_terms: this.setting?.invoice_terms,
            show_payment_method_on_invoice: this.setting?.show_payment_method_on_invoice,
            generate_invoice_on: this.setting?.generate_invoice_on,
            next_period_billing: this.setting?.next_period_billing,
            skip_starting_period: this.setting?.skip_starting_period
        });
        this.nextPeriodBilling = this.setting?.next_period_billing;
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
        this.leaseSettingService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Lease Setting has been updated.');
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
                                this.form.controls[prop]?.markAsTouched();
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }
}
