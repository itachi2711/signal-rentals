import { Component, Inject, OnInit, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject } from 'rxjs';
import { NotificationService } from '../../../../shared/notification.service';
import { LeaseTypeModel } from '../model/lease-type-model';
import { LeaseTypeService } from '../data/lease-type.service';
import { AmenityModel } from '../../../property/amenity/model/amenity-model';
import { AmenityService } from '../../../property/amenity/data/amenity.service';

@Component({
    selector: 'robi-add-lease-type',
    styles: [],
    templateUrl: './add-lease-type.component.html'
})
export class AddLeaseTypeComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    leaseType: LeaseTypeModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private leaseTypeService: LeaseTypeService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddLeaseTypeComponent>) {
        this.isAdd = row.isAdd;
        this.leaseType = row.leaseType;
    }

    ngOnInit() {
        if (this.isAdd) {
            this.form = this.fb.group({
                lease_type_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                lease_type_display_name: [''],
                lease_type_description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                lease_type_name: [this.leaseType?.lease_type_name, [Validators.required,
                    Validators.minLength(3)]],
                lease_type_display_name: [this.leaseType?.lease_type_display_name],
                lease_type_description: [this.leaseType?.lease_type_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.leaseType, this.form.value);

        this.loader = true;

        this.leaseTypeService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! LeaseType created.');
            },
            (error) => {
                this.loader = false;
                this.errorInForm.next(true);
                this.formErrors = error;
                if (this.formErrors) {
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
                        }
                    }
                }
            });
    }

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.leaseType, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.leaseTypeService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! LeaseType has been updated.');

            },
            (error) => {
                this.loader = false;
                this.errorInForm.next(true);
                this.formErrors = error;
                if (this.formErrors) {
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
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
}

