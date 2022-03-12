import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { PropertyService } from '../../../properties/data/property.service';
import { PropertyDataSource } from '../../../properties/data/property-data.source';
import { LandlordService } from '../../data/landlord.service';
import { PropertyModel } from '../../../properties/models/property-model';

@Component({
    selector: 'robi-landlord-property',
    templateUrl: './landlord-property.component.html',
    styleUrls: ['./landlord-property.component.css']
})
export class LandlordPropertyComponent implements OnInit, AfterViewInit {
    propertyColumns = [
        'property_code',
        'property_name',
        'location',
        'total_units',
        // @itachi
        'plot_area',
        'covered_area,',
        'dm_circle_rate,',
        // @itachi
    ];

    // Data for the list table display
    propertyDataSource: PropertyDataSource;

    // pagination
    @ViewChild(MatPaginator, { static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, { static: true }) sort: MatSort;
    // Search field
    @ViewChild('search', { static: true }) search: ElementRef;

    loader = false;

    landlordData: any;
    landlordID: string;

    constructor(private notification: NotificationService, private propertyService: PropertyService,
        private landlordService: LandlordService) { }

    ngOnInit() {
        this.landlordService.selectedLandlordChanges$.subscribe(data => {
            if (data) {
                this.landlordData = data;
                this.landlordID = data.id;
            }
        });

        this.propertyDataSource = new PropertyDataSource(this.propertyService);
        this.propertyDataSource.meta$.subscribe((res) => this.meta = res);
        this.propertyDataSource.loadNested(
            this.landlordService.nestedPropertiesUrl(this.landlordID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.propertyDataSource.loadNested(
            this.landlordService.nestedPropertiesUrl(this.landlordID),
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
            tap(() => this.loadData())
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

    /**
     * @param property
     */
    onPropertySelected(property: PropertyModel): void {
        this.propertyService.changeSelectedProperty(property);
    }
}
