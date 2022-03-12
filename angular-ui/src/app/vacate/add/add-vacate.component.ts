import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { VacateModel } from '../models/vacate-model';
import { VacateService } from '../data/vacate.service';

import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { TenantService } from '../../tenants/data/tenant.service';
import { LeaseModel } from '../../leases/models/lease-model';
import { LeaseService } from '../../leases/data/lease.service';
import { TenantModel } from '../../tenants/models/tenant-model';
import { Router } from '@angular/router';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { AuthenticationService } from '../../authentication/authentication.service';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { selectorUserID } from '../../authentication/authentication.selectors';

@Component({
    selector: 'robi-add-vacate',
    styles: [],
    templateUrl: './add-vacate.component.html'
})
export class AddVacateComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    member: VacateModel;

    loader = false;

    groups: any = [];

    formGroup: FormGroup;

    memberStatuses: any = [];

    profilePicFileToUpload: File = null;
    profilePicUrl = '';
   vacate: VacateModel;

    isAdd: boolean;

    lease$ = of();
    leases$: Observable<any>;

    tenant$ = of();
    tenantID: string;
    leaseID: string;
    propertyID: string;
    leaseNumber: string;
    tenants: any = [];
    tenantActiveLeases$: Observable<any>;

    /** control for filter for server side. */
    public tenantServerSideFilteringCtrl: FormControl = new FormControl();

    /** indicate search operation is in progress */
    public searching = false;

    /** list of banks filtered after simulating server side search */
    public  filteredServerSideTenants: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    tenantsFiltered$: Observable<any>;
    deleteDialogRef: MatDialogRef<ConfirmationDialogComponent>;
    isAdmin$: Observable<boolean>;
    isTenant: boolean;
    userID: string;
    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private vacateService: VacateService,
                private tenantService: TenantService,
                private leaseService: LeaseService,
                private notification: NotificationService,
                private router: Router,
                private dialog: MatDialog,
                private store: Store<AppState>,
                private authenticationService: AuthenticationService,
                private dialogRef: MatDialogRef<AddVacateComponent>) {
        this.isAdd = row?.isAdd;
        this.store.pipe(select(selectorUserID)).subscribe(id => this.userID = id);
        this.vacate = row?.vacateNotice;
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.authenticationService.isTenant().subscribe(res => this.isTenant = res);
    }

    ngOnInit() {
        if (this.isTenant) {
            this.tenantID = this.userID;
            this.tenantService.getNested(this.tenantService.nestedLeasesUrl(this.tenantID),
                '', 0, 0).subscribe(res => {
                this.leases$ = of(res['data']);
            });
        }
        if (!this.isTenant && !this.isAdd) {
            this.tenantID = this.vacate?.tenant_id;
            this.tenantService.getNested(this.tenantService.nestedLeasesUrl(this.tenantID),
                '', 0, 0).subscribe(res => {
                this.leases$ = of(res['data']);
            });
        }

        this.tenantServerSideFilteringCtrl.valueChanges
            .pipe(
                filter(search => !!search),
                tap(() => this.searching = true),
                takeUntil(this._onDestroy),
                debounceTime(2000),
                distinctUntilChanged(),
                map(search => {
                    search = search.toLowerCase();
                    this.tenantsFiltered$ = this.tenantService.search(search);
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

        if (this.isAdd) {
            this.form = this.fb.group({
                tenant: [''],
                lease_id: [''],
                vacating_date: ['', [Validators.required,
                    Validators.minLength(2)]],
                vacating_reason: ['']
            });
        }

        if (!this.isAdd) {
            this.tenantsFiltered$ = of([this.vacate?.tenant]);
            this.form = this.fb.group({
                tenant: [this.vacate?.tenant, [Validators.required,
                    Validators.minLength(3)]],
                lease_id: [this.vacate?.lease_id],
                vacating_date: [this.vacate?.vacating_date],
                vacating_reason: [this.vacate?.vacating_reason]
            });
        }
    }

    /**
     * @param tenant
     */
    onTenantItemChange(tenant: TenantModel) {
        this.tenantID = tenant?.id;
        this.tenantActiveLeases$ = of();
        this.lease$ = of();
        this.tenant$ = of();
        this.tenantsFiltered$.subscribe(tenants => {
           this.leases$ =  of(tenants.find((item: any) => item.id === tenant?.id).leases);
           this.tenant$ = of(tenants.find((item: any) => item.id === tenant?.id));
        });
    }

    /**
     * @param leaseID
     */
    onLeaseItemChange(leaseID: string) {
        this.leases$.subscribe(leases => {
            const selectedLease =  leases.find((item: any) => item.id === leaseID);
            this.leaseID = selectedLease?.id;
            this.propertyID = selectedLease?.property_id;
            this.leaseNumber = selectedLease?.lease_number;
        });
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.vacate, this.form.value);
        body.tenant_id = this.tenantID;
        body.property_id = this.propertyID;
        body.lease_id = this.leaseID;
        body.lease_number = this.leaseNumber;

        this.loader = true;

        this.vacateService.create(body)
            .subscribe((data) => {
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! New Vacate Notice created.');
                },
                (error) => {
                    this.loader = false;
                    if (error.member === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
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

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.vacate, this.form.value);
        body.tenant_id = this.tenantID;
        body.property_id = this.propertyID;
        body.lease_id = this.leaseID;
        body.lease_number = this.leaseNumber;

        this.loader = true;
        this.errorInForm.next(false);

        this.vacateService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.dialogRef.close(this.form.value);
                    this.router.navigate(['/notices']);
                    this.notification.showNotification('success', 'Success !! Vacate Notice has been updated.');
                },
                (error) => {
                    this.loader = false;

                    if (error.member === 0) {
                        // notify error
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

     /**
     * Create or Update Data
     */
    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    close() {
        this.dialogRef.close();
    }

    /**
     *
     */
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.dialogRef.close(this.form.value);
    }

    deleting() {
        this.dialogRef.close('deleting')
    }
}

