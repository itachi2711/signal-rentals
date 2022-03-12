import { AfterViewInit, Component, ElementRef, Input, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { LeaseDataSource } from '../../../leases/data/lease-data.source';
import { LeaseService } from '../../../leases/data/lease.service';
import { TenantService } from '../../data/tenant.service';
import { LeaseModel } from '../../../leases/models/lease-model';

@Component({
    selector: 'robi-tenant-lease',
    templateUrl: './tenant-lease.component.html',
    styleUrls: ['./tenant-lease.component.css']
})
export class TenantLeaseComponent implements OnInit, AfterViewInit {
    leaseColumns = [
        'lease_number',
        'unit_names',
        'rent_amount',
        'lease_type_id',
        'due_on',
        'billing_frequency',
        'billed_on',
        'status'
    ];

    // Data for the list table display
    leaseDataSource: LeaseDataSource;

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
                private leaseService: LeaseService,
                private tenantService: TenantService) {}

    ngOnInit() {

        this.tenantService.selectedTenantChanges$.subscribe(data => {
            if (data) {
                this.tenantData = data;
                this.tenantID = data.id;
            }
        });

        this.leaseDataSource = new LeaseDataSource(this.leaseService);
        this.leaseDataSource.meta$.subscribe((res) => this.meta = res);
        this.leaseDataSource.loadNested(
            this.tenantService.nestedLeasesUrl(this.tenantID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.leaseDataSource.loadNested(
            this.tenantService.nestedLeasesUrl(this.tenantID),
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

    onLeaseSelected(lease: LeaseModel): void {
        this.leaseService.changeSelectedLease(lease);
    }
}
