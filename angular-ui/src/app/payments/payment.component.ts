import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { AddPaymentComponent } from './add/add-payment.component';
import { PaymentModel } from './models/payment-model';
import { PaymentDataSource } from './data/payment-data.source';
import { NotificationService } from '../shared/notification.service';
import { PaymentService } from './data/payment.service';
import { PaymentDetailComponent } from './details/payment-detail.component';
import { StatusChangeComponent } from './status-change/status-change.component';
import { select, Store } from '@ngrx/store';
import { selectorIsAgent, selectorIsLandlord, selectorUserID } from '../authentication/authentication.selectors';
import { AppState } from '../reducers';
import { LandlordService } from '../landlords/data/landlord.service';
import { AuthenticationService } from '../authentication/authentication.service';
import { UserSettingService } from '../settings/user/data/user-setting.service';
import { TenantService } from '../tenants/data/tenant.service';
import { USER_SCOPES } from '../shared/enums/user-scopes.enum';

@Component({
    selector: 'robi-utility-bills',
    templateUrl: './payment.component.html',
    styleUrls: ['./payment.component.scss']
})
export class PaymentComponent implements OnInit, AfterViewInit {

    displayedColumns = [
        'amount',
        'payment_method_id',
        'payment_date',
        'tenant_id',
        'lease_id',
        'property_id',
        'receipt_number',
        'payment_status',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: PaymentDataSource;

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
    isAgent$: Observable<any>;
    isLandlord = false;
    landlordID: string;
    isAdmin$: Observable<boolean>;
    activeUser: any;
    constructor(private store: Store<AppState>,
                private userService: UserSettingService,
                private landlordService: LandlordService,
                private tenantService: TenantService,
                private paymentService: PaymentService,
                private utilityBillService: PaymentService,
                private notification: NotificationService,
                private authenticationService: AuthenticationService,
                private dialog: MatDialog) {
        this.activeUser = this.userService.getActiveUser();
        this.isAgent$ = this.store.pipe(select(selectorIsAgent));
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => {
            if (isLandlord) {
                this.isLandlord = true;
                this.store.pipe(select(selectorUserID)).subscribe(userID => this.landlordID = userID);
            }
        });
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new PaymentDataSource(this.paymentService);
        this.dataSource.meta$.subscribe((res) => this.meta = res);

        // load Payments
        switch (this.activeUser?.userType) {
            case USER_SCOPES.ADMIN: {
                this.dataSource.load('', 0, 0, 'updated_at', 'desc');
                break;
            }
            case USER_SCOPES.LANDLORD: {
                this.dataSource.loadNested(
                    this.landlordService.nestedPaymentsUrl(this.activeUser?.userID),
                    '', 0, 0);
                break;
            }
            case USER_SCOPES.TENANT: {
                this.dataSource.loadNested(
                    this.tenantService.nestedPaymentsUrl(this.activeUser?.userID),
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
                    this.landlordService.nestedPaymentsUrl(this.activeUser?.userID),
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
                    this.tenantService.nestedPaymentsUrl(this.activeUser?.userID),
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
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }

    /**
     *
     */
    addDialog() {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {};

        const dialogRef = this.dialog.open(AddPaymentComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    this.loadData();
                }
            }
        );
    }

    /**
     * paymentDetails dialog launch
     */
    paymentDetails(data: PaymentModel, isStandAlone = false) {

        const id = data.id;

        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {data, isStandAlone};

        const dialogRef = this.dialog.open(PaymentDetailComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                }
            }
        );
    }

    /**
     *
     * @param data
     */
    approvePayment(data: PaymentModel) {
        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true,
            data: {
                'title' : 'Approve Payment? Confirm permanent action.'
            }
        });
        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.paymentService.approve({id: data.id}).subscribe((payment) => {
                        this.loader = false;
                        this.notification.showNotification('success', 'Success !! Payment has been Approved.');
                        this.loadData();
                    },
                    (error) => {
                        this.notification.showNotification('danger', 'Error !! Could not approve payment.');
                    }
                );
            }
            this.dialogRef = null;
        });
    }

    /**
     *
     * @param data
     */
    cancelPayment(data: PaymentModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {data};
        const dialogRef = this.dialog.open(StatusChangeComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    this.loadData();
                }
            }
        );
    }

    /**
     * Open Edit form
     * @param landlord
     */
    openConfirmationDialog(landlord: PaymentModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(landlord);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param landlord
     */
   delete(landlord: PaymentModel) {
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

    onSelected(payment: PaymentModel): void {
        this.paymentService.changeSelectedPayment(payment);
    }
}
