import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { TenantService } from '../../data/tenant.service';
import { PaymentDataSource } from '../../../payments/data/payment-data.source';
import { PaymentService } from '../../../payments/data/payment.service';

@Component({
    selector: 'robi-tenant-payment',
    templateUrl: './tenant-payment.component.html',
    styleUrls: ['./tenant-payment.component.css']
})
export class TenantPaymentComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'amount',
        'payment_date',
        'lease_id',
        'property_id',
        'receipt_number',
        'payment_status'
    ];

    // Data for the list table display
    paymentDataSource: PaymentDataSource;

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
                private paymentService: PaymentService,
                private tenantService: TenantService) {}

    ngOnInit() {

        this.tenantService.selectedTenantChanges$.subscribe(data => {
            if (data) {
                this.tenantData = data;
                this.tenantID = data.id;
            }
        });

        this.paymentDataSource = new PaymentDataSource(this.paymentService);
        this.paymentDataSource.meta$.subscribe((res) => this.meta = res);
        this.paymentDataSource.loadNested(
            this.tenantService.nestedPaymentsUrl(this.tenantID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.paymentDataSource.loadNested(
            this.tenantService.nestedPaymentsUrl(this.tenantID),
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
}
