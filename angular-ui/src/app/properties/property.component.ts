import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { AddPropertyComponent } from './add/add-property.component';
import { PropertyModel } from './models/property-model';
import { PropertyDataSource } from './data/property-data.source';
import { NotificationService } from '../shared/notification.service';
import { PropertyService } from './data/property.service';
import { LandlordService } from '../landlords/data/landlord.service';
import { AuthenticationService } from '../authentication/authentication.service';
import { USER_SCOPES } from '../shared/enums/user-scopes.enum';
import { UserSettingService } from '../settings/user/data/user-setting.service';
import { TenantService } from '../tenants/data/tenant.service';

@Component({
    selector: 'robi-properties',
    templateUrl: './property.component.html',
    styleUrls: ['./property.component.scss']
})
export class PropertyComponent implements OnInit, AfterViewInit {

    displayedColumns = [
        'property_code',
        'property_name',
        'location',
        // @itachi
        'plot_area',
        'covered_area,',
        'dm_circle_rate,',
        // @itachi
        'total_units',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: PropertyDataSource;

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
    isAdmin$: Observable<boolean>;
    activeUser: any;
    constructor(private userService: UserSettingService,
                private landlordService: LandlordService,
                private tenantService: TenantService,
                private propertyService: PropertyService,
                private notification: NotificationService,
                private authenticationService: AuthenticationService,
                private dialog: MatDialog) {
        this.activeUser = this.userService.getActiveUser();
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.isLandlord = this.landlordService.isLandlord();
        this.landlordID = this.landlordService.getLoggedInLandlordID();
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new PropertyDataSource(this.propertyService);
        // Load pagination data
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        // load properties
        switch (this.activeUser?.userType) {
            case USER_SCOPES.ADMIN: {
                this.dataSource.load('', 0, 0, 'updated_at', 'desc');
                break;
            }
            case USER_SCOPES.LANDLORD: {
                this.dataSource.loadNested(
                    this.landlordService.nestedPropertiesUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
            case USER_SCOPES.TENANT: {
                this.dataSource.loadNested(
                    this.tenantService.nestedPropertiesUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
        }
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
    addDialog(mode: string, property?: PropertyModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {property,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddPropertyComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    this.loadData();
                }
            }
        );
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        switch (this.activeUser?.userType) {
            case USER_SCOPES.ADMIN: {
                this.dataSource.load(
                    this.search.nativeElement.value,
                    (this.paginator.pageIndex + 1),
                    (this.paginator.pageSize),
                    this.sort.active,
                    this.sort.direction
                );
                break;
            }
            case USER_SCOPES.LANDLORD: {
                this.dataSource.loadNested(
                    this.landlordService.nestedPropertiesUrl(this.activeUser?.userID),
                    this.search.nativeElement.value,
                    (this.paginator.pageIndex + 1),
                    (this.paginator.pageSize),
                    this.sort.active,
                    this.sort.direction
                );
                break;
            }
            case USER_SCOPES.TENANT: {
                this.dataSource.loadNested(
                    this.tenantService.nestedPropertiesUrl(this.activeUser?.userID),
                    this.search.nativeElement.value,
                    (this.paginator.pageIndex + 1),
                    (this.paginator.pageSize),
                    this.sort.active,
                    this.sort.direction
                );
                break;
            }
        }
    }

    /**
     * Open Edit form
     * @param property
     */
    openConfirmationDialog(property: PropertyModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(property);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param property
     */
   delete(property: PropertyModel) {
       // this.loader = true;
     /*   this.service.delete(lead)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Lead has been deleted.');
                },
                (error) => {
                    this.loader = false;
                    if (!error.error['error']) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing deleted.' +
                            ' Check Connection and retry. ');
                    } else {
                        this.notification.showNotification('danger', 'Delete Error !! ');
                    }
                });*/
    }

    /**
     * @param property
     */
    onSelected(property: PropertyModel): void {
        this.propertyService.changeSelectedProperty(property);
    }
}
