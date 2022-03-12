import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../../../shared/notification.service';
import { BehaviorSubject } from 'rxjs';
import { PropertyTypeModel } from '../model/property-type-model';
import { PropertyTypeService } from '../data/property-type.service';

@Component({
    selector: 'robi-add-property-type',
    styles: [],
    templateUrl: './add-property-type.component.html'
})
export class AddPropertyTypeComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    propertyType: PropertyTypeModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private propertyTypeService: PropertyTypeService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddPropertyTypeComponent>) {
        this.isAdd = row.isAdd;
        this.propertyType = row.propertyType;
    }

    ngOnInit() {
        if (this.isAdd) {
            this.form = this.fb.group({
                name: ['', [Validators.required,
                    Validators.minLength(2)]],
                display_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                name: [this.propertyType?.name, [Validators.required,
                    Validators.minLength(3)]],
                display_name: [this.propertyType?.display_name],
                description: [this.propertyType?.description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.propertyType, this.form.value);

        this.loader = true;

        this.propertyTypeService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! PropertyType created.');
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
        const body = Object.assign({}, this.propertyType, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.propertyTypeService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! PropertyType has been updated.');

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
