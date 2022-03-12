import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../../../shared/notification.service';
import { BehaviorSubject } from 'rxjs';
import { PaymentMethodModel } from '../model/payment-method-model';
import { PaymentMethodService } from '../data/payment-method.service';

@Component({
    selector: 'robi-add-payment-method',
    styles: [],
    templateUrl: './add-payment-method.component.html'
})

export class AddPaymentMethodComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    paymentMethod: PaymentMethodModel;

    loader = false;

    roles: any = [];
    employees: any = [];
    branches: any = [];
    isAdd: boolean;


    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private paymentMethodService: PaymentMethodService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddPaymentMethodComponent>) {
        this.roles = row.roles;
        this.employees = row.employees;
        this.branches = row.branches;
        this.isAdd = row.isAdd;
        this.paymentMethod = row.paymentMethod;
    }

    ngOnInit() {
        if (this.isAdd) {
            this.form = this.fb.group({
                payment_method_name: ['', [Validators.required,
                    Validators.minLength(3)]],
                payment_method_display_name: [''],
                payment_method_description: ['', [Validators.required,
                    Validators.minLength(3)]],
            });
        }
        if (!this.isAdd) {
            this.form = this.fb.group({
                payment_method_name: [this.paymentMethod?.payment_method_name, [Validators.required,
                    Validators.minLength(2)]],
                payment_method_display_name: [this.paymentMethod?.payment_method_display_name],
                payment_method_description: [this.paymentMethod?.payment_method_description]
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.paymentMethod, this.form.value);

        this.loader = true;

        this.paymentMethodService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Payment Method created.');
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
        const body = Object.assign({}, this.paymentMethod, this.form.value);

        this.loader = true;
        this.errorInForm.next(false);

        this.paymentMethodService.update(body).subscribe((data) => {
                this.loader = false;
                this.dialogRef.close(this.form.value);
                // notify success
                this.notification.showNotification('success', 'Success !! Payment Method has been updated.');
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

    save() {
        this.dialogRef.close(this.form.value);
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
