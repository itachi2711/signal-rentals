import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';
import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { AddPaymentMethodComponent } from './add/add-payment-method.component';
import { PaymentMethodModel } from './model/payment-method-model';
import { PaymentMethodDataSource } from './data/payment-method-data.source';
import { PaymentMethodService } from './data/payment-method.service';
import { NotificationService } from '../../../shared/notification.service';

@Component({
    selector: 'robi-payment-method-setting',
    templateUrl: './payment-method-setting.component.html',
    styleUrls: ['./payment-method-setting.component.css']
})
export class PaymentMethodSettingComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'payment_method_name',
        'payment_method_display_name',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    // Search field
    @ViewChild('search', {static: true}) search: ElementRef;
    // pagination
    @ViewChild(MatPaginator, {static: true}) paginator: MatPaginator;
    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;

    // Data for the list table display
    dataSource: PaymentMethodDataSource;

    roles: any = [];

    constructor(private paymentMethodService: PaymentMethodService,
                private notification: NotificationService,
                private dialog: MatDialog) {
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new PaymentMethodDataSource(this.paymentMethodService);
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        this.dataSource.load('', 0, 0, 'updated_at', 'desc');
    }

    /**
     * @param isAdd
     * @param paymentMethod
     */
    addDialog(isAdd = true, paymentMethod?: PaymentMethodModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {paymentMethod, isAdd,
            roles: this.roles,
        };
        dialogConfig.width = '500px';
        const dialogRef = this.dialog.open(AddPaymentMethodComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    this.loadData();
                }
            }
        );
    }

    /**
     * Fetch data from data source
     */
    loadData() {
        this.dataSource.load(
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
     * Open Edit form
     * @param paymentMethod
     */
    openConfirmationDialog(paymentMethod: PaymentMethodModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(paymentMethod);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param paymentMethod
     */
    delete(paymentMethod: PaymentMethodModel) {
        this.loader = true;
        this.paymentMethodService.delete(paymentMethod)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Payment Method has been deleted.');
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
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }
}
