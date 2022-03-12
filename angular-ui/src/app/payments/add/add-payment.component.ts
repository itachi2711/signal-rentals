import { Component, Inject, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

import * as moment from 'moment';
import { Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, distinctUntilChanged, map, delay, tap, filter, takeUntil } from 'rxjs/operators';
import { PaymentModel } from '../models/payment-model';
import { PaymentService } from '../data/payment.service';
import { NotificationService } from '../../shared/notification.service';
import { PaymentMethodService } from '../../settings/payment/payment-method/data/payment-method.service';
import { TenantService } from '../../tenants/data/tenant.service';
import { LeaseService } from '../../leases/data/lease.service';
import { TenantModel } from '../../tenants/models/tenant-model';
import { LeaseModel } from '../../leases/models/lease-model';


@Component({
    selector: 'robi-add-payment',
    styles: [],
    templateUrl: './add-payment.component.html'
})
export class AddPaymentComponent implements OnInit, OnDestroy  {

    form: FormGroup;

    formErrors: any;

    payment: PaymentModel;

    loader = false;
    balanceLoader = false;
    accountBalance$: Observable<any>;
    loanAccountBalance$: Observable<any>;
    lease$ = of();
    tenant$ = of();
    tenantID: string;
    leaseID: string;
    propertyID: string;
    leaseNumber: string;
    isBank = false;

    paymentMethods: any = [];
    paymentMethods$: Observable<any>;

    banks: any = [];

    tenants: any = [];
    memberLoans$: Observable<any>;
    tenantActiveLeases$: Observable<any>;

    phoneNumber: string;
    tenantType: string;
    idNumber: string;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;

    /** control for filter for server side. */
    public tenantServerSideFilteringCtrl: FormControl = new FormControl();

    /** indicate search operation is in progress */
    public searching = false;

    /** list of banks filtered after simulating server side search */
    public  filteredServerSideTenants: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    tenantsFiltered$: Observable<any>;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private paymentService: PaymentService,
                private leaseService: LeaseService,
                private tenantService: TenantService,
                private notification: NotificationService,
                private paymentMethodService: PaymentMethodService,
                private dialogRef: MatDialogRef<AddPaymentComponent>) {
    }

    ngOnInit() {
        this.paymentMethods$ = this.paymentMethodService.list(['payment_method_name', 'payment_method_display_name']);

        this.tenantServerSideFilteringCtrl.valueChanges
            .pipe(
                filter(search => !!search),
                tap(() => this.searching = true),
                takeUntil(this._onDestroy),
                debounceTime(200),
                distinctUntilChanged(),
                map(search => {
                    search = search.toLowerCase();
                    this.tenantsFiltered$ =  this.tenantService.search(search);
                }),
                delay(500)
            )
            .subscribe(filteredTenants => {
                    this.searching = false;
                    this.filteredServerSideTenants.next(filteredTenants);
                },
                error => {
                    this.searching = false;
                });

        this.form = this.fb.group({
            tenant: ['', [Validators.required,
                Validators.minLength(1)]],
            lease: ['', [Validators.required,
                Validators.minLength(1)]],

            payment_method_id: ['', [Validators.required,
                Validators.minLength(1)]],
            amount: ['', [Validators.required,
                Validators.minLength(1)]],
            payment_date: [moment(), Validators.required],
            notes: [''],
            paid_by: [''],
            reference_number: [''],

            bank_fields: this.fb.group({
                cheque_number: [''],
                cheque_date: [moment(), Validators.required],
                bank_name: [''],
                bank_branch: ['']
            })
        });
    }

    /**
     * @param tenant
     */
    onTenantItemChange(tenant: TenantModel) {
        this.tenantID = tenant?.id;
        const tenantID = tenant?.id;
        this.tenantActiveLeases$ = of();
        this.lease$ = of();
        this.tenant$ = of();

        this.tenantsFiltered$.subscribe(tenants => {
            this.tenantActiveLeases$ =  of(tenants.find((item: any) => item.id === tenant?.id).leases);
            this.tenant$ = of(tenants.find((item: any) => item.id === tenant?.id));
        });

        this.form.patchValue({
            lease_id: '',
        });

        this.balanceLoader = true;
       // this.accountBalance$ =  this.memberService.balance({id: value});

        this.loanAccountBalance$ = of (null);
    }

    /**
     * @param lease
     */
    onLeaseItemChange(lease: LeaseModel) {
        this.leaseID = lease?.id;
        this.propertyID = lease?.property_id;
        this.leaseNumber = lease?.lease_number;
       // this.loanAccountBalance$ =  this.loanService.loanAccountBalance({loan_id: value});
        this.lease$ = this.leaseService.getById(lease?.id);
    }

    /**
     *
     * @param value
     */
    onPaymentMethodItemChange(value) {
       // const paymentMethod = this.paymentMethods.find((item: any) => item.id === value)?.payment_method_name;
       // this.isBank = paymentMethod === 'BANK';
    }

    /**
     *
     */
    save() {
        this.dialogRef.close(this.form.value);
    }

    /**
     *
     */
    close() {
        this.dialogRef.close();
    }

    /**
     * Create payment
     */
    create() {
        const body = Object.assign({}, this.payment, this.form.value);
        body.tenant_id = this.tenantID;
        body.property_id = this.propertyID;
        body.lease_id = this.leaseID;
        body.lease_number = this.leaseNumber;

        this.loader = true;

        this.paymentService.create(body)
            .subscribe((data) => {
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! New payment created.');
                },
                (error) => {
                    this.loader = false;
                    if (error.error && error.error.status_code === 404) {
                        this.notification.showNotification('danger', error.error.message);
                        return;
                    }
                    if (error.payment === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
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

    /**
     *
     */
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.dialogRef.close(this.form.value);
    }

    ngOnDestroy() {
        this._onDestroy.next();
        this._onDestroy.complete();
    }

}

