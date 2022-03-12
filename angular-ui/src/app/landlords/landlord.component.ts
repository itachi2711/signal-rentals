import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { fromEvent, merge, Observable } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { ConfirmationDialogComponent } from '../shared/delete/confirmation-dialog-component';
import { AddLandlordComponent } from './add/add-landlord.component';
import { LandlordModel } from './models/landlord-model';
import { LandlordDataSource } from './data/landlord-data.source';
import { NotificationService } from '../shared/notification.service';
import { LandlordService } from './data/landlord.service';
import { AuthenticationService } from '../authentication/authentication.service';
@Component({
    selector: 'robi-landlords',
    templateUrl: './landlord.component.html',
    styleUrls: ['./landlord.component.scss']
})
export class LandlordComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'actions'
    ];

    loader = false;
    dialogRef: MatDialogRef<ConfirmationDialogComponent>;
    dataSource: LandlordDataSource;
    @ViewChild('search') search: ElementRef;
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    isAdmin$: Observable<boolean>;
    constructor(private landlordService: LandlordService,
                private notification: NotificationService,
                private authenticationService: AuthenticationService,
                private dialog: MatDialog) {
        this.isAdmin$ = this.authenticationService.isAdmin();
    }
    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new LandlordDataSource(this.landlordService);
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        this.dataSource.load('', 0, 0, 'updated_at', 'desc');
    }

    /**
     * Fetch data from data lead
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
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }

    /**
     * @param isAdd
     * @param landlord
     */
    addDialog(isAdd = true, landlord?: LandlordModel) {
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {landlord, isAdd};

        const dialogRef = this.dialog.open(AddLandlordComponent, dialogConfig);
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
    openConfirmationDialog(landlord: LandlordModel) {
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
   delete(landlord: LandlordModel) {
        this.loader = true;
        this.landlordService.delete(landlord)
            .subscribe((data) => {
                    this.loader = false;
                    this.loadData();
                    this.notification.showNotification('success', 'Success !! Landlord has been deleted.');
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
     * @param landlord
     */
    onSelected(landlord: LandlordModel): void {
        this.landlordService.changeSelectedLandlord(landlord);
    }
}
