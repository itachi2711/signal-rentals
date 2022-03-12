import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject } from 'rxjs';
import { NotificationService } from '../../../../shared/notification.service';
import { TenantTypeModel } from '../model/tenant-type-model';
import { TenantTypeService } from '../data/tenant-type.service';

@Component({
    selector: 'robi-add-tenant-ype',
    styles: [],
    templateUrl: './add-tenant-type.component.html'
})
export class AddTenantTypeComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    tenantType: TenantTypeModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private tenantTypeService: TenantTypeService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddTenantTypeComponent>) {
        this.isAdd = row.isAdd;
        this.tenantType = row.tenantType;
    }

    ngOnInit() {

        if (this.isAdd) {
            this.form = this.fb.group({
                tenant_type_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                tenant_type_display_name: [''],
                tenant_type_description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                tenant_type_name: [this.tenantType?.tenant_type_name, [Validators.required,
                    Validators.minLength(3)]],
                tenant_type_display_name: [this.tenantType?.tenant_type_display_name],
                tenant_type_description: [this.tenantType?.tenant_type_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.tenantType, this.form.value);

        this.loader = true;

        this.tenantTypeService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! TenantType created.');
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
        const body = Object.assign({}, this.tenantType, this.form.value);

        this.loader = true;
        this.errorInForm.next(false);

        this.tenantTypeService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! TenantType has been updated.');

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

