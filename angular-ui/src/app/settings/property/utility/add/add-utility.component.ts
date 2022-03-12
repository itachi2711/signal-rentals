import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject } from 'rxjs';
import { NotificationService } from '../../../../shared/notification.service';
import { UtilityModel } from '../model/utility-model';
import { UtilityService } from '../data/utility.service';

@Component({
    selector: 'robi-add-utility',
    styles: [],
    templateUrl: './add-utility.component.html'
})
export class AddUtilityComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    isAdd: boolean;
    utility: UtilityModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private utilityService: UtilityService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddUtilityComponent>) {
        this.isAdd = row.isAdd;
        this.utility = row.utility;
    }

    ngOnInit() {

        if (this.isAdd) {
            this.form = this.fb.group({
                utility_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                utility_display_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                utility_description: ['']
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                utility_name: [this.utility.utility_name, [Validators.required,
                    Validators.minLength(3)]],
                utility_display_name: [this.utility.utility_display_name],
                utility_description: [this.utility.utility_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.utility, this.form.value);

        this.loader = true;

        this.utilityService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Utility created.');
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
        const body = Object.assign({}, this.utility, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.utilityService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! Utility has been updated.');

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

