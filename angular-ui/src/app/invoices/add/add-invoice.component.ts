import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { InvoiceModel } from '../models/invoice-model';
import { InvoiceService } from '../data/invoice.service';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { PropertyService } from '../../properties/data/property.service';

@Component({
    selector: 'robi-add-tenant',
    styles: [],
    templateUrl: './add-invoice.component.html'
})
export class AddInvoiceComponent implements OnInit  {

    properties: any = [];

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    member: InvoiceModel;

    loader = false;

    memberMethods: any = [];
    groups: any = [];

    formGroup: FormGroup;

    memberStatuses: any = [];
    memberSources: any = [];
    memberTypes: any = [];

    profilePicFileToUpload: File = null;
    membershipFormFileToUpload: File = null;
    profilePicUrl = '';

    membershipFormToUpload: File = null;
    membershipFormUrl = '';

    urls = new Array<string>();

    tenant: InvoiceModel;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;

    utilitySummaryFormGroup: FormGroup;
    utilityBillsFormGroup: FormGroup;

    utilityBillFields: FormArray;


    details = 'noooone';

    utilities$: Observable<any>;
    amenities$: Observable<any>;

    amenities: any;
    utilities: any;
    progress = 0;

    public entryType: string;

    units: any = [];
    units$ = of([]);

    /** control for filter for server side. */
    public propertyServerSideFilteringCtrl: FormControl = new FormControl();
    /** list of tenants filtered after simulating server side search */
    public  filteredServerSideProperties: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** indicate search operation is in progress */
    public searching = false;

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private _formBuilder: FormBuilder,
                private propertyService: PropertyService,
                private utilityBillService: InvoiceService,
                private notification: NotificationService) {
        this.entryType = 'manual';
        // Load properties list
        this.propertyService.list(['property_name', 'location'])
            .subscribe((res) => this.properties = res,
                () => this.properties = []
            );
    }

    ngOnInit() {

        // Property Search
        this.propertyServerSideFilteringCtrl.valueChanges
            .pipe(
                filter(search => !!search),
                tap(() => this.searching = true),
                takeUntil(this._onDestroy),
                debounceTime(200),
                distinctUntilChanged(),
                map(search => {
                    if (!this.properties) {
                        return [];
                    }
                    search = search.toLowerCase();
                    console.log('search', search);

                    // simulate server fetching and filtering data
                    return this.properties.filter(property => {
                        console.log('property', property);
                        return property.property_name.toLowerCase().indexOf(search) > -1
                            || property.location.toLowerCase().indexOf(search) > -1;
                    });
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

     /*   this.loadPropertyTypes();

      //  this.loadPropertyTypes();
        this.loadLeaseTypes();
        this.loadLeaseModes();
        this.loadUtilities();
        this.loadTenantTypes();
        this.loadPaymentFrequencies();*/

       // this.loadUtilities ();
      //  this.loadAmenities();


        // load amenities

        this.utilitySummaryFormGroup = this._formBuilder.group({
            property_id: ['', [Validators.required]],
            utility_id: ['', [Validators.required]],
        });

        /*this.entryChoiceFormGroup = this._formBuilder.group({
            entry_type: [this.entryType]
        });*/

        /*this.autoDataEntryFormGroup = this._formBuilder.group({
            entry_type: [this.entryType]
        });*/

        this.utilityBillsFormGroup = this._formBuilder.group({
            unitBills: this.fb.array([ this.utilityBillFieldCreate() ]),
        });
    }

    /**
     * Update supporting fields when property drop down changes content
     * @param value
     */
    onPropertyItemChange(value) {
        this.units = this.properties.find((item: any) => item.id === value).units;
        this.units$ = of(this.properties.find((item: any) => item.id === value).units);
    }

    /**
     * Update supporting fields when unit drop down changes content
     * @param value
     */
    onUnitItemChange(value) {
    }

    /**
     * For mat-button-toggle-group to select either commercial or residential property unit
     * @param val
     */
    public onToggleChange(val: string) {
        this.entryType = val;
    }

    /*Start Utility Bill section*/

    /**
     * Fetch all defined fields
     */
    get utilityBillFieldsAll () {
        return <FormArray>this.utilityBillsFormGroup.get('unitBills');
    }

    /**
     * Initial field creation
     * @param data
     */
    utilityBillFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            unit_id: [data?.unit_id, [Validators.required]],
            reading_date: [data ? data?.reading_date : (new Date()).toISOString().substring(0, 10), [Validators.required]],
            current_reading: [data?.current_reading, [Validators.required]],
        });
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    utilityBillFieldAdd(data?: any): void {
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitBills') as FormArray;
        this.utilityBillFields.push(this.utilityBillFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    utilityBillFieldRemove(i): void {
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitBills') as FormArray;
        this.utilityBillFields.removeAt(i);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    utilityBillFieldCopy(i): void {
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitBills') as FormArray;
        const holder = [];
        holder.push(this.utilityBillFields.value[i])
        this.utilityBillFieldAdd(...holder);
    }

    /* End Utility Bills section*/

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);

        const unitBills = {...this.utilitySummaryFormGroup.value, ...this.utilityBillsFormGroup.value};

        this.loader = true;

        this.utilityBillService.create(unitBills).subscribe((data) => {
               // this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Utility Readings Added.');
            },
            (error) => {
                this.errorInForm.next(true);

                this.loader = false;
                if (error.member === 0) {
                    this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                        ' Check your connection and retry.');
                    return;
                }
                // An array of all form errors as returned by server
                this.formErrors = error?.error;

                if (this.formErrors) {

                    // loop through from fields, If has an error, mark as invalid so mat-error can show
                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            this.stepper.selectedIndex = 0;

                            if (this.utilitySummaryFormGroup.controls[prop]) {
                                this.utilitySummaryFormGroup.controls[prop]?.markAsTouched();
                                this.utilitySummaryFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.utilityBillsFormGroup.controls[prop]) {
                                this.utilityBillsFormGroup.controls[prop]?.markAsTouched();
                                this.utilityBillsFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                }

            });
    }

}

