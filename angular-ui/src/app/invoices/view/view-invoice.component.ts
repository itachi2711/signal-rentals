import { AfterViewInit, Component, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Observable, of } from 'rxjs';
import { InvoiceModel } from '../models/invoice-model';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { AddInvoiceComponent } from '../add/add-invoice.component';
import { InvoiceService } from '../data/invoice.service';
import { DomSanitizer } from '@angular/platform-browser';
import { select, Store } from '@ngrx/store';
import { selectorIsLandlord, selectorUserID } from '../../authentication/authentication.selectors';
import { AppState } from '../../reducers';
import { LandlordService } from '../../landlords/data/landlord.service';
import { WaiveInvoiceComponent } from './waive/waive-invoice.component';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-view-invoice',
    styleUrls: ['./view-invoice.component.scss'],
    templateUrl: './view-invoice.component.html'
})
export class ViewInvoiceComponent implements OnInit, AfterViewInit  {

    form: FormGroup;
    formErrors: any;
    loader = false;
    invoiceID: string;
    invoiceNumber: string;
    invoiceData$: Observable<InvoiceModel>;
    pdfSrc: any;
    domSanitizer: DomSanitizer;
    isLandlord = false;
    landlordID: string;
    isAdmin$: Observable<boolean>;
    constructor(private store: Store<AppState>,
                private landlordService: LandlordService,
                private fb: FormBuilder,
                sanitizer: DomSanitizer,
                private dialog: MatDialog,
                private invoiceService: InvoiceService,
                private authenticationService: AuthenticationService,
                private notification: NotificationService,
                private router: Router, private route: ActivatedRoute) {
        this.domSanitizer = sanitizer;
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => {
            if (isLandlord) {
                this.isLandlord = true;
                this.store.pipe(select(selectorUserID)).subscribe(userID => this.landlordID = userID);
            }
        });
    }

    ngOnInit() {
        this.invoiceID = this.route.snapshot.paramMap.get('id');
        this.invoiceData$ = this.invoiceService.selectedInvoiceChanges$;

        this.invoiceService.selectedInvoiceChanges$.subscribe(data => {
            if (data) {
                this.invoiceData$ = of(data);
            }
            if (!data) {
                if (this.isLandlord) {
                    this.landlordService.getNestedById(this.landlordService.nestedInvoiceUrl(this.landlordID, this.invoiceID))
                        .subscribe(invoice => {
                            this.invoiceData$ = of(invoice);
                            this.invoiceService.changeSelectedInvoice(invoice);
                        });
                } else {
                    this.invoiceService.getById(this.invoiceID).subscribe(invoice => {
                        this.invoiceData$ = of(invoice);
                        this.invoiceService.changeSelectedInvoice(invoice);
                    });
                }
            }
        });
        this.downloadInvoice(this.invoiceID)
    }

    /**
     * @param invoiceId
     */
    downloadInvoice(invoiceId: string) {
        this.loader = true;

        this.invoiceService.downloadInvoice({id: invoiceId, pdf: true})
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

    /**
     *
     * @param blob
     */
    showFile(blob) {
        const newBlob = new Blob([blob], {type: 'application/pdf'});

        if (window.navigator && window.navigator.msSaveOrOpenBlob) {
            window.navigator.msSaveOrOpenBlob(newBlob);
            return;
        }
        const data = window.URL.createObjectURL(newBlob);
        const link = document.createElement('a');
        link.href = data;
        link.download = 'statement.pdf';
        link.click();
        setTimeout(function() {
            window.URL.revokeObjectURL(data);
        }, 100);
    }

    /**
     * Add dialog launch
     */
    addDialog(mode: string, property?: InvoiceModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {property,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddInvoiceComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

    ngAfterViewInit(): void {}

    /**
     * Add dialog launch
     */
    waiveInvoice(invoice: InvoiceModel) {
        this.invoiceID = invoice?.id;
        this.invoiceNumber = invoice?.invoice_number;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.width = 'auto';
        dialogConfig.height = 'auto';
        dialogConfig.data = {
           invoice: invoice
        };

        const dialogRef = this.dialog.open(WaiveInvoiceComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    // this.loadData();
                }
            }
        );
    }

}
