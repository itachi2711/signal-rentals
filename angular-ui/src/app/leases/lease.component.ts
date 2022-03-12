import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { AddLeaseComponent } from './add/add-lease.component';
import { LeaseModel } from './models/lease-model';
import { LeaseDataSource } from './data/lease-data.source';
import { NotificationService } from '../shared/notification.service';
import { LeaseService } from './data/lease.service';
import { LandlordService } from '../landlords/data/landlord.service';
import { TenantService } from '../tenants/data/tenant.service';
import { UserSettingService } from '../settings/user/data/user-setting.service';
import { USER_SCOPES } from '../shared/enums/user-scopes.enum';
import { Router } from '@angular/router';
import { AuthenticationService } from '../authentication/authentication.service';
import { AccountingModel } from '../accounting/models/accounting-model';
import { PdfStatementComponent } from '../accounting/pdf-statement/pdf-statement.component';

@Component({
    selector: 'robi-properties',
    templateUrl: './lease.component.html',
    styleUrls: ['./lease.component.scss']
})
export class LeaseComponent implements OnInit, AfterViewInit {

    displayedColumns = [
        'lease_number',
        'property_id',
        'unit_names',
        'rent_amount',
        'start_date',
        'billed_on',
        'status',
        'statement',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: LeaseDataSource;

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
    activeUser: any;
    isAdmin$: Observable<boolean>;
    isTenant: any;
    isLandlord: any;
    constructor(private landlordService: LandlordService,
                private tenantService: TenantService,
                private userService: UserSettingService,
                private leaseService: LeaseService,
                private notification: NotificationService,
                private authenticationService: AuthenticationService,
                private dialog: MatDialog) {
        this.activeUser = this.userService.getActiveUser();
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.isTenant = this.authenticationService.isTenant();
        this.isLandlord = this.authenticationService.isLandlord();
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new LeaseDataSource(this.leaseService);
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        // load leases
        switch (this.activeUser?.userType) {
            case USER_SCOPES.ADMIN: {
                this.dataSource.load('', 0, 0, 'lease_number', 'desc');
                break;
            }
            case USER_SCOPES.LANDLORD: {
                this.dataSource.loadNested(
                    this.landlordService.nestedLeasesUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
            case USER_SCOPES.TENANT: {
                this.dataSource.loadNested(
                    this.tenantService.nestedLeasesUrl(this.activeUser?.userID),
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
                    this.landlordService.nestedLeasesUrl(this.activeUser?.userID),
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
                    this.tenantService.nestedLeasesUrl(this.activeUser?.userID),
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
     * Add dialog launch
     */
    addDialog(mode: string, landlord?: LeaseModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {landlord,
            mode: mode
        };

        const dialogRef = this.dialog.open(AddLeaseComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                   // this.loadData();
                }
            }
        );
    }

    /**
     * Open Edit form
     * @param lease
     */
    openConfirmationDialog(lease: LeaseModel) {
        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(lease);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param landlord
     */
   delete(landlord: LeaseModel) {
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
     * @param lease
     */
    onSelected(lease: LeaseModel): void {
        this.leaseService.changeSelectedLease(lease);
    }

    viewPdfStatement(lease: LeaseModel) {
        const id = lease?.id;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {
            id: id,
            isLease: true,
        };
        dialogConfig.width = '600px';
        this.dialog.open(PdfStatementComponent, dialogConfig);
    }
}
