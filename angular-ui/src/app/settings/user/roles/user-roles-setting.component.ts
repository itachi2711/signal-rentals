import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';

import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { RoleSettingDataSource } from '../data/role-setting-data.source';
import { AddRoleComponent } from './add/add-role.component';
import { RoleSettingModel } from '../model/role-setting-model';
import { EditRoleComponent } from './edit/edit-role.component';
import { CheckboxItem } from './edit/check-box-item';
import { RoleSettingService } from '../data/role-setting.service';
import { NotificationService } from '../../../shared/notification.service';
import { PermissionSettingService } from '../data/permission-setting.service';

@Component({
    selector: 'robi-user-role-setting',
    templateUrl: './user-roles-setting.component.html',
    styleUrls: ['./user-roles-setting.component.css']
})
export class UserRolesSettingComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'name',
        'display_name',
        'permissions',
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
    dataSource: RoleSettingDataSource;

    allPermissions: any;
    allPermissionsOptions = new Array<CheckboxItem>();

    rolePermissions: any;


    constructor(private roleService: RoleSettingService, private permissionService: PermissionSettingService,
                private notification: NotificationService, private dialog: MatDialog) {
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {

        this.dataSource = new RoleSettingDataSource(this.roleService);

        // Load pagination data
        this.dataSource.meta$.subscribe((res) => this.meta = res);

        // We load initial data here to avoid affecting life cycle hooks if we load all data on after view init
        this.dataSource.load('', 0, 0, 'updated_at', 'desc');

        // Fetch all permissions
        this.permissionService.list(['name', 'display_name'])
            .subscribe((res) => {
                    this.allPermissions = res;
                    this.allPermissionsOptions = this.allPermissions.map(
                        x => new CheckboxItem(x.id, x.display_name));
                },
                () => this.allPermissions = []
            );

    }

    /**
     * Add dialog launch
     */
    addDialog() {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.width = '550px';
        dialogConfig.height = '450px';

        const dialogRef = this.dialog.open(AddRoleComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    this.loadData();
                }
            }
        );
    }

    /**
     * Edit dialog launch
     */
    editDialog(role: RoleSettingModel) {

        const id = role.id;

        const data = {
            role,
            permOptions: this.allPermissionsOptions
        };

        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {role,
            permOptions: this.allPermissionsOptions};

        dialogConfig.width = '550px';
        dialogConfig.height = '450px';

        const dialogRef = this.dialog.open(EditRoleComponent, dialogConfig);
        dialogRef.afterClosed().subscribe(
            (val) => {
                if ((val)) {
                    if (val === 'deleting') {
                        this.openConfirmationDialog(role);
                    } else {
                        this.loadData();
                    }
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
     * @param role
     */
    openConfirmationDialog(role: RoleSettingModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(role);
            }
            this.dialogRef = null;
        });
    }

    /**
     * Remove resource from db
     * @param role
     */
    delete(role: RoleSettingModel) {
        this.loader = true;
        this.roleService.delete(role)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Role has been deleted.');
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
