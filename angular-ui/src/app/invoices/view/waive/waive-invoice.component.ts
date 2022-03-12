import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../../shared/notification.service';
import { Router } from '@angular/router';
import { BehaviorSubject } from 'rxjs';
import { WaiverModel } from '../../models/waiver-model';
import { WaiverService } from '../../data/waiver.service';
import { InvoiceModel } from '../../models/invoice-model';

@Component({
    selector: 'robi-waive-invoice',
    styles: [],
    templateUrl: './waive-invoice.component.html'
})
export class WaiveInvoiceComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    private terminationErrorMessage = new BehaviorSubject<string>('');
    terminationError$ = this.terminationErrorMessage.asObservable();

    waiver: WaiverModel;

    loader = false;
    invoiceNumber: string;
    invoice: InvoiceModel;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private waiverService: WaiverService,
                private notification: NotificationService,
                private router: Router,
                private dialogRef: MatDialogRef<WaiveInvoiceComponent>) {
        this.invoice = row.invoice;
        this.invoiceNumber = row?.invoice?.invoice_number;
    }

    ngOnInit() {
        this.form = this.fb.group({
            waiver_date: [(new Date()).toISOString().substring(0, 10), [Validators.required]],
            amount: ['', [Validators.required]],
            waiver_note: ['', [Validators.required]],
        });
    }

    save() {
        this.dialogRef.close(this.form.value);
    }

    close() {
        this.dialogRef.close();
    }

    /**
     * Create a resource
     */
    create() {
        this.errorInForm.next(false);
        this.loader = true;

        const body = Object.assign({}, this.waiver, this.form.value);
        body.invoice_id = this.invoice?.id;
        body.lease_id = this.invoice?.lease_id;
        body.property_id = this.invoice?.property_id;
        body.lease_number = this.invoice?.lease?.lease_number;

        this.waiverService.create(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Waiver was a success.');
                },
                (error) => {
                    this.errorInForm.next(true);
                    this.loader = false;
                    this.terminationErrorMessage.next(error.error.message);

                    if (error.role === 0) {
                        this.notification.showNotification('danger', 'Connection Error !!' +
                            ' Check your connection and retry.');
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.form && this.form.controls[prop]) {
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
        this.form.reset();
        this.dialogRef.close(this.form.value);
        this.router.navigate(['/invoices']);
    }

}
