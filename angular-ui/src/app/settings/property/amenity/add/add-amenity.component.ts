import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../../../shared/notification.service';
import { BehaviorSubject } from 'rxjs';
import { AmenityService } from '../data/amenity.service';
import { AmenityModel } from '../model/amenity-model';

@Component({
    selector: 'robi-add-amenity',
    styles: [],
    templateUrl: './add-amenity.component.html'
})
export class AddAmenityComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    amenity: AmenityModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private amenityService: AmenityService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddAmenityComponent>) {
        this.isAdd = row.isAdd;
        this.amenity = row.amenity;
    }

    ngOnInit() {

        if (this.isAdd) {
            this.form = this.fb.group({
                amenity_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                amenity_display_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                amenity_description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
               amenity_name: [this.amenity?.amenity_name, [Validators.required,
                    Validators.minLength(3)]],
                amenity_display_name: [this.amenity?.amenity_display_name],
                amenity_description: [this.amenity?.amenity_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.amenity, this.form.value);

        this.loader = true;

        this.amenityService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Amenity created.');
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
        const body = Object.assign({}, this.amenity, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.amenityService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! Amenity has been updated.');

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
