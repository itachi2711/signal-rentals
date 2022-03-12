import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject } from 'rxjs';
import { NotificationService } from '../../../../shared/notification.service';
import { UnitTypeModel } from '../model/unit-type-model';
import { AmenityService } from '../../amenity/data/amenity.service';
import { UnitTypeService } from '../data/unit-type.service';

@Component({
    selector: 'robi-add-unit-type',
    styles: [],
    templateUrl: './add-unit-type.component.html'
})
export class AddUnitTypeComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    unitType: UnitTypeModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private unitTypeService: UnitTypeService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddUnitTypeComponent>) {
        this.isAdd = row.isAdd;
        this.unitType = row.unitType;
    }

    ngOnInit() {

        if (this.isAdd) {
            this.form = this.fb.group({
                unit_type_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                unit_type_display_name: [''],
                unit_type_description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                unit_type_name: [this.unitType?.unit_type_name, [Validators.required,
                    Validators.minLength(3)]],
                unit_type_display_name: [this.unitType?.unit_type_display_name],
                unit_type_description: [this.unitType?.unit_type_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.unitType, this.form.value);

        this.loader = true;

        this.unitTypeService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! UnitType created.');
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
        const body = Object.assign({}, this.unitType, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.unitTypeService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! UnitType has been updated.');

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

