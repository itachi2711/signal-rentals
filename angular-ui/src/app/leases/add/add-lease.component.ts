import { AfterViewInit, Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { LeaseModel } from '../models/lease-model';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { debounceTime, delay, distinctUntilChanged, filter, map, take, takeUntil, tap } from 'rxjs/operators';
import { MatSelect } from '@angular/material/select';
import { PropertyService } from '../../properties/data/property.service';
import { EXTRA_CHARGE_TYPES } from '../../shared/enums/extra-charge-type-enum';
import { EXTRA_CHARGE__FREQUENCIES } from '../../shared/enums/extra-charge-frequency-enum';
import { PaymentMethodService } from '../../settings/payment/payment-method/data/payment-method.service';
import { ActivatedRoute, Router } from '@angular/router';
import { BILLING_FREQUENCIES } from '../../shared/enums/billing-frequency-enum';
import { LeaseService } from '../data/lease.service';
import { LeaseTypeService } from '../../settings/lease/lease-type/data/lease-type.service';
import { UtilityService } from '../../settings/property/utility/data/utility.service';
import { TenantService } from '../../tenants/data/tenant.service';
import { LeaseSettingService } from '../../settings/lease/general/data/lease-setting.service';
import { LeaseGeneralSettingModel } from '../../settings/lease/general/model/lease-general-setting.model';
import { LATE_FEE_FREQUENCIES } from '../../shared/enums/late-fee-frequencies.enum';
import { LATE_FEE_TYPES } from '../../shared/enums/late-fee-types.enum';
import { LeaseExtraDataService } from '../data/lease-extra-data.service';
import { PropertyModel } from '../../properties/models/property-model';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-add-lease',
    styles: [],
    templateUrl: './add-lease.component.html'
})
export class AddLeaseComponent implements OnInit, AfterViewInit, OnDestroy  {

    unitFields: FormArray;
    utilityDeposits: FormArray;

    unitValues = [];

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;
    formGroup: FormGroup;
    tenant: LeaseModel;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;

    isLinear = false;

    leaseDetailsFormGroup: FormGroup;
    depositsFormGroup: FormGroup;
    tenantsFormGroup: FormGroup;
    extraChargesFormGroup: FormGroup;
    lateFeesFormGroup: FormGroup;
    utilityChargesFormGroup: FormGroup;
    paymentMethodsFormGroup: FormGroup;
    leaseSettingsFormGroup: FormGroup;

    extraChargeTypes: any;
    lateFeeTypes: any;
    extraChargeFrequencies: any;
    extraCharges$: Observable<any>;

    billingFrequencies: any;
    details = 'noooone';

  //  leaseModes$: Observable<any>;
    leaseTypes$: Observable<any>;

    leaseSettings$: Observable<any>;
    leaseSetting: any;
    utilities$: Observable<any>;
    amenities$: Observable<any>;
    amenities: any;
    utilities: any;
    progress = 0;

    public newTenant: string;

    properties: any = [];
    tenants: any = [];
    units: any = [];
    lateFeeFrequencies: any;
    selectedProperty: any;
    propertyID: string;
    landlordID: string;

    extraCharges: FormArray;
    lateFeeFields: FormArray;

    paymentMethodFields: FormArray;
    utilityCharges: FormArray;

    lateFees$: any;
    utilityCharges$ = of([]);
    paymentMethods$: Observable<any>;
    utilityDeposits$: Observable<any>;

    isAdd = true;
    lease: LeaseModel;
    leaseID: string;

    dueON = Array.from({length: (29 - 1)}, (v, k) => k + 1);

    /** control for filter for server side. */
    public propertyServerSideFilteringCtrl: FormControl = new FormControl();
    /** list of tenants filtered after simulating server side search */
    public  filteredServerSideProperties: ReplaySubject<any> = new ReplaySubject<any>(1);


    /** control for filter for server side. */
    public tenantServerSideFilteringCtrl: FormControl = new FormControl();
    /** list of tenants filtered after simulating server side search */
    public  filteredServerSideTenants: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** indicate search operation is in progress */
    public searching = false;

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    /** control for the MatSelect filter keyword multi-selection */
    public tenantMultiFilterCtrl: FormControl = new FormControl();

    /** list of tenants filtered by search keyword */
    public filteredTenantsMulti: ReplaySubject<any> = new ReplaySubject<any>(1);

