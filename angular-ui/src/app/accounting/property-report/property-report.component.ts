import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { AccountingService } from '../data/accounting.service';
import { GeneralJournalService } from '../data/general-journal.service';
import { NotificationService } from '../../shared/notification.service';
import { Observable, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { PropertyService } from '../../properties/data/property.service';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
    selector: 'robi-property-report',
    templateUrl: './property-report.component.html',
    styleUrls: ['./property-report.component.css']
})
export class PropertyReportComponent implements OnInit {
    loader = false;

    form: FormGroup;

    /** control for filter for server side. */
    public propertyServerSideFilteringCtrl: FormControl = new FormControl();

    /** indicate search operation is in progress */
    public searching = false;

    /** list of banks filtered after simulating server side search */
    public  filteredServerSideProperties: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    propertiesFiltered$: Observable<any>;
    periods$: Observable<any>;
    pdfSrc: any;
    domSanitizer: DomSanitizer;

    constructor(private fb: FormBuilder, private service: AccountingService,
                private generalJournalService: GeneralJournalService,
                sanitizer: DomSanitizer,
                private notification: NotificationService,
                private dialog: MatDialog,
                private memberService: NotificationService,
                private propertyService: PropertyService) {
        this.domSanitizer = sanitizer;
    }

    /**
     * Initialize data lead
     * Set pagination data values
     * Initial data load
     */
    ngOnInit() {
        this.form = this.fb.group({
            property_id: [''],
            period_id: ['']
        });

        // listen for search field value changes
        this.propertyServerSideFilteringCtrl.valueChanges
            .pipe(
                filter(search => !!search),
                tap(() => this.searching = true),
                takeUntil(this._onDestroy),
                debounceTime(2000),
                distinctUntilChanged(),
                map(search => {
                    search = search.toLowerCase();
                    // Server fetching and filtering data
                    this.propertiesFiltered$ =  this.propertyService.search(search);
                }),
                delay(500)
            )
            .subscribe(filteredProperties => {
                    this.searching = false;
                    this.filteredServerSideProperties.next(filteredProperties);
                },
                error => {
                    this.searching = false;
                });

    }

    /**
     * @param propertyID
     */
    onPropertyItemChange(propertyID) {
        this.periods$ = this.propertyService.periods(propertyID);
    }

    onPeriodItemChange(periodID) {
       // this.fetchBillingSummary();
    }

    fetchReport() {
        const body = {...this.form.value};
        this.loader = true;
        this.propertyService.report(body)
            .subscribe((res) => {
                    this.pdfSrc = this.domSanitizer.bypassSecurityTrustResourceUrl(
                        URL.createObjectURL(res)
                    );
                    this.loader = false;
                },
                () => {
                    this.loader = false;
                    this.notification.showNotification('danger', 'Error downloading Report !');
                }
            );
    }
}
