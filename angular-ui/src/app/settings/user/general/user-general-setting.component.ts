import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';

import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';

import { AddUserComponent } from './add/add-user.component';
import { UserSettingModel } from '../model/user-setting.model';
import { UserSettingDataSource } from '../data/user-setting-data.source';
import { UserSettingService } from '../data/user-setting.service';
import { NotificationService } from '../../../shared/notification.service';
import { RoleSettingService } from '../data/role-setting.service';

@Component({
    selector: 'robi-user-general-setting',
    templateUrl: './user-general-setting.component.html',
    styleUrls: ['./user-general-setting.component.css']
})
export class UserGeneralSettingComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'actions',
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
    dataSource: UserSettingDataSource;

    roles: any = [];

    constructor(private service: UserSettingService, private notification: NotificationService,
                private roleService: RoleSettingService, private dialog: MatDialog) {
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new UserSettingDataSource(this.service);
        // Load pagination data
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        // We load initial data here to avoid affecting life cycle hooks if we load all data on after view init
        this.dataSource.load('', 0, 0, 'updated_at', 'desc');
        this.roleService.list('name')
            .subscribe((res) => this.roles = res,
                () => this.roles = []
            );
    }

    /**
     * @param isAdd
     * @param user
     */
    addDialog(isAdd = true, user?: UserSettingModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {user, isAdd,
            roles: this.roles,
        };
        dialogConfig.width = '550px';
        dialogConfig.minHeight = '500px';

        const dialogRef = this.dialog.open(AddUserComponent, dialogConfig);

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
     * @param user
     */
    openConfirmationDialog(user: UserSettingModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(user);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param user
     */
    delete(user: UserSettingModel) {
        this.loader = true;
        this.service.delete(user)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! User has been deleted.');
                },
                (error) => {
                    this.loader = false;
                    if (!error.error['error']) {
                        this.notification.showNotification('danger', error?.error?.message);
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
