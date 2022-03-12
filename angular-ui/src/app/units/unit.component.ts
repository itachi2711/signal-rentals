import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { NotificationService } from '../shared/notification.service';
import { LandlordService } from '../landlords/data/landlord.service';
import { UnitModel } from './model/unit-model';
import { UnitDataSource } from './data/unit-data.source';
import { UnitService } from './data/unit.service';
import { ActivatedRoute } from '@angular/router';
import { PropertyService } from '../properties/data/property.service';
import { AddUnitComponent } from './add/add-unit.component';
import { PropertyModel } from '../properties/models/property-model';
import { CheckboxItem } from '../properties/add/unit-details/check-box-item';
import { UnitTypeService } from '../settings/property/unit-type/data/unit-type.service';
import { AmenityService } from '../settings/property/amenity/data/amenity.service';
import { UtilityService } from '../settings/property/utility/data/utility.service';

@Component({
    selector: 'robi-units',
    templateUrl: './unit.component.html',
    styleUrls: ['./unit.component.scss']
})
export class UnitComponent implements OnInit, AfterViewInit {

    displayedColumns = [
        'unit_name',
        'unit_mode',
        'unit_type_id',
        'total_rooms',
        'bed_rooms',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: UnitDataSource;

    isLandlord = false;
    landlordID: string;

    // Search field
    @ViewChild('search') search: ElementRef;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    propertyID: string;
    property$: Observable<PropertyModel>;
    utilities$: Observable<any>;
    paymentMethods$: Observable<any>;
    amenities$: Observable<any>;
    unitTypes$: Observable<any>;

    allAmenitiesOptions = new Array<CheckboxItem>();
    allUtilitiesOptions = new Array<CheckboxItem>();
    amenities: any;
    utilities: any;
    constructor(private landlordService: LandlordService,
                private unitsService: UnitService,
                private propertyService: PropertyService,
                private notification: NotificationService,
                private route: ActivatedRoute,
                private unitTypeService: UnitTypeService,
                private amenityService: AmenityService,
                private utilityService: UtilityService,
                private dialog: MatDialog) {
        this.isLandlord = this.landlordService.isLandlord();
        this.landlordID = this.landlordService.getLoggedInLandlordID();
        this.propertyID = this.route.snapshot.paramMap.get('id');
        this.property$ = this.propertyService.selectedPropertyChanges$;
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new UnitDataSource(this.unitsService);
        // Load pagination data
        this.dataSource.meta$.subscribe((res) => this.meta = res);

        // load units
        if (this.propertyID) {
            this.dataSource.loadNested(
                this.propertyService.nestedUnitsUrl(this.propertyID),
                '', 0, 0);
        } else {
            this.dataSource.load('', 0, 0, 'created_at', 'desc', 'property_id', this.propertyID);
        }


        this.unitTypes$ = this.unitTypeService.list(['unit_type_name ', 'unit_type_display_name ']);

        // Amenities list
        this.amenities$ = this.amenityService.list(['amenity_name ', 'amenity_display_name ']);
        this.amenities$.subscribe(amenities => {
            this.allAmenitiesOptions = amenities.map(
                x => new CheckboxItem(x.id, x.amenity_display_name));
        });

        // Utility list
        this.utilities$ = this.utilityService.list(['utility_name ', 'utility_display_name ']);
        this.utilities$.subscribe(utilities => {
            this.allUtilitiesOptions = utilities.map(
                x => new CheckboxItem(x.id, x.utility_display_name));
        });
    }

    /**
     * Handle search and pagination
     */
    ngAfterViewInit() {
        fromEvent(this.search.nativeElement, 'keyup')
            .pipe(
                debounceTime(1000),
                distinctUntilChanged(),
                tap(() => {
                    this.paginator.pageIndex = 0;
                    this.loadData();
                })
            ).subscribe();

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

    /**
     * Add dialog launch
     */
    addDialog(isAdd = true, unit?: UnitModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {unit, isAdd,
            propertyID: this.propertyID,
            utilities: this.utilities$,
            amenities$: this.amenities$,
            unitTypes$: this.unitTypes$,
            amenitiesData: this.amenities,
            amenityOptions: this.allAmenitiesOptions,
            utilityOptions: this.allUtilitiesOptions,
        };

        const dialogRef = this.dialog.open(AddUnitComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    if (val === 'deleting') {
                        this.openConfirmationDialog(unit);
                    } else {
                        this.loadData();
                    }
                }
            }
        );
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.dataSource.loadNested(
            this.propertyService.nestedUnitsUrl(this.propertyID),
            this.search.nativeElement.value,
            (this.paginator.pageIndex + 1),
            (this.paginator.pageSize),
            this.sort.active,
            this.sort.direction
        );
    }

    /**
     * Open Edit form
     * @param unit
     */
    openConfirmationDialog(unit: UnitModel) {
        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(unit);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param unit
     */
   delete(unit: UnitModel) {
        this.loader = true;
        this.unitsService.delete(unit)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Unit has been deleted.');
                },
                (error) => {
                    this.loader = false;
                    if (error.error['message']) {
                        this.notification.showNotification('danger', error.error['message']);
                    } else {
                        this.notification.showNotification('danger', 'Delete Error !! ');
                    }
                });
    }
}
