import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { select, Store } from '@ngrx/store';
import { LocalStorageService } from '../core/local-storage/local-storage.service';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { merge, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { PropertyService } from '../properties/data/property.service';
import {
    selectorUserID, selectorUserScopes
} from '../authentication/authentication.selectors';
import { VacantUnitService } from '../units/data/vacant-unit.service';
import { MatPaginator } from '@angular/material/paginator';
import { VacantUnitDataSource } from '../units/data/vacant-unit-data.source';
import { MatSort } from '@angular/material/sort';
import { AdminSummaryModel } from './model/admin-summary-model';
import { ActivatedRoute } from '@angular/router';
import { ChartType } from 'chart.js';

@Component({
  selector: 'robi-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit, AfterViewInit {

    scopes$: any;
    formBillingStats: FormGroup;
    properties: any = [];

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
    LoggedInUserId: string;

    vacantUnitColumns = [
        'unit_mode',
        'unit_type_id',
        'unit_name',
        'property_id',
        'location'
    ];
    loader = false;

    vacantUnitsDataSource: VacantUnitDataSource;
    // Search field
    @ViewChild('search') search: ElementRef;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [3, 5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;

    billingForm: FormGroup;
    adminSummary: AdminSummaryModel;
    billed$: Observable<string>;
    paid$: Observable<string>;
    pending$: Observable<string>;

    public barChartOptions = {
        scaleShowVerticalLines: false,
        responsive: true
    };
    public barChartLabels = [];
    public barChartType = 'bar'  as ChartType;
    public barChartLegend = true;
    public barChartData = [];

    public pieChartLabels = ['Pending', 'Paid', 'Billed'];
    public pieChartData$: Observable<[any, any, any]>;
    public pieChartType = 'doughnut' as ChartType;
    options = {
        responsive: true,
        maintainAspectRatio: false
    }
  constructor(private store: Store,
              private localStorageService: LocalStorageService,
              private route: ActivatedRoute,
              private _formBuilder: FormBuilder,
              private propertyService: PropertyService,
              private vacantUnitService: VacantUnitService) {
      this.formBillingStats = this._formBuilder.group({
          property_id: [''],
          period_id: [''],
      });
  }
  ngOnInit() {
      this.scopes$ = this.store.pipe(select(selectorUserScopes));
      this.store.pipe(select(selectorUserID)).subscribe(id => this.LoggedInUserId = id);

      if (this.route.snapshot.data['adminData']) {
          this.adminSummary = this.route.snapshot.data['adminData'];
      }

      this.periods$ = of(this.adminSummary?.periodical_billing);
      const firstPeriod = this.adminSummary?.periodical_billing[0];
      this.billed$ = of(firstPeriod?.amount_billed_as_currency);
      this.paid$ = of(firstPeriod?.amount_paid_as_currency);
      this.pending$ = of(firstPeriod?.amount_due_as_currency);

      this.billingForm = this._formBuilder.group({
          period_id: [firstPeriod?.period_id, [Validators.required]]
      });

      this.pieChartData$ = of([
          firstPeriod?.amount_due,
          firstPeriod?.amount_paid,
          firstPeriod?.amount_billed,
      ]);

      const periodNames = [];
      const billedData = [];
      const paidData = [];
      const pendingData = [];
      const periodBilling = this.adminSummary?.periodical_billing.reverse();
      periodBilling.forEach(function (period) {
          periodNames.push(period?.period_name);
          billedData.push(period?.amount_billed);
          paidData.push(period?.amount_paid);
          pendingData.push(period?.amount_due);
      });
      this.barChartLabels = periodNames;
      this.barChartData = [
          {data: pendingData, label: 'Pending'},
          {data: paidData, label: 'Paid'},
          {data: billedData, label: 'Billed'},
      ];

      this.vacantUnitsDataSource = new VacantUnitDataSource(this.vacantUnitService);
      this.vacantUnitsDataSource.meta$.subscribe((res) => this.meta = res);
      this.vacantUnitsDataSource.load('', 0, 0, 'id', 'desc');

      // property search
      this.propertyServerSideFilteringCtrl.valueChanges
          .pipe(
              filter(search => !!search),
              tap(() => this.searching = true),
              takeUntil(this._onDestroy),
              debounceTime(2000),
              distinctUntilChanged(),
              map(search => {
                  search = search.toLowerCase();
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

    onPeriodChange(periodID) {
        const periodBilling  = this.adminSummary?.periodical_billing;
        const selectedPeriod = periodBilling.find((item: any) => item?.period_id === periodID);

        this.billed$ = of(selectedPeriod?.amount_billed_as_currency);
        this.paid$ = of(selectedPeriod?.amount_paid_as_currency);
        this.pending$ = of(selectedPeriod?.amount_due_as_currency);

        this.pieChartData$ = of([
            selectedPeriod?.amount_due,
            selectedPeriod?.amount_paid,
            selectedPeriod?.amount_billed,
        ]);
    }

    /**
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }

    /**
     * Handle search and pagination
     */
    ngAfterViewInit() {
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
        this.vacantUnitsDataSource.load(
            '',
            (this.paginator.pageIndex + 1),
            (this.paginator.pageSize),
            this.sort.active,
            this.sort.direction
        );
    }
}
