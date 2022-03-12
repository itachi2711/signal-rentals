import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge, Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { InvoiceDataSource } from '../../../invoices/data/invoice-data.source';
import { InvoiceService } from '../../../invoices/data/invoice.service';
import { LeaseService } from '../../data/lease.service';
import { InvoiceModel } from '../../../invoices/models/invoice-model';
import { AuthenticationService } from '../../../authentication/authentication.service';
import { MatDialog, MatDialogConfig } from '@angular/material/dialog';
import { PdfStatementComponent } from '../../../accounting/pdf-statement/pdf-statement.component';
import { PdfInvoiceComponent } from '../../../accounting/pdf-invoice/pdf-invoice.component';

@Component({
    selector: 'robi-lease-invoice',
    templateUrl: './lease-invoice.component.html',
    styleUrls: ['./lease-invoice.component.css']
})
export class LeaseInvoiceComponent implements OnInit, AfterViewInit {
    invoiceColumns = [
        'invoice_number',
        'invoice_date',
        'invoice_amount',
        'amount_paid',
        'amount_due',
        'due_date',
        'status',
    ];

    // Data for the list table display
    invoiceDataSource: InvoiceDataSource;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    // Search field
    @ViewChild('search', {static: true}) search: ElementRef;

    loader = false;
    leaseData: any;
    leaseID: string;
    isAdmin$: Observable<boolean>;
    constructor(private notification: NotificationService,
                private leaseService: LeaseService,
                private authenticationService: AuthenticationService,
                private invoiceService: InvoiceService,
                private dialog: MatDialog) {
        this.isAdmin$ = this.authenticationService.isAdmin();
    }

    ngOnInit() {
        this.leaseService.selectedLeaseChanges$.subscribe(data => {
            if (data) {
                this.leaseData = data;
                this.leaseID = data.id;
            }
        });

        this.invoiceDataSource = new InvoiceDataSource(this.invoiceService);
        this.invoiceDataSource.meta$.subscribe((res) => this.meta = res);
        this.invoiceDataSource.loadNested(
            this.leaseService.nestedInvoicesUrl(this.leaseID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.invoiceDataSource.loadNested(
            this.leaseService.nestedInvoicesUrl(this.leaseID),
            this.search.nativeElement.value,
            (this.paginator.pageIndex + 1),
            (this.paginator.pageSize),
            this.sort.active,
            this.sort.direction
        );
    }

    /**
     * Handle search and pagination
     */
    ngAfterViewInit() {
        this.paginator.page.pipe(
            tap(() => this.loadData() )
        ).subscribe();

        // reset the paginator after sorting
        this.sort.sortChange.subscribe(() => this.paginator.pageIndex = 0);

        merge(this.sort.sortChange, this.paginator.page)
            .pipe(
                tap(() => this.loadData())
            )
            .subscribe();
    }

    /**
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }

    onInvoiceSelected(invoice: InvoiceModel): void {
        this.invoiceService.changeSelectedInvoice(invoice);
    }

    viewPdfInvoice(invoice: InvoiceModel) {
        const id = invoice?.id;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {
            id: id,
            isLease: true,
        };
        dialogConfig.width = '600px';
        this.dialog.open(PdfInvoiceComponent, dialogConfig);
    }
}
