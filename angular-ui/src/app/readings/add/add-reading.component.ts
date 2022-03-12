import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ReadingModel } from '../models/reading-model';
import { ReadingService } from '../data/reading.service';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { PropertyService } from '../../properties/data/property.service';
import { UtilityService } from '../../settings/property/utility/data/utility.service';
import { Router } from '@angular/router';

@Component({
    selector: 'robi-add-reading',
    styles: [],
    templateUrl: './add-reading.component.html'
})
export class AddReadingComponent implements OnInit  {

    properties: any = [];
    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    member: ReadingModel;

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

    tenant: ReadingModel;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;
    autoDataEntryFormGroup: FormGroup;

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
    previousReadings$: Observable<any>;
    pastReadings: any = [];
    utilityID: string;

    consumption$: Observable<any>;
    consumptionBank: any = [];


    /** control for filter for server side. */
    public propertyServerSideFilteringCtrl: FormControl = new FormControl();
    /** list of tenants filtered after simulating server side search */
    public  filteredServerSideProperties: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** indicate search operation is in progress */
    public searching = false;

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    entryChoice = 'manual';
    dataEntryOptions = [ {'id': 'manual', 'description': 'Manual Entry'},
                        {'id': 'import', 'description': 'Auto Import (CSV or Excel)'}
                        ];

    logoToUpload: File = null;
    logoUrl = '';
    showLogo: any;

    photoToUpload: File = null;
    photoName: any;
    photoUrl = '';
    showPhoto: any;

    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private router: Router,
                private _formBuilder: FormBuilder,
                private utilityService: UtilityService,
                private propertyService: PropertyService,
                private readingService: ReadingService,
                private notification: NotificationService) {
        this.entryType = 'manual';
        // Load properties list
        this.propertyService.list(['property_name', 'location'])
            .subscribe((res) => this.properties = res,
                () => this.properties = []
            );
    }

    ngOnInit() {
        // Utilities list
        this.utilities$ = this.utilityService.list(['utility_name ', 'utility_display_name']);

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

                    // simulate server fetching and filtering data
                    return this.properties.filter(property => {
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


        // load amenities

        // load utilities

        this.utilitySummaryFormGroup = this._formBuilder.group({
            property_id: ['', [Validators.required]],
            utility_id: ['', [Validators.required]],
        });

        this.autoDataEntryFormGroup = this._formBuilder.group({
            entry_type: [this.entryType]
        });

        this.utilityBillsFormGroup = this._formBuilder.group({
            unitReadings: this.fb.array([ this.utilityBillFieldCreate() ]),
        });
    }

    /**
     * Update supporting fields when property drop down changes content
     * @param value
     */
    onPropertyItemChange(value) {
        this.units = this.properties.find((item: any) => item.id === value).units;
        this.units$ = of(this.properties.find((item: any) => item.id === value).units);
        this.previousReadings$ = of (null);
    }

    onUtilityItemChange(value) {
        this.utilityID = value;
    }


    onCurrentReadingChange(event, i) {
        this.consumption$ = of (event.target.value);
        this.consumptionBank[i] = this.consumption$;
    }

    /**
     * @param value
     * @param i
     */
    onUnitItemChange(value, i) {
        this.previousReadings$ =  this.readingService.previousReading({unit_id: value, utility_id: this.utilityID});
        this.pastReadings[i] = this.previousReadings$;
    }

    getTemplateFileCSV() {
        const property = {...this.utilitySummaryFormGroup.value};
        // console.log(property);

        this.readingService.csvTemplate(property).subscribe((res) => {
                console.log(res);
                this.showFile(res, 'csv');
                },
            (error) => {
            });
    }
    getTemplateFileExcel() {
        const property = {...this.utilitySummaryFormGroup.value};
        // console.log(property);

        this.readingService.excelTemplate(property).subscribe((res) => {
                console.log(res);
                this.showFile(res, 'xlsx');
            },
            (error) => {
            });
    }

    /**
     * @param blob
     * @param fileExtension
     */
    showFile(blob, fileExtension: string) {
        const newBlob = new Blob([blob], {type: 'text/csv'});

        if (window.navigator && window.navigator.msSaveOrOpenBlob) {
            window.navigator.msSaveOrOpenBlob(newBlob);
            return;
        }
        const data = window.URL.createObjectURL(newBlob);
        const link = document.createElement('a');
        link.href = data;
        link.download = 'statement.' + fileExtension;
        link.click();
        setTimeout(function() {
            window.URL.revokeObjectURL(data);
        }, 100);
    }



    /**
     *
     * @param file
     */
    onLogoSelect(file: FileList) {
        if (file.length > 0) {
            this.logoToUpload = file.item(0);
            const reader = new FileReader();
            reader.onload = (event: any) => {
                this.logoUrl = event.target.result;
            };
            reader.readAsDataURL(this.logoToUpload);
        }
    }

    /**
     *
     * @param file
     */
    onReadingTemplateSelect(file: FileList) {
        if (file.length > 0) {
            this.photoToUpload = file.item(0);
            this.photoName = file.item(0).name;
            const reader = new FileReader();
            reader.onload = (event: any) => {
                this.photoUrl = event.target.result;
            };
            reader.readAsDataURL(this.photoToUpload);

            this.loader = true;

            const formData = new FormData();
            formData.append('readings', this.photoToUpload);
            this.uploadPhoto(formData);
        }
    }

    /**
     * Upload profile image to server
     * @param formData
     */
    private uploadPhoto(formData: FormData) {
        this.readingService.uploadReadings(formData)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Readings has been uploaded.');
                },
                (error) => {
                    this.loader = false;
                    if (error.payment === 0) {
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                          /*  if (this.form) {
                               // this.form.controls[prop]?.markAsTouched();
                             //   this.form.controls[prop].setErrors({incorrect: true});
                            }*/
                        }
                    }
                });
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
        return <FormArray>this.utilityBillsFormGroup.get('unitReadings');
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
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitReadings') as FormArray;
        this.utilityBillFields.push(this.utilityBillFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    utilityBillFieldRemove(i): void {
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitReadings') as FormArray;
        this.utilityBillFields.removeAt(i);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    utilityBillFieldCopy(i): void {
        this.utilityBillFields = this.utilityBillsFormGroup.get('unitReadings') as FormArray;
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

        const unitReadings = {...this.utilitySummaryFormGroup.value, ...this.utilityBillsFormGroup.value};

        this.loader = true;

        this.readingService.create(unitReadings).subscribe((data) => {
                this.loader = false;
                this.notification.showNotification('success', 'Success !! Utility Readings Added.');
                this.onSaveComplete();
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

    public onSaveComplete(): void {
        this.router.navigate(['/readings']);
    }
}