    @ViewChild('tenantSelect', { static: true }) tenantSelect: MatSelect;

    /** control for the MatSelect filter keyword multi-selection */
    public unitMultiFilterCtrl: FormControl = new FormControl();

    /** list of units filtered by search keyword */
    public filteredUnitsMulti: ReplaySubject<any> = new ReplaySubject<any>(1);
    deleteDialogRef: MatDialogRef<ConfirmationDialogComponent>;
    isAdmin$: Observable<boolean>;

    propertiesFiltered$: Observable<any>;
    @ViewChild('unitSelect', { static: true }) unitSelect: MatSelect;

    /**
     * fetch tenant fields
     */
    get tenantsCtrl() {
        return <FormControl>this.tenantsFormGroup.get('tenants');
    }

    /**
     * fetch tenant fields
     */
    get unitsCtrl() {
        return <FormControl>this.leaseDetailsFormGroup.get('units');
    }
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private leaseExtraDataService: LeaseExtraDataService,
                private route: ActivatedRoute,
                private router: Router,
                private _formBuilder: FormBuilder,
                private propertyService: PropertyService,
                private tenantService: TenantService,
                private paymentMethodService: PaymentMethodService,
                private leaseService: LeaseService,
                private leaseTypeService: LeaseTypeService,
                private utilityService: UtilityService,
                private leaseSettingService: LeaseSettingService,
                private authenticationService: AuthenticationService,
                private notification: NotificationService) {
        this.lateFeeFrequencies = LATE_FEE_FREQUENCIES;
        this.newTenant = 'new';
        this.extraChargeTypes = EXTRA_CHARGE_TYPES;
        this.lateFeeTypes = LATE_FEE_TYPES;
        this.extraChargeFrequencies = EXTRA_CHARGE__FREQUENCIES;
        this.billingFrequencies = BILLING_FREQUENCIES;
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.leaseDetailsFormGroup = this._formBuilder.group({
            lease_type_id: [''],
            property: [''],
            units: [[]],
            start_date: [(new Date()).toISOString().substring(0, 10), [Validators.required]],
            due_date: [''],
            rent_amount: [''],
            due_on: [5]
        });

        this.depositsFormGroup = this._formBuilder.group({
            rent_deposit: [''],
            utilityDeposits: this.fb.array([ this.utilityDepositFieldCreate() ]),
        });

        this.tenantsFormGroup = this._formBuilder.group({
            tenants: [[], Validators.required],
        });

        this.extraChargesFormGroup = this._formBuilder.group({
            extraCharges: this.fb.array([ this.extraChargeFieldCreate() ])
        });

        this.lateFeesFormGroup = this._formBuilder.group({
            lateFeeFields: this.fb.array([ this.lateFeeFieldCreate() ])
        });

        this.utilityChargesFormGroup = this._formBuilder.group({
            utilityCharges: this.fb.array([ this.utilityChargeCreate() ])
        });

        this.paymentMethodsFormGroup = this._formBuilder.group({
            paymentMethodFields: this.fb.array([ this.paymentMethodFieldCreate() ])
        });

        this.leaseSettingsFormGroup = this._formBuilder.group({
            generate_invoice_on: [''],
            next_period_billing: [''],
            skip_starting_period: [''],
            waive_penalty: [''],
        });
    }

    /**
     * Sets the initial value after the filteredTenants are loaded initially
     */
    protected setTenantInitialValue() {
        this.filteredTenantsMulti
            .pipe(take(1), takeUntil(this._onDestroy))
            .subscribe(() => {
            });
    }

    protected filterTenantsMulti() {
        if (!this.tenants) {
            return;
        }
        // get the search keyword
        let search = this.tenantMultiFilterCtrl.value;
        if (!search) {
            this.filteredTenantsMulti.next(this.tenants.slice());
            return;
        } else {
            search = search.toLowerCase();
        }
        // filter the tenants
        this.filteredTenantsMulti.next(
            this.tenants.filter(tenant => tenant.first_name.toLowerCase().indexOf(search) > -1)
        );
    }

    /**
     * Sets the initial value after the filteredUnits are loaded initially
     */
    protected setUnitInitialValue() {
        this.filteredUnitsMulti
            .pipe(take(1), takeUntil(this._onDestroy))
            .subscribe(() => {
            });
    }

    protected filterUnitsMulti() {
        if (!this.units) {
            return;
        }
        // get the search keyword
        let search = this.unitMultiFilterCtrl.value;
        if (!search) {
            this.filteredUnitsMulti.next(this.units.slice());
            return;
        } else {
            search = search.toLowerCase();
        }
        // filter the units
        this.filteredUnitsMulti.next(
            this.units.filter(unit => unit?.unit_name?.toLowerCase().indexOf(search) > -1)
        );
    }

    private populateForm(lease: LeaseModel) {
        this.leaseDetailsFormGroup.get('property').disable();
        this.leaseDetailsFormGroup.get('units').disable();
        this.leaseDetailsFormGroup.get('start_date').disable();

        this.leaseDetailsFormGroup.patchValue({
            property: lease?.property?.property_name + ' (' + lease?.property?.property_code + ')',
            units: lease?.unit_names,
            lease_type_id: lease?.lease_type_id,
            start_date: lease?.start_date,
            rent_amount: lease?.rent_amount,
            due_on: lease?.due_on,
        });

        this.depositsFormGroup.get('rent_deposit').disable();
        this.depositsFormGroup.patchValue({
            rent_deposit: lease?.rent_deposit
        });

        this.tenantsFormGroup.get('tenants').disable();
        this.tenantsFormGroup.patchValue({
            tenants: [lease?.tenant_names]
        });

        this.populateUtilityDeposits(lease);
        this.populateExtraCharges(lease);
        this.populateLateFees(lease);
        this.populateUtilityCharges(lease);
        this.populatePaymentMethods(lease);

        this.leaseSettingsFormGroup.get('skip_starting_period').disable();
        this.leaseSettingsFormGroup.patchValue({
            generate_invoice_on: lease?.generate_invoice_on,
            next_period_billing: lease?.next_period_billing,
            skip_starting_period: lease?.skip_starting_period,
            waive_penalty: lease?.waive_penalty,
        });
    }
    ngOnInit() {
        this.leaseID = this.route.snapshot.paramMap.get('id');
        if (this.leaseID) {
            this.isAdd = false;
            this.leaseService.selectedLeaseChanges$.subscribe(lease => {
                if (lease) {
                    this.lease = lease;
                    this.populateForm(lease);
                }
                if (!lease) {
                    this.leaseService.getById(this.leaseID).subscribe(data => {
                        this.lease = data;
                        this.leaseService.changeSelectedLease(data);
                        this.populateForm(data);
                    });
                }
            });
        }

        this.leaseExtraDataService.fetch().subscribe(res => {
            this.leaseSetting = res?.lease_settings;
            if (this.isAdd) {
                this.prePopulateLeaseSettingForm(this.leaseSetting);
            }
            this.leaseTypes$ = of(res?.lease_types);
            this.lateFees$ = of(res?.late_fees);
            this.paymentMethods$ = of(res?.payment_methods);
            this.utilities$ = of(res?.utilities);
            this.extraCharges$ = of(res?.extra_charges);
        });

        // Tenants list
        this.tenantService.list(['first_name', 'middle_name', 'last_name'])
            .subscribe((res) => this.tenants = res,
                () => this.tenants = []
            );

        // load the initial tenant list
        this.filteredTenantsMulti.next(this.tenants.slice());

        // listen for search field value changes
        this.tenantMultiFilterCtrl.valueChanges
            .pipe(takeUntil(this._onDestroy))
            .subscribe(() => {
                this.filterTenantsMulti();
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
     *
     * @param setting
     */
    prePopulateLeaseSettingForm(setting: LeaseGeneralSettingModel) {
        this.leaseSettingsFormGroup.patchValue({
            generate_invoice_on: setting?.generate_invoice_on,
            next_period_billing: setting?.next_period_billing,
            skip_starting_period: setting?.skip_starting_period,
            waive_penalty: setting?.waive_penalty
        });
    }

    ngAfterViewInit() {
        this.setTenantInitialValue();
       // this.setUnitInitialValue();
    }
    ngOnDestroy() {
        this._onDestroy.next();
        this._onDestroy.complete();
    }

    populateUtilityDeposits(lease) {
        this.utilityDeposits$ = of(lease?.utility_deposits);
        this.depositsFormGroup.setControl('utilityDeposits', this.utilityDepositFieldReplaceAll());
        this.depositsFormGroup.get('utilityDeposits')['controls']
            .forEach(control => {
                control.disable();
            });
    }
    populateExtraCharges(lease) {
        this.extraCharges$ = of(lease?.extra_charges);
        this.extraCharges$.subscribe(res => {
            this.extraChargesFormGroup.setControl('extraCharges', this.extraChargeFieldReplaceAll());
        });
    }
    populateLateFees(lease) {
        this.lateFees$ = of(lease?.late_fees);
        this.lateFees$.subscribe(res => {
            this.lateFeesFormGroup.setControl('lateFeeFields', this.lateFeeFieldReplaceAll());
        });
    }

    populateUtilityCharges(lease) {
        this.utilityCharges$ = of(lease?.utility_charges);
        this.utilityCharges$.subscribe(res => {
            this.utilityChargesFormGroup.setControl('utilityCharges', this.utilityChargeReplaceAll());
        });
    }

    populatePaymentMethods(lease) {
        this.paymentMethods$ = of(lease?.payment_methods);
        this.paymentMethods$.subscribe(res => {
            this.paymentMethodsFormGroup.setControl('paymentMethodFields', this.paymentMethodFieldReplaceAll());
        });
    }

    /**
     * Update supporting fields when property drop down changes content
     * @param property
     */
    onPropertyItemChange(property: PropertyModel) {
        this.selectedProperty = property;
        this.propertyID = property?.id;
        this.landlordID = property?.landlord_id;
        this.units = property?.vacant_units;

        // load the initial tenant list
        this.filteredUnitsMulti.next(this.units.slice());

        // listen for search field value changes
        this.unitMultiFilterCtrl.valueChanges
            .pipe(takeUntil(this._onDestroy))
            .subscribe(() => {
                this.filterUnitsMulti();
            });

        if (Array.isArray(property?.extra_charges) && property?.extra_charges?.length !== 0) {
               this.extraCharges$ = of(property?.extra_charges);
                this.extraCharges$.subscribe(charges => {
                    this.extraChargesFormGroup.setControl('extraCharges', this.extraChargeFieldReplaceAll());
                });
        }

        if (Array.isArray(property?.late_fees) && property?.late_fees?.length !== 0) {
            this.lateFees$ = of(property?.late_fees);
            this.lateFees$.subscribe(fees => {
                this.lateFeesFormGroup.setControl('lateFeeFields', this.lateFeeFieldReplaceAll());
            });
        }

        if (Array.isArray(property?.utility_costs) && property?.utility_costs?.length !== 0) {
            this.utilityCharges$ = of(property?.utility_costs);
            this.utilityCharges$.subscribe(utilities => {
                this.utilityChargesFormGroup.setControl('utilityCharges', this.utilityChargeReplaceAll());
            });
        }

        if (Array.isArray(property?.payment_methods) && property?.payment_methods?.length !== 0) {
            this.paymentMethods$ = of(property?.payment_methods);
            this.paymentMethods$.subscribe(paymentMethods => {
                this.paymentMethodsFormGroup.setControl('paymentMethodFields', this.paymentMethodFieldReplaceAll());
            });
        }
    }

    /*Start extra charge section*/
    /**
     * Fetch all defined fields
     */
    get extraChargeFieldAll () {
        return <FormArray>this.extraChargesFormGroup.get('extraCharges');
    }

    /**
     * Replace  fields for those filled with data from selected property
     */
    extraChargeFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.extraCharges$.subscribe(charges => {
            charges.forEach(charge => {
                formArray.push(this.fb.group({
                    extra_charge_id: charge?.id,
                    extra_charge_value: charge?.pivot?.extra_charge_value,
                    extra_charge_type: charge?.pivot?.extra_charge_type,
                    extra_charge_frequency: charge?.pivot?.extra_charge_frequency
                }))
            });
        });
        return formArray;
    }

    /**
     * Initial field creation
     * @param data
     */
    extraChargeFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            extra_charge_id: [data?.extra_charge_id],
            extra_charge_value: [data?.extra_charge_value],
            extra_charge_type: [data?.extra_charge_type],
            extra_charge_frequency: [data?.extra_charge_frequency]
        });
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    extraChargeFieldAdd(data?: any): void {
        this.extraCharges = this.extraChargesFormGroup.get('extraCharges') as FormArray;
        this.extraCharges.push(this.extraChargeFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    extraChargeFieldRemove(i): void {
        this.extraCharges = this.extraChargesFormGroup.get('extraCharges') as FormArray;
        this.extraCharges.removeAt(i);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    extraChargeFieldCopy(i): void {
        this.extraCharges = this.extraChargesFormGroup.get('extraCharges') as FormArray;
        const holder = [];
        holder.push(this.extraCharges.value[i])
        this.extraChargeFieldAdd(...holder);
    }
    /* End extra charge section*/

    /*Start late fee section*/
    /**
     * Fetch all defined fields
     */
    get lateFeeFieldAll () {
        return <FormArray>this.lateFeesFormGroup.get('lateFeeFields');
    }

    /**
     * Replace  fields for those filled with data from selected property
     */
    lateFeeFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.lateFees$.subscribe(fees => {
            fees.forEach(fee => {
                formArray.push(this.fb.group({
                    late_fee_id: fee?.id,
                    late_fee_value: fee?.pivot?.late_fee_value,
                    late_fee_type: fee?.pivot?.late_fee_type,
                    late_fee_frequency: fee?.pivot?.late_fee_frequency,
                    grace_period: fee?.pivot?.grace_period,
                }))
            });
        });
        return formArray;
    }

    /**
     * Initial field creation
     * @param data
     */
    lateFeeFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            late_fee_id: [data?.late_fee_id],
            late_fee_value: [data?.late_fee_value],
            late_fee_type: [data?.late_fee_type],
            grace_period: [data?.grace_period],
            late_fee_frequency: [data?.late_fee_frequency]
        });
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    lateFeeFieldAdd(data?: any): void {
        this.lateFeeFields = this.lateFeesFormGroup.get('lateFeeFields') as FormArray;
        this.lateFeeFields.push(this.lateFeeFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    lateFeeFieldRemove(i): void {
        this.lateFeeFields = this.lateFeesFormGroup.get('lateFeeFields') as FormArray;
        this.lateFeeFields.removeAt(i);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    lateFeeFieldCopy(i): void {
        this.lateFeeFields = this.lateFeesFormGroup.get('lateFeeFields') as FormArray;
        const holder = [];
        holder.push(this.lateFeeFields.value[i])
        this.lateFeeFieldAdd(...holder);
    }
    /* End late fee section*/

    /* Start utility charge section*/
    /**
     * Fetch all defined fields
     */
    get utilityChargesAll () {
        return <FormArray>this.utilityChargesFormGroup.get('utilityCharges');
    }

    /**
     * Generate fields for a data row
     */
    utilityChargeCreate(data?: any): FormGroup {
        return this.fb.group({
            utility_id: [data?.utility_id],
            utility_unit_cost: [data?.utility_unit_cost],
            utility_base_fee: [data?.utility_base_fee],
        });
    }

    /**
     * Replace  fields for those filled with data from selected property
     */
    utilityChargeReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.utilityCharges$.subscribe(charges => {
            charges.forEach(charge => {
                formArray.push(this.fb.group({
                    utility_id: charge?.id,
                    utility_unit_cost: charge?.pivot?.utility_unit_cost,
                    utility_base_fee: charge?.pivot?.utility_base_fee,
                }))
            });
        });
        return formArray;
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    utilityChargeAdd(data?: any): void {
        this.utilityCharges = this.utilityChargesFormGroup.get('utilityCharges') as FormArray;
        this.utilityCharges.push(this.utilityChargeCreate(data));
    }

    /**
     * remove an existing data row
     */
    utilityChargeRemove(i): void {
        this.utilityCharges = this.utilityChargesFormGroup.get('utilityCharges') as FormArray;
        this.utilityCharges.removeAt(i);
        //  this.lateFeeValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    utilityChargeCopy(i): void {
        this.utilityCharges = this.utilityChargesFormGroup.get('utilityCharges') as FormArray;
        const holder = [];
        holder.push(this.utilityCharges.value[i])
        this.utilityChargeAdd(...holder);
    }
    /* End utility charge section*/

    /* Start payment methods section*/
    /**
     * Fetch all defined fields
     */
    get paymentMethodFieldsAll () {
        return <FormArray>this.paymentMethodsFormGroup.get('paymentMethodFields');
    }

    /**
     * Generate fields for a data row
     */
    paymentMethodFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            payment_method_id: [data?.payment_method_id],
            payment_method_description: [data?.payment_method_description]
        });
    }

    /**
     * Replace  fields for those filled with data from selected property
     */
    paymentMethodFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.paymentMethods$.subscribe(paymentMethods => {
            paymentMethods.forEach(paymentMethod => {
                formArray.push(this.fb.group({
                    payment_method_id: paymentMethod?.id,
                    payment_method_description: paymentMethod?.payment_method_description,
                }))
            });
        });
        return formArray;
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    paymentMethodFieldAdd(data?: any): void {
        this.paymentMethodFields = this.paymentMethodsFormGroup.get('paymentMethodFields') as FormArray;
        this.paymentMethodFields.push(this.paymentMethodFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    paymentMethodFieldRemove(i): void {
        this.paymentMethodFields = this.paymentMethodsFormGroup.get('paymentMethodFields') as FormArray;
        this.paymentMethodFields.removeAt(i);
        //  this.lateFeeValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    paymentMethodFieldCopy(i): void {
        this.paymentMethodFields = this.paymentMethodsFormGroup.get('paymentMethodFields') as FormArray;
        const holder = [];
        holder.push(this.paymentMethodFields.value[i])
        this.paymentMethodFieldAdd(...holder);
    }
    /* End payment methods section*/

    /* Start utility deposit section*/
    get utilityDepositFieldsAll() {
        return <FormArray>this.depositsFormGroup.get('utilityDeposits');
    }

    /**
     * Generate fields for a data row
     */
    utilityDepositFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            utility_id: [data?.utility_id],
            deposit_amount: [data?.deposit_amount]
        });
    }

    utilityDepositFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.utilityDeposits$.subscribe(deposits => {
            deposits.forEach(deposit => {
                formArray.push(this.fb.group({
                    utility_id: deposit?.id,
                    deposit_amount: deposit?.pivot?.deposit_amount,
                }))
            });
        });
        return formArray;
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    utilityDepositFieldAdd(data?: any): void {
        this.utilityDeposits = this.depositsFormGroup.get('utilityDeposits') as FormArray;
        this.utilityDeposits.push(this.utilityDepositFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    utilityDepositFieldRemove(i): void {
        this.unitFields = this.depositsFormGroup.get('utilityDeposits') as FormArray;
        this.unitFields.removeAt(i);
        const item = this.unitValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    utilityDepositFieldCopy(i): void {
        this.utilityDeposits = this.depositsFormGroup.get('utilityDeposits') as FormArray;
        const holder = [];
        holder.push(this.utilityDeposits.value[i])
        this.utilityDepositFieldAdd(...holder);
    }
    /* End utility deposit section*/

    /**
     * For mat-button-toggle-group to select either commercial or residential property unit
     * @param val
     */
    public onToggleChange(val: string) {
        this.newTenant = val;
    }

    /**
     * Generate fields for a data row
     */
    createUnitField(data?: any): FormGroup {
        return this.fb.group({
            unit_name: [data?.unit_name]
        });
    }

    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);
        const lease = {
            ...this.leaseDetailsFormGroup.value,
            ...this.depositsFormGroup.value,
            ...this.tenantsFormGroup.value,
            ...this.extraChargesFormGroup.value,
            ...this.lateFeesFormGroup.value,
            ...this.utilityChargesFormGroup.value,
            ...this.paymentMethodsFormGroup.value,
            ...this.leaseSettingsFormGroup.value
        };
        const body = Object.assign({}, this.lease, lease);
        body.property_id = this.propertyID;
        body.landlord_id = this.landlordID;
        this.loader = true;
        this.leaseService.create(body).subscribe((data) => {
                this.loader = false;
                this.notification.showNotification('success', 'Success !! Lease created.');
                this.onSaveComplete();
            },
            (error) => {
                this.errorInForm.next(true);

                this.loader = false;
                if (error.lease === 0) {
                    this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                        ' Check your connection and retry.');
                    return;
                }
                this.formErrors = error?.error;

                if (this.formErrors) {
                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            if (this.formErrors.hasOwnProperty(prop)) {
                                this.stepper.selectedIndex = 0;

                                if (this.leaseDetailsFormGroup.controls[prop]) {
                                    this.leaseDetailsFormGroup.controls[prop]?.markAsTouched();
                                    this.leaseDetailsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.depositsFormGroup.controls[prop]) {
                                    this.depositsFormGroup.controls[prop]?.markAsTouched();
                                    this.depositsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.tenantsFormGroup.controls[prop]) {
                                    this.tenantsFormGroup.controls[prop]?.markAsTouched();
                                    this.tenantsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.extraChargesFormGroup.controls[prop]) {
                                    this.extraChargesFormGroup.controls[prop]?.markAsTouched();
                                    this.extraChargesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.lateFeesFormGroup.controls[prop]) {
                                    this.lateFeesFormGroup.controls[prop]?.markAsTouched();
                                    this.lateFeesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.utilityChargesFormGroup.controls[prop]) {
                                    this.utilityChargesFormGroup.controls[prop]?.markAsTouched();
                                    this.utilityChargesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.paymentMethodsFormGroup.controls[prop]) {
                                    this.paymentMethodsFormGroup.controls[prop]?.markAsTouched();
                                    this.paymentMethodsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                            }
                        }
                    }
                }

            });
    }

    update() {
        const lease = {
            ...this.leaseDetailsFormGroup.value,
            ...this.depositsFormGroup.value,
            ...this.tenantsFormGroup.value,
            ...this.extraChargesFormGroup.value,
            ...this.lateFeesFormGroup.value,
            ...this.utilityChargesFormGroup.value,
            ...this.paymentMethodsFormGroup.value,
            ...this.leaseSettingsFormGroup.value
        };
        const body = Object.assign({}, this.lease, lease);
        body.property_id = this.propertyID;
        body.landlord_id = this.landlordID;
       /* delete body.property;
        delete body.units;
        delete body.utility_deposits;
        delete body.late_fees;
        delete body.extra_charges;
        delete body.payment_methods;
        delete body.utility_charges;
        delete body.status;
        delete body.utilityDeposits;
        delete body.tenants;*/

        this.loader = true;
        this.errorInForm.next(false);

        this.leaseService.update(body)
            .subscribe((res) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Lease has been updated.');
                    this.onSaveComplete();
                },
                (error) => {
                    this.loader = false;
                    if (error.landlord === 0) {
                        return;
                    }
                    this.formErrors = error;

                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            if (this.formErrors.hasOwnProperty(prop)) {
                                this.stepper.selectedIndex = 0;
                                if (this.leaseDetailsFormGroup.controls[prop]) {
                                    this.leaseDetailsFormGroup.controls[prop]?.markAsTouched();
                                    this.leaseDetailsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.depositsFormGroup.controls[prop]) {
                                    this.depositsFormGroup.controls[prop]?.markAsTouched();
                                    this.depositsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.tenantsFormGroup.controls[prop]) {
                                    this.tenantsFormGroup.controls[prop]?.markAsTouched();
                                    this.tenantsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.extraChargesFormGroup.controls[prop]) {
                                    this.extraChargesFormGroup.controls[prop]?.markAsTouched();
                                    this.extraChargesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.lateFeesFormGroup.controls[prop]) {
                                    this.lateFeesFormGroup.controls[prop]?.markAsTouched();
                                    this.lateFeesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.utilityChargesFormGroup.controls[prop]) {
                                    this.utilityChargesFormGroup.controls[prop]?.markAsTouched();
                                    this.utilityChargesFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                                if (this.paymentMethodsFormGroup.controls[prop]) {
                                    this.paymentMethodsFormGroup.controls[prop]?.markAsTouched();
                                    this.paymentMethodsFormGroup.controls[prop].setErrors({incorrect: true});
                                }
                            }
                        }
                    }
                });
    }

    public onSaveComplete(): void {
        this.loader = false;
        this.router.navigate(['/leases']);
    }

    openConfirmationDialog(lease: LeaseModel) {
        this.deleteDialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.deleteDialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(lease);
            }
            this.deleteDialogRef = null;
        });
    }

    private delete(lease: LeaseModel) {
        this.loader = true;
        this.leaseService.delete(lease)
            .subscribe((data) => {
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Lease has been deleted.');
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
}
