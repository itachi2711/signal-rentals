import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';
import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';

import { NotificationService } from '../../../shared/notification.service';
import { EmailGeneralSettingModel } from './model/email-general-setting.model';
import { EmailGeneralDataSource } from './data/email-general-data.source';
import { EmailGeneralService } from './data/email-general.service';
import { EditEmailGeneralComponent } from './edit/edit-email-general.component';

@Component({
    selector: 'robi-system-email-setting',
    templateUrl: './email-general-setting.component.html',
    styleUrls: ['./email-general-setting.component.css']
})
export class EmailGeneralSettingComponent implements OnInit, AfterViewInit {
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
    dataSource: EmailGeneralDataSource;

    emailTemplates: any;

    smsTemplates: any;

    displayedColumns = [
        'display_name',
        'send_email',
        'send_sms',
        'actions',
    ];

    constructor(private emailGeneralService: EmailGeneralService,
                private notification: NotificationService,
                private dialog: MatDialog) {
    }

    /**
     * Initialize data source
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new EmailGeneralDataSource(this.emailGeneralService);
        // Load pagination data
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        // We load initial data here to avoid affecting life cycle hooks if we load all data on after view init
        this.dataSource.load('', 0, 0, 'display_name', 'asc');
    }

    /**
     * Edit dialog launch
     */
    editDialog(communicationSetting: EmailGeneralSettingModel) {

        const id = communicationSetting.id;

        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.width = '500px';
        dialogConfig.height = '350px';

        dialogConfig.data = {communicationSetting,
            emailTemplates: this.emailTemplates,
            smsTemplates: this.smsTemplates
        };

        const dialogRef = this.dialog.open(EditEmailGeneralComponent, dialogConfig);
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
     *
     * @param communicationSetting
     */
    openConfirmationDialog(communicationSetting: EmailGeneralSettingModel) {

        this.dialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });

        this.dialogRef.afterClosed().subscribe((result) => {
            if (result) {
            }
            this.dialogRef = null;
        });
    }

    /**
     *
     * @param communicationSetting
     */
    delete(communicationSetting: EmailGeneralSettingModel) {
        this.loader = true;
        this.emailGeneralService.delete(communicationSetting)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Communication Setting has been deleted.');
                },
                (error) => {
                    this.loader = false;
                    if (!error.error['error']) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing deleted.' +
                            ' Check Connection and retry. ');
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
