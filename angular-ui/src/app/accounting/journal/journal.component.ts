import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { GeneralJournalDataSource } from '../data/general-journal-data.source';
import { AccountingService } from '../data/accounting.service';
import { GeneralJournalService } from '../data/general-journal.service';
import { NotificationService } from '../../shared/notification.service';
// import { MemberService } from '../../members/data/member.service';
// import { BranchService } from '../../settings/branch/general/data/branch.service';
import { fromEvent, merge } from 'rxjs';
import { debounceTime, distinctUntilChanged, tap } from 'rxjs/operators';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { PropertyService } from '../../properties/data/property.service';
// import { branch } from '../../auth/auth.selectors';

@Component({
    selector: 'robi-journal',
    templateUrl: './journal.component.html',
    styleUrls: ['./journal.component.css']
})
export class JournalComponent implements OnInit, AfterViewInit {
    journalColumns = [
        'created_at',
        'narration',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'prepared_by',
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    // Search field
    @ViewChild('search') search: ElementRef;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    metaLedger: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;

    // Data for the list table display
    dataSourceJournal: GeneralJournalDataSource;

    members: any;
    properties$: any;
    propertyID: any;

    form: FormGroup;

    constructor(private fb: FormBuilder, private service: AccountingService,
                private generalJournalService: GeneralJournalService, private store: Store<AppState>,
                private notification: NotificationService, private dialog: MatDialog,
                private memberService: NotificationService, private propertyService: PropertyService) {
       // this.store.pipe(select(branch)).subscribe(user => this.branchId = user);
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {

        this.dataSourceJournal = new GeneralJournalDataSource(this.generalJournalService);

        // Load pagination data
        this.dataSourceJournal.meta$.subscribe((res) => this.metaLedger = res);

        // We load initial data here to avoid affecting life cycle hooks if we load all data on after view init
        this.dataSourceJournal.load('', 0, 0, 'created_at', 'desc', 'property_id', this.propertyID);

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

    /**
     * Fetch data from data lead
     */
    loadData() {
       // console.log(this.sort.direction);
        this.dataSourceJournal.load(
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
