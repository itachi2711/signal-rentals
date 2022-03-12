import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup} from '@angular/forms';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { AccountingDataSource } from '../data/accounting-data.source';
import { AccountingService } from '../data/accounting.service';
import { GeneralJournalService } from '../data/general-journal.service';
import { NotificationService } from '../../shared/notification.service';
import { AccountingModel } from '../models/accounting-model';
import { StatementComponent } from '../statement/statement.component';
import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { PropertyService } from '../../properties/data/property.service';
import { PdfStatementComponent } from '../pdf-statement/pdf-statement.component';

@Component({
    selector: 'robi-ledger',
    templateUrl: './ledger.component.html',
    styleUrls: ['./ledger.component.css']
})
export class LedgerComponent implements OnInit, AfterViewInit {
    displayedColumns = [
        'account_number',
        'account_name',
        'balance',
        'actions',
    ];
    loader = false;
    dialogRef: MatDialogRef<ConfirmationDialogComponent>;
    @ViewChild('search') search: ElementRef;
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;
    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    dataSource: AccountingDataSource;
    properties$: any;
    propertyID: any;
    form: FormGroup;
    constructor(private fb: FormBuilder, private service: AccountingService,
                private generalJournalService: GeneralJournalService,
                private notification: NotificationService, private dialog: MatDialog,
                private memberService: NotificationService, private propertyService: PropertyService) {}

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.dataSource = new AccountingDataSource(this.service);
        this.dataSource.meta$.subscribe((res) => this.meta = res);
        this.dataSource.load('', 0, 0, 'account_number', 'desc', 'property_id', this.propertyID);
        this.properties$ = this.propertyService.list(['property_name']);
        this.form = this.fb.group({
            property_id: [this.propertyID],
            include_members: ['']
        });
    }

    /**
     *
     * @param value
     */
    onPropertyChange(value) {
        this.propertyID = value;
        this.loadData();
    }

    viewStatement(account: AccountingModel) {
        const id = account.id;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;
        dialogConfig.data = {
            id: id,
            type: 'general'
        };
        const dialogRef = this.dialog.open(StatementComponent, dialogConfig);
    }

    viewPdfStatement(account: AccountingModel) {
        const id = account?.id;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        dialogConfig.data = {
            id: id
        };
        dialogConfig.width = '600px';

        const dialogRef = this.dialog.open(PdfStatementComponent, dialogConfig);
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
            this.sort.direction,
            'property_id',
            this.propertyID
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
            // startWith(null),
            tap(() => this.loadData() )
          //  tap( () => console.log('Page Index: ' + (this.paginator.pageIndex + 1))),
           // tap( () => console.log('Page Size: ' + (this.paginator.pageSize)))
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
}
