import { Component, OnInit, ViewChild } from '@angular/core';
import { MatStepper } from '@angular/material/stepper';
import { FormBuilder, FormGroup } from '@angular/forms';
import { TenantModel } from '../models/tenant-model';
import { TenantService } from '../data/tenant.service';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { TenantTypeService } from '../../settings/tenant/tenant-type/data/tenant-type.service';
import { ActivatedRoute, Router } from '@angular/router';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { AuthenticationService } from '../../authentication/authentication.service';
import { LATE_FEE_TYPES } from '../../shared/enums/late-fee-types.enum';
import { GENDER } from '../../shared/enums/gender-enum';

@Component({
    selector: 'robi-add-tenant',
    styles: [],
    templateUrl: './add-tenant.component.html'
})
export class AddTenantComponent implements OnInit  {

    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;
    isLinear = false;

    personalFormGroup: FormGroup;
    employmentFormGroup: FormGroup;
    nextOfKinFormGroup: FormGroup;

    tenantTypes$: Observable<any>;
    gender: any;

    isAdd = true;
    tenantID: string;
    tenant: TenantModel;
    deleteDialogRef: MatDialogRef<ConfirmationDialogComponent>;
    isAdmin$: Observable<boolean>;
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private _formBuilder: FormBuilder,
                private router: Router,
                private route: ActivatedRoute,
                private tenantService: TenantService,
                private tenantTypeService: TenantTypeService,
                private authenticationService: AuthenticationService,
                private notification: NotificationService) {
        this.gender = GENDER;
            this.isAdmin$ = this.authenticationService.isAdmin();
        this.personalFormGroup = this._formBuilder.group({
            tenant_type_id: [''],
            first_name: [''],
            middle_name: [''],
            last_name: [''],
            gender: [''],
            date_of_birth: [''],
            id_passport_number: [''],
            marital_status: [''],

            phone: [''],
            email: [''],
            country: [''],
            city: [''],
            postal_code: [''],
            postal_address: [''],
            physical_address: [''],
            password: [''],
            password_confirmation: ['']
        });

        this.nextOfKinFormGroup = this._formBuilder.group({
            next_of_kin_name: [''],
            next_of_kin_phone: [''],
            next_of_kin_relation: [''],

            emergency_contact_name: [''],
            emergency_contact_phone: [''],
            emergency_contact_email: [''],
            emergency_contact_relationship: [''],
            emergency_contact_postal_address: [''],
            emergency_contact_physical_address: ['']
        });

        this.employmentFormGroup = this._formBuilder.group({
            employment_status: [''],
            employment_position: [''],
            employer_contact_phone: [''],
            employer_contact_email: [''],
            employment_postal_address: [''],
            employment_physical_address: ['']
        });
    }

    ngOnInit() {
        this.tenantID = this.route.snapshot.paramMap.get('id');
        if (this.tenantID) {
            this.isAdd = false;
            this.tenantService.selectedTenantChanges$.subscribe(tenant => {
                if (tenant) {
                    this.tenant = tenant;
                    this.populateForm(tenant);
                }
                if (!tenant) {
                    this.tenantService.getById(this.tenantID).subscribe(data => {
                        this.tenant = data;
                        this.tenantService.changeSelectedTenant(data);
                        this.populateForm(data);
                    });
                }
            });
        }
        this.tenantTypes$ = this.tenantTypeService.list([
            'tenant_type_name ',
            'tenant_type_display_name'
        ]);
    }

    populateForm(tenant: TenantModel) {
        this.populatePersonalData(tenant);
        this.populateNextOfKinData(tenant);
        this.populateEmploymentData(tenant);
    }

    populatePersonalData(tenant) {
        this.personalFormGroup.patchValue({
            tenant_type_id: tenant?.tenant_type_id,
            first_name: tenant?.first_name,
            middle_name: tenant?.middle_name,
            last_name: tenant?.last_name,
            gender: tenant?.gender,
            date_of_birth: tenant?.date_of_birth,
            id_passport_number: tenant?.id_passport_number,
            marital_status: tenant?.marital_status,

            phone: tenant?.phone,
            email: tenant?.email,
            country: tenant?.country,
            city: tenant?.city,
            postal_code: tenant?.postal_code,
            postal_address: tenant?.postal_address,
            physical_address: tenant?.physical_address,
        });
    }

    populateNextOfKinData(tenant) {
        this.nextOfKinFormGroup.patchValue({
            next_of_kin_name: tenant?.next_of_kin_name,
            next_of_kin_phone: tenant?.next_of_kin_phone,
            next_of_kin_relation: tenant?.next_of_kin_relation,
            emergency_contact_name: tenant?.emergency_contact_name,
            emergency_contact_phone: tenant?.emergency_contact_phone,
            emergency_contact_email: tenant?.emergency_contact_email,
            emergency_contact_relationship: tenant?.emergency_contact_relationship,
            emergency_contact_postal_address: tenant?.emergency_contact_postal_address,
            emergency_contact_physical_address: tenant?.emergency_contact_physical_address,
        });
    }

    populateEmploymentData(tenant) {
        this.employmentFormGroup.patchValue({
            employment_status: tenant?.employment_status,
            employment_position: tenant?.employment_position,
            employer_contact_phone: tenant?.employer_contact_phone,
            employer_contact_email: tenant?.employer_contact_email,
            employment_postal_address: tenant?.employment_postal_address,
            employment_physical_address: tenant?.employment_physical_address,
        });
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);
        const tenantFields = {...this.personalFormGroup.value, ...this.nextOfKinFormGroup.value, ...this.employmentFormGroup.value};
        this.loader = true;
        this.tenantService.create(tenantFields)
            .subscribe((res) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! New Tenant created.');
                    this.onSaveComplete();
                },
                (error) => {
                    this.loader = false;
                    if (error.lead === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
                        return;
                    }
                    this.formErrors = error;

                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            this.stepper.selectedIndex = 0;

                            if (this.personalFormGroup.controls[prop]) {
                                this.personalFormGroup.controls[prop]?.markAsTouched();
                                this.personalFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.nextOfKinFormGroup.controls[prop]) {
                                this.nextOfKinFormGroup.controls[prop]?.markAsTouched();
                                this.nextOfKinFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.employmentFormGroup.controls[prop]) {
                                this.employmentFormGroup.controls[prop]?.markAsTouched();
                                this.employmentFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }

                });
    }

    update() {
        const tenantFields = {...this.personalFormGroup.value, ...this.nextOfKinFormGroup.value, ...this.employmentFormGroup.value};
     //   tenantFields.id = this.tenantID;
        const body = Object.assign({}, this.tenant, tenantFields);
        this.loader = true;
        this.errorInForm.next(false);

        this.tenantService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Tenant has been updated.');
                    this.onSaveComplete();
                },
                (error) => {
                    this.loader = false;
                    if (error.landlord === 0) {
                        return;
                    }
                    this.formErrors = error;
                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            if (this.personalFormGroup) {
                                this.personalFormGroup.controls[prop]?.markAsTouched();
                                this.personalFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }

    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    public onSaveComplete(): void {
        this.loader = false;
        this.router.navigate(['/tenants']);
    }

    openConfirmationDialog(tenant: TenantModel) {
        this.deleteDialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.deleteDialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(tenant);
            }
             this.deleteDialogRef = null;
        });
    }

    private delete(tenant: TenantModel) {
        this.loader = true;
        this.tenantService.delete(tenant)
            .subscribe((data) => {
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Tenant has been deleted.');
                },
                (error) => {
                    this.loader = false;
                    if (error.error['message']) {
                        this.notification.showNotification('danger', error.error['message']);
                    } else {
                        this.notification.showNotification('danger', 'Delete Error !! ');
                    }
                });
    }
}

