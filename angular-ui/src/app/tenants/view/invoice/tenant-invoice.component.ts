import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { InvoiceModel } from '../../../invoices/models/invoice-model';
import { InvoiceService } from '../../../invoices/data/invoice.service';
import { InvoiceDataSource } from '../../../invoices/data/invoice-data.source';
import { TenantService } from '../../data/tenant.service';


@Component({
    selector: 'robi-tenant-invoice',
    templateUrl: './tenant-invoice.component.html',
    styleUrls: ['./tenant-invoice.component.css']
})
export class TenantInvoiceComponent implements OnInit, AfterViewInit {
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

    tenantData: any;
    tenantID: string;

    constructor(private notification: NotificationService,
                private tenantService: TenantService,
                private invoiceService: InvoiceService) {}

    ngOnInit() {
        this.tenantService.selectedTenantChanges$.subscribe(data => {
            if (data) {
                this.tenantData = data;
                this.tenantID = data.id;
            }
        });

        this.invoiceDataSource = new InvoiceDataSource(this.invoiceService);
        this.invoiceDataSource.meta$.subscribe((res) => this.meta = res);
        this.invoiceDataSource.loadNested(
            this.tenantService.nestedInvoicesUrl(this.tenantID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.invoiceDataSource.loadNested(
            this.tenantService.nestedInvoicesUrl(this.tenantID),
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
}
