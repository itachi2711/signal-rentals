import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { PropertyService } from '../../data/property.service';
import { VacateService } from '../../../vacate/data/vacate.service';
import { VacateDataSource } from '../../../vacate/data/vacate-data.source';
import { InvoiceModel } from '../../../invoices/models/invoice-model';
import { VacateModel } from '../../../vacate/models/vacate-model';

@Component({
    selector: 'robi-property-notice',
    templateUrl: './property-notice.component.html',
    styleUrls: ['./property-notice.component.css']
})
export class PropertyNoticeComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'vacating_date',
        'tenant_id',
        'lease',
        'unit'
    ];

    // Data for the list table display
    vacateDataSource: VacateDataSource;

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
                private propertyService: PropertyService,
                private vacateService: VacateService) {}

    ngOnInit() {
        this.propertyService.selectedPropertyChanges$.subscribe(data => {
            if (data) {
                this.propertyData = data;
                this.propertyID = data.id;
            }
        });

        this.vacateDataSource = new VacateDataSource(this.vacateService);
        this.vacateDataSource.meta$.subscribe((res) => this.meta = res);
        this.vacateDataSource.loadNested(
            this.propertyService.nestedNoticesUrl(this.propertyID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.vacateDataSource.loadNested(
            this.propertyService.nestedNoticesUrl(this.propertyID),
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

    onSelectedNotice(notice: VacateModel): void {
        this.vacateService.changeSelectedVacateNotice(notice);
    }
}
