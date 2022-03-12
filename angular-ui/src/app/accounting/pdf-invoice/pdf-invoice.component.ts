import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { AccountingService } from '../data/accounting.service';
import { DomSanitizer } from '@angular/platform-browser';
import { InvoiceService } from '../../invoices/data/invoice.service';

@Component({
    selector: 'robi-pdf-invoice',
    styleUrls: ['./pdf-invoice.component.css'],
    templateUrl: './pdf-invoice.component.html'
})
export class PdfInvoiceComponent implements OnInit  {
    id: string;
    isLease: boolean;
    loader = false;
    pdfSrc: any;
    domSanitizer: DomSanitizer;
    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                sanitizer: DomSanitizer,
                private accountingService: AccountingService,
                private notification: NotificationService,
                private invoiceService: InvoiceService,
                private dialogRef: MatDialogRef<PdfInvoiceComponent>) {
        this.domSanitizer = sanitizer;
        this.id = row.id;
        this.isLease = row.isLease;
    }

    ngOnInit() {
        this.loader = true;
        this.downloadInvoice();
    }

    downloadInvoice() {
        this.loader = true;
        this.invoiceService.downloadInvoice({id: this.id, pdf: true})
            .subscribe((res) => {
                    this.pdfSrc = this.domSanitizer.bypassSecurityTrustResourceUrl(
                        URL.createObjectURL(res)
                    );
                    this.loader = false;
                },
                () => {
                    this.loader = false;
                    this.notification.showNotification('danger', 'Error downloading Invoice !');
                }
            );
    }

    close() {
        this.dialogRef.close();
    }
}
