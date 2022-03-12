import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { AccountingService } from '../data/accounting.service';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
    selector: 'robi-pdf-statement',
    styleUrls: ['./pdf-statement.component.css'],
    templateUrl: './pdf-statement.component.html'
})
export class PdfStatementComponent implements OnInit  {
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
                private dialogRef: MatDialogRef<PdfStatementComponent>) {
        this.domSanitizer = sanitizer;
        this.id = row.id;
        this.isLease = row.isLease;
    }

    ngOnInit() {
        this.loader = true;
        if (this.isLease) {
            this.downloadLeaseStatement();
        } else {
            this.downloadStatement();
        }
    }

   downloadStatement() {
        this.loader = true;
        this.accountingService.downloadGeneralAccountStatement({id: this.id, pdf: true})
            .subscribe((res) => {
                    this.pdfSrc = this.domSanitizer.bypassSecurityTrustResourceUrl(
                        URL.createObjectURL(res)
                    );
                    this.loader = false;
                },
                () => {
                    this.loader = false;
                    this.notification.showNotification('danger', 'Error downloading Statement !');
                }
            );
    }

    downloadLeaseStatement() {
        this.loader = true;
        this.accountingService.downloadLeaseAccountStatement({id: this.id, pdf: true})
            .subscribe((res) => {
                    this.pdfSrc = this.domSanitizer.bypassSecurityTrustResourceUrl(
                        URL.createObjectURL(res)
                    );
                    this.loader = false;
                },
                () => {
                    this.loader = false;
                    this.notification.showNotification('danger', 'Error downloading lease statement !');
                }
            );
    }

    close() {
        this.dialogRef.close();
    }
}
