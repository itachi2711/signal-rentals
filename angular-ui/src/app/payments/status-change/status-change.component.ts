import { AfterViewInit, Component, ElementRef, Inject, OnInit, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { PaymentModel } from '../models/payment-model';
import { PaymentService } from '../data/payment.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { PaymentStatusModel } from '../models/payment-status.model';
import { PaymentMethodService } from '../../settings/payment/payment-method/data/payment-method.service';
import { TransactionDataSource } from '../data/transaction-data.source';
import { TransactionService } from '../data/transaction.service';

@Component({
    selector: 'robi-status-change',
    styles: [],
    templateUrl: './status-change.component.html'
})
export class StatusChangeComponent implements OnInit, AfterViewInit  {

    form: FormGroup;
    formErrors: any;

    transactionColumns = [
        'loan_id',
        'amount',
        'transaction_date',
        'transaction_type'
    ];

    // Data for the list table display
    transactionDataSource: TransactionDataSource;
    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;

    payment: PaymentModel;
    paymentStatus: PaymentStatusModel;
    loader = false;
    paymentMethods: any = [];

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private paymentService: PaymentService,
                private notification: NotificationService,
                private transactionService: TransactionService,
                private paymentMethodService: PaymentMethodService,
                private dialogRef: MatDialogRef<StatusChangeComponent>) {

        this.payment = row.data;
    }

    ngOnInit() {
        this.form = this.fb.group({
            cancel_notes: ['', [Validators.required,
                Validators.minLength(2)]]
        });

        this.paymentMethodService.list('name')
            .subscribe((res) => this.paymentMethods = res,
                () => this.paymentMethods = []
            );
    }

    close() {
        this.dialogRef.close();
    }

    /**
     * Handle search and pagination
     */
    ngAfterViewInit() {
    }

    /**
     *
     */
    cancelPayment() {

        const body = Object.assign({}, this.paymentStatus, this.form.value);
        body.id = this.payment.id;

        this.loader = true;

        this.paymentService.cancel(body)
            .subscribe((data) => {
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Payment has been Cancelled.');
                },
                (error) => {
                    this.loader = false;
                    // User has no loan
                    if (error.error && error.error.status_code === 404) {
                        this.notification.showNotification('danger', error.error.message);
                        return;
                    }
                    if (error.payment === 0) {
                        this.notification.showNotification('danger', 'Connection Error !!.' +
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
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.dialogRef.close(this.form.value);
    }

}
