import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { AddVacateComponent } from './add/add-vacate.component';
import { VacateModel } from './models/vacate-model';
import { VacateDataSource } from './data/vacate-data.source';
import { NotificationService } from '../shared/notification.service';
import { VacateService } from './data/vacate.service';
import { UserSettingService } from '../settings/user/data/user-setting.service';
import { USER_SCOPES } from '../shared/enums/user-scopes.enum';
import { LandlordService } from '../landlords/data/landlord.service';
import { TenantService } from '../tenants/data/tenant.service';
import { AuthenticationService } from '../authentication/authentication.service';

@Component({
    selector: 'robi-vacate',
    templateUrl: './vacate.component.html',
    styleUrls: ['./vacate.component.scss']
})
export class VacateComponent implements OnInit, AfterViewInit {

    displayedColumns = [
        'vacating_date',
        'tenant_id',
        'lease',
        'property_id',
        'unit',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: VacateDataSource;

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
    constructor(private vacateNoticeService: VacateService,
                private notification: NotificationService,
                private userService: UserSettingService,
                private landlordService: LandlordService,
                private authenticationService: AuthenticationService,
                private tenantService: TenantService,
                private dialog: MatDialog) {
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.activeUser = this.userService.getActiveUser();
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new VacateDataSource(this.vacateNoticeService);
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        switch (this.activeUser?.userType) {
            case USER_SCOPES.ADMIN: {
                this.dataSource.load('', 0, 0, 'updated_at', 'desc');
                break;
            }
            case USER_SCOPES.LANDLORD: {
                this.dataSource.loadNested(
                    this.landlordService.nestedNoticesUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
            case USER_SCOPES.TENANT: {
                this.dataSource.loadNested(
                    this.tenantService.nestedNoticesUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
        }
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
                    this.landlordService.nestedNoticesUrl(this.activeUser?.userID),
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
                    this.tenantService.nestedNoticesUrl(this.activeUser?.userID),
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
     * @param isAdd
     * @param vacateNotice
     */
    addDialog(isAdd = true, vacateNotice?: VacateModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {vacateNotice, isAdd};

        const dialogRef = this.dialog.open(AddVacateComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    if (val === 'deleting') {
                        this.openConfirmationDialog(vacateNotice);
                    } else {
                    this.loadData();
                    }
                }
            }
        );
    }

    /**
     * Open Edit form
     * @param vacateNotice
     */
    openConfirmationDialog(vacateNotice: VacateModel) {
        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(vacateNotice);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param vacateNotice
     */
   delete(vacateNotice: VacateModel) {
        this.loader = true;
        this.vacateNoticeService.delete(vacateNotice)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Vacate Notice has been deleted.');
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

    /**
     * @param notice
     */
    onSelected(notice: VacateModel): void {
        this.vacateNoticeService.changeSelectedVacateNotice(notice);
    }
}
