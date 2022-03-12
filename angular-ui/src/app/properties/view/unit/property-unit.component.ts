import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { LeaseDataSource } from '../../../leases/data/lease-data.source';
import { LeaseService } from '../../../leases/data/lease.service';
import { PropertyService } from '../../data/property.service';
import { TenantModel } from '../../../tenants/models/tenant-model';
import { TenantService } from '../../../tenants/data/tenant.service';
import { LeaseModel } from '../../../leases/models/lease-model';
import { PropertyDataSource } from '../../data/property-data.source';

@Component({
    selector: 'robi-property-unit',
    templateUrl: './property-unit.component.html',
    styleUrls: ['./property-unit.component.css']
})
export class PropertyUnitComponent implements OnInit, AfterViewInit {
    unitColumns = [
        'unit_name',
        'unit_type_id',
        'unit_mode',
        'bed_rooms',
        'square_foot',
        'leases'
    ];

    // Data for the list table display
    unitDataSource: PropertyDataSource;

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

    propertyData: any;
    propertyID: string;

    constructor(private notification: NotificationService,
                private leaseService: LeaseService,
                private tenantService: TenantService,
                private propertyService: PropertyService) {}

    ngOnInit() {
        this.propertyService.selectedPropertyChanges$.subscribe(data => {
            if (data) {
                this.propertyData = data;
                this.propertyID = data.id;
            }
        });

        this.unitDataSource = new PropertyDataSource(this.propertyService);
        this.unitDataSource.meta$.subscribe((res) => this.meta = res);
        this.unitDataSource.loadNested(
            this.propertyService.nestedUnitsUrl(this.propertyID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.unitDataSource.loadNested(
            this.propertyService.nestedUnitsUrl(this.propertyID),
            '',
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

    onSelectedTenant(tenant: TenantModel): void {
        this.tenantService.changeSelectedTenant(tenant);
    }

    onLeaseSelected(lease: LeaseModel): void {
        this.leaseService.changeSelectedLease(lease);
    }
}
