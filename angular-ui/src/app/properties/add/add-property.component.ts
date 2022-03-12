import { Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogConfig, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { PropertyModel } from '../models/property-model';
import { PropertyService } from '../data/property.service';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable, of, ReplaySubject, Subject } from 'rxjs';
import { PropertyUnitDetailsComponent } from './unit-details/property-unit-details.component';
import { debounceTime, delay, distinctUntilChanged, filter, map, takeUntil, tap } from 'rxjs/operators';
import { LandlordService } from '../../landlords/data/landlord.service';
import { ActivatedRoute, Router } from '@angular/router';
import { EXTRA_CHARGE_TYPES } from '../../shared/enums/extra-charge-type-enum';
import { EXTRA_CHARGE__FREQUENCIES } from '../../shared/enums/extra-charge-frequency-enum';
import { ExtraChargeService } from '../../settings/property/extra-charges/data/extra-charge.service';
import { AGENT_COMMISSION_TYPES } from '../../shared/enums/agent-commision-type-enum';
import { PaymentMethodService } from '../../settings/payment/payment-method/data/payment-method.service';
import { UnitTypeService } from '../../settings/property/unit-type/data/unit-type.service';
import { AmenityService } from '../../settings/property/amenity/data/amenity.service';
import { PropertyTypeService } from '../../settings/property/type/data/property-type.service';
import { UtilityService } from '../../settings/property/utility/data/utility.service';
import { CheckboxItem } from './unit-details/check-box-item';
import { LateFeeService } from '../../settings/lease/late-fee/data/late-fee.service';
import { LATE_FEE_FREQUENCIES } from '../../shared/enums/late-fee-frequencies.enum';
import { LATE_FEE_TYPES } from '../../shared/enums/late-fee-types.enum';
import { PropertyExtraDataService } from '../data/property-extra-data.service';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-add-property',
    styles: [],
    templateUrl: './add-property.component.html'
})
export class AddPropertyComponent implements OnInit, OnDestroy  {
    form: FormGroup;
    unitFields: FormArray;
    paymentMethodFields: FormArray;
    extraChargeFields: FormArray;
    lateFeeFields: FormArray;
    utilityFields: FormArray;

    unitValues = [];
    unitTypeDisplayName: string;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    loader = false;

    formGroup: FormGroup;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;

    isLinear = false;
    propertyDetailsFormGroup: FormGroup;
    paymentsFormGroup: FormGroup;
    extraChargesFormGroup: FormGroup;
    utilitiesFormGroup: FormGroup;

    lateFeesFormGroup: FormGroup;

    propertyTypes$: Observable<any>;

    utilities$: Observable<any>;
    paymentMethods$: Observable<any>;
    amenities$: Observable<any>;
    unitTypes$: Observable<any>;

    allAmenitiesOptions = new Array<CheckboxItem>();
    allUtilitiesOptions = new Array<CheckboxItem>();
    amenities: any;
    utilities: any;
    lateFeeTypes: any;
    agentCommissionTypes: any;
    extraChargeTypes: any;
    extraChargeFrequencies: any;
    lateFeeFrequencies: any;
    extraCharges$: any;
    lateFees$: any;
    propertySetting: any;

    /** control for filter for server side. */
    public landlordServerSideFilteringCtrl: FormControl = new FormControl();

    /** indicate search operation is in progress */
    public searching = false;

    /** list of banks filtered after simulating server side search */
    public  filteredServerSideLandlords: ReplaySubject<any> = new ReplaySubject<any>(1);

    /** Subject that emits when the component has been destroyed. */
    protected _onDestroy = new Subject<void>();

    landlordsFiltered$: Observable<any>;

    isAdd = true;
    propertyID: string;
    property: PropertyModel;

    deleteDialogRef: MatDialogRef<ConfirmationDialogComponent>;
    isAdmin$: Observable<boolean>;
    constructor(private fb: FormBuilder,
                private propertyExtraDataService: PropertyExtraDataService,
                private dialog: MatDialog,
                private _formBuilder: FormBuilder,
                private route: ActivatedRoute,
                private landlordService: LandlordService,
                private extraChargeService: ExtraChargeService,
                private lateFeeService: LateFeeService,
                private paymentMethodService: PaymentMethodService,
                private propertyTypeService: PropertyTypeService,
                private router: Router,
                private propertyService: PropertyService,
                private unitTypeService: UnitTypeService,
                private amenityService: AmenityService,
                private utilityService: UtilityService,
                private authenticationService: AuthenticationService,
                private notification: NotificationService) {
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.lateFeeTypes = LATE_FEE_TYPES;
        this.extraChargeTypes = EXTRA_CHARGE_TYPES;
        this.extraChargeFrequencies = EXTRA_CHARGE__FREQUENCIES;
        this.lateFeeFrequencies = LATE_FEE_FREQUENCIES;
        this.agentCommissionTypes = AGENT_COMMISSION_TYPES;
        this.unitTypes$ = this.unitTypeService.list(['unit_type_name ', 'unit_type_display_name ']);

            this.propertyDetailsFormGroup = this._formBuilder.group({
                total_units: [''],
                landlord_id: ['', [Validators.required,
                    Validators.minLength(2)]],
                property_name: ['', [Validators.required,
                    Validators.minLength(2)]],
                location: [''],
                property_code: ['', [Validators.required,
                    Validators.minLength(2)]],
                property_type_id: ['', [Validators.required,
                    Validators.minLength(2)]],
                // @itachi
                plot_area: [''],
                covered_area: ['', [Validators.required,
                    Validators.minLength(2)]],
                dm_circle_rate: ['', [Validators.required,
                    Validators.minLength(2)]],
                // @itachi
                unitFields: this.fb.array([ this.createUnitField() ])
            });

            this.paymentsFormGroup = this._formBuilder.group({
                agent_commission_value: [''],
                agent_commission_type: [''],
                paymentMethodFields: this.fb.array([ this.paymentMethodFieldCreate() ])
            });

            this.extraChargesFormGroup = this._formBuilder.group({
                extraChargeFields: this.fb.array([ this.extraChargeFieldCreate() ]),
            });

            this.lateFeesFormGroup = this._formBuilder.group({
                lateFeeFields: this.fb.array([ this.lateFeeFieldCreate() ]),
            });

            this.utilitiesFormGroup = this._formBuilder.group({
                utilityFields: this.fb.array([ this.utilityFieldCreate() ])
            });
    }

    populatePropertyDetailsForm(property) {
        this.propertyDetailsFormGroup.get('total_units').disable();
        this.propertyDetailsFormGroup.get('landlord_id').disable();

        this.propertyDetailsFormGroup.patchValue({
            total_units: property?.total_units,
            landlord_id: property?.landlord?.first_name + ' ' + property?.landlord?.last_name,
            property_name: property?.property_name,
            location: property?.location,
            property_code: property?.property_code,
            property_type_id: property?.property_type_id,
            // @itachi
            plot_area: property?.plot_area,
            covered_area: property?.covered_area,
            dm_circle_rate: property?.dm_circle_rate,
            // @itachi
        });
    }

    populatePaymentsForm(property) {
        this.paymentsFormGroup.patchValue({
            agent_commission_value: property?.agent_commission_value,
            agent_commission_type: property?.agent_commission_type
        });

        this.paymentMethods$ = of(property?.payment_methods);
        this.paymentMethods$.subscribe(res => {
            this.paymentsFormGroup.setControl('paymentMethodFields',  this.paymentMethodFieldReplaceAll());
        });
    }

    populateExtraChargesForm(property) {
        this.extraCharges$ = of(property?.extra_charges);
        this.extraCharges$.subscribe(res => {
            this.extraChargesFormGroup.setControl('extraChargeFields', this.extraChargeFieldReplaceAll());
        });
    }

    populateLateFeesForm(property) {
        this.lateFees$ = of(property?.late_fees);
        this.lateFees$.subscribe(res => {
            this.lateFeesFormGroup.setControl('lateFeeFields', this.lateFeeFieldReplaceAll());
        });
    }

    populateUtilitiesForm(property) {
        this.utilities$ = of(property?.utility_costs);
        this.utilities$.subscribe(res => {
            this.utilitiesFormGroup.setControl('utilityFields', this.utilityFieldReplaceAll());
        });
    }

    populateForm(property: PropertyModel) {
        this.populatePropertyDetailsForm(property);
        this.populatePaymentsForm(property);
        this.populateExtraChargesForm(property);
        this.populateLateFeesForm(property);
        this.populateUtilitiesForm(property);
    }

    ngOnInit() {
        this.propertyID = this.route.snapshot.paramMap.get('id');
        if (this.propertyID) {
            this.isAdd = false;

            this.propertyService.selectedPropertyChanges$.subscribe(property => {
                if (property) {
                    this.property = property;
                    this.populateForm(property);
                }
                if (!property) {
                    this.propertyService.getById(this.propertyID).subscribe(data => {
                        this.property = data;
                        this.propertyService.changeSelectedProperty(data);
                        this.populateForm(data);
                    });
                }
            });
        }

        this.propertyExtraDataService.fetch().subscribe(res => {
            this.propertySetting = res?.property_settings;
            if (this.isAdd) {
              //  this.prePopulateLeaseSettingForm(this.leaseSetting);
            }
            this.propertyTypes$ = of(res?.property_types);
            this.lateFees$ = of(res?.late_fees);
            this.paymentMethods$ = of(res?.payment_methods);
            this.utilities$ = of(res?.utilities);
            this.extraCharges$ = of(res?.extra_charges);
          //  this.amenities$ = of(res?.amenities);
        });

        // Amenities list
        this.amenities$ = this.amenityService.list(['amenity_name ', 'amenity_display_name ']);
        this.amenities$.subscribe(amenities => {
            this.allAmenitiesOptions = amenities.map(
                x => new CheckboxItem(x.id, x.amenity_display_name));
        });

        // Utility list
     /*   this.utilities$ = this.utilityService.list(['utility_name ', 'utility_display_name ']);
        this.utilities$.subscribe(utilities => {
            this.allUtilitiesOptions = utilities.map(
                x => new CheckboxItem(x.id, x.utility_display_name));
        });*/

        // listen for search field value changes
        this.landlordServerSideFilteringCtrl.valueChanges
            .pipe(
                filter(search => !!search),
                tap(() => this.searching = true),
                takeUntil(this._onDestroy),
                debounceTime(2000),
                distinctUntilChanged(),
                map(search => {
                    search = search.toLowerCase();
                    this.landlordsFiltered$ =  this.landlordService.search(search);
                }),
                delay(500)
            )
            .subscribe(filteredLandlords => {
                    this.searching = false;
                    this.filteredServerSideLandlords.next(filteredLandlords);
                },
                error => {
                    this.searching = false;
                });
    }


    /**
     * Fetch all defined fields
     */
    get utilityFieldsAll () {
        return <FormArray>this.utilitiesFormGroup.get('utilityFields');
    }

    /**
     * Generate fields for a data row
     */
    utilityFieldCreate(data?: any): FormGroup {
        return this.fb.group({
            utility_id: [data?.utility_id],
            utility_unit_cost: [data?.utility_unit_cost],
            utility_base_fee: [data?.utility_base_fee]
        });
    }

    utilityFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.utilities$.subscribe(utilities => {
            utilities.forEach(utility => {
                formArray.push(this.fb.group({
                    utility_id: utility?.id,
                    utility_unit_cost: utility?.pivot?.utility_unit_cost,
                    utility_base_fee: utility?.pivot?.utility_base_fee
                }))
            });
        });
        return formArray;
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    utilityFieldAdd(data?: any): void {
        this.utilityFields = this.utilitiesFormGroup.get('utilityFields') as FormArray;
        this.utilityFields.push(this.utilityFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    utilityFieldRemove(i): void {
        this.utilityFields = this.utilitiesFormGroup.get('utilityFields') as FormArray;
        this.utilityFields.removeAt(i);
        //  this.lateFeeValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    utilityFieldCopy(i): void {
        this.utilityFields = this.utilitiesFormGroup.get('utilityFields') as FormArray;
        const holder = [];
        holder.push(this.utilityFields.value[i])
        this.utilityFieldAdd(...holder);
    }

    /**
     * Fetch all defined fields
     */
    get extraChargeFieldsAll () {
        return <FormArray>this.extraChargesFormGroup.get('extraChargeFields');
    }

    extraChargeFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.extraCharges$.subscribe(extraCharges => {
            extraCharges.forEach(extraCharge => {
                formArray.push(this.fb.group({
                    extra_charge_id: extraCharge?.id,
                    extra_charge_value: extraCharge?.pivot?.extra_charge_value,
                    extra_charge_type: extraCharge?.pivot?.extra_charge_type,
                    extra_charge_frequency: extraCharge?.pivot?.extra_charge_frequency,
                }))
            });
        });
        return formArray;
    }

    /**
     * Generate fields for a data row
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
        this.extraChargeFields = this.extraChargesFormGroup.get('extraChargeFields') as FormArray;
        this.extraChargeFields.push(this.extraChargeFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    extraChargeFieldRemove(i): void {
        this.extraChargeFields = this.extraChargesFormGroup.get('extraChargeFields') as FormArray;
        this.extraChargeFields.removeAt(i);
        //  this.lateFeeValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    extraChargeFieldCopy(i): void {
        this.extraChargeFields = this.extraChargesFormGroup.get('extraChargeFields') as FormArray;
        const holder = [];
        holder.push(this.extraChargeFields.value[i])
        this.extraChargeFieldAdd(...holder);
    }

    /* Start Late fees fields */
    /**
     * Fetch all defined fields
     */
    get lateFeeFieldsAll () {
        return <FormArray>this.lateFeesFormGroup.get('lateFeeFields');
    }

    /**
     * Generate fields for a data row
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

    lateFeeFieldReplaceAll(): FormArray {
        const formArray =  new FormArray([]);
        this.lateFees$.subscribe(lateFees => {
            lateFees.forEach(lateFee => {
                formArray.push(this.fb.group({
                    late_fee_id: lateFee?.id,
                    late_fee_value: lateFee?.pivot?.late_fee_value,
                    late_fee_type: lateFee?.pivot?.late_fee_type,
                    grace_period: lateFee?.pivot?.grace_period,
                    late_fee_frequency: lateFee?.pivot?.late_fee_frequency,
                }))
            });
        });
        return formArray;
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
     * Fetch all defined fields
     */
    get paymentMethodFieldsAll () {
        return <FormArray>this.paymentsFormGroup.get('paymentMethodFields');
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
     * Add an extra data row
     * @param data Default data
     */
    paymentMethodFieldAdd(data?: any): void {
        this.paymentMethodFields = this.paymentsFormGroup.get('paymentMethodFields') as FormArray;
        this.paymentMethodFields.push(this.paymentMethodFieldCreate(data));
    }

    /**
     * remove an existing data row
     */
    paymentMethodFieldRemove(i): void {
        this.paymentMethodFields = this.paymentsFormGroup.get('paymentMethodFields') as FormArray;
        this.paymentMethodFields.removeAt(i);
      //  this.lateFeeValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    paymentMethodFieldCopy(i): void {
        this.paymentMethodFields = this.paymentsFormGroup.get('paymentMethodFields') as FormArray;
        const holder = [];
        holder.push(this.paymentMethodFields.value[i])
        this.paymentMethodFieldAdd(...holder);
    }

    /**
     * Generate fields for a data row
     */
    createUnitField(data?: any): FormGroup {
        return this.fb.group({
            unit_name: [data?.unit_name],
            unit_type_name: [this.unitTypeName(data?.unit_type_id)]
        });
    }

    /**
     * Fetch all defined fields
     */
    get allUnitFields () {
        return <FormArray>this.propertyDetailsFormGroup.get('unitFields');
    }

    /**
     * Add an extra data row
     * @param data Default data
     */
    addUnitField(data?: any): void {
        this.unitFields = this.propertyDetailsFormGroup.get('unitFields') as FormArray;
        this.unitFields.push(this.createUnitField(data));
    }

    /**
     * remove an existing data row
     */
    removeUnitField(i): void {
        this.unitFields = this.propertyDetailsFormGroup.get('unitFields') as FormArray;
        this.unitFields.removeAt(i);
        const item = this.unitValues.splice(i, 1);
    }

    /**
     * Copy an existing data row to a new one
     * Makes an extra data object with an id same as size of the previous data array
     * @param i
     */
    copyUnitField(i): void {

        const unitValue = this.unitValues[i];
        const size = this.unitValues.length;

        const newCopyUnit = {...unitValue};
        newCopyUnit.id = size;
        this.unitValues.push(newCopyUnit);
        this.addUnitField(newCopyUnit);
    }

    /**
     * Gets a unit type name give id
     * @param id
     */
    unitTypeName(id) {
        let result;
        this.unitTypes$.subscribe(unitTypes => {
            result = unitTypes.find((item: any) => item.id === id)?.unit_type_display_name;
            this.unitTypeDisplayName = unitTypes.find((item: any) => item.id === id)?.unit_type_display_name;
        });
        return this.unitTypeDisplayName;
    }


    /**
     * Pop up dialog form to capture unit details. Also Edit existing data.
     * Save data on dialog close
     * Add dialog launch
     */
    addUnitDetails(number) {
        let edit = false;
        const dialogConfig = new MatDialogConfig();
        dialogConfig.disableClose = true;
        dialogConfig.autoFocus = true;

        const unitValue = this.unitValues[number];

        if (typeof unitValue !== 'undefined') {
            edit = true;
        }

        dialogConfig.data = {unitValue,
            utilities: this.utilities$,
            amenities$: this.amenities$,
            unitTypes$: this.unitTypes$,
            amenitiesData: this.amenities,
            amenityOptions: this.allAmenitiesOptions,
            utilityOptions: this.allUtilitiesOptions,
        };

        const dialogRef = this.dialog.open(PropertyUnitDetailsComponent, dialogConfig);

        dialogRef.afterClosed().subscribe(result => {
            if ((result)) {

                const resultData = result.data;
                resultData.id = number;

                if (edit === true) {
                    const elementsIndex = this.unitValues.findIndex(element => element.id === number );

                    const newArray = [...this.unitValues];

                    newArray.splice(elementsIndex, 1, resultData);

                    this.unitValues = newArray.slice();

                    this.unitFields = this.propertyDetailsFormGroup.get('unitFields') as FormArray;
                    this.unitFields.at(number).patchValue({
                        unit_name: result.data.unit_name,
                        unit_type_name: this.unitTypeName(result.data.unit_type_id),
                    });
                } else {
                    this.unitValues.push(resultData);

                    this.unitFields = this.propertyDetailsFormGroup.get('unitFields') as FormArray;
                    this.unitFields.at(number).patchValue({
                        unit_name: result.data.unit_name,
                        unit_type_name: this.unitTypeName(result.data.unit_type_id),
                    });
                }
            }
        });
    }

    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

     create() {
        this.errorInForm.next(false);
        const data = {...this.propertyDetailsFormGroup.value, ...this.paymentsFormGroup.value,
            ...this.extraChargesFormGroup.value, ...this.utilitiesFormGroup.value, ...this.lateFeesFormGroup.value};

        const body = Object.assign({}, this.property, data);
        body.units = this.unitValues;
        this.loader = true;
        this.propertyService.create(body)
            .subscribe((res) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! New Property created.');
                    this.onSaveComplete();
                },
                (error) => {
                    this.loader = false;
                    if (error.lead === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
                        return;
                    }
                    this.formErrors = error;

                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            this.stepper.selectedIndex = 0;

                            if (this.utilitiesFormGroup.controls[prop]) {
                                this.utilitiesFormGroup.controls[prop]?.markAsTouched();
                                this.utilitiesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.lateFeesFormGroup.controls[prop]) {
                                this.lateFeesFormGroup.controls[prop]?.markAsTouched();
                                this.lateFeesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.extraChargesFormGroup.controls[prop]) {
                                this.extraChargesFormGroup.controls[prop]?.markAsTouched();
                                this.extraChargesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.paymentsFormGroup.controls[prop]) {
                                this.paymentsFormGroup.controls[prop]?.markAsTouched();
                                this.paymentsFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.propertyDetailsFormGroup.controls[prop]) {
                                this.propertyDetailsFormGroup.controls[prop]?.markAsTouched();
                                this.propertyDetailsFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }

                });
    }

    update() {
        const data = {...this.propertyDetailsFormGroup.value, ...this.paymentsFormGroup.value,
            ...this.extraChargesFormGroup.value, ...this.utilitiesFormGroup.value, ...this.lateFeesFormGroup.value};
        const body = Object.assign({}, this.property, data);
        this.loader = true;
        this.errorInForm.next(false);

        this.propertyService.update(body)
            .subscribe((res) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Property has been updated.');
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
                            if (this.utilitiesFormGroup.controls[prop]) {
                                this.utilitiesFormGroup.controls[prop]?.markAsTouched();
                                this.utilitiesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.lateFeesFormGroup.controls[prop]) {
                                this.lateFeesFormGroup.controls[prop]?.markAsTouched();
                                this.lateFeesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.extraChargesFormGroup.controls[prop]) {
                                this.extraChargesFormGroup.controls[prop]?.markAsTouched();
                                this.extraChargesFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.paymentsFormGroup.controls[prop]) {
                                this.paymentsFormGroup.controls[prop]?.markAsTouched();
                                this.paymentsFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                            if (this.propertyDetailsFormGroup.controls[prop]) {
                                this.propertyDetailsFormGroup.controls[prop]?.markAsTouched();
                                this.propertyDetailsFormGroup.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }

    ngOnDestroy() {
        this._onDestroy.next();
        this._onDestroy.complete();
    }

    onPropertySelected() {
        this.propertyService.changeSelectedProperty(this.property);
    }

    public onSaveComplete(): void {
        this.loader = false;
        this.router.navigate(['/properties']);
    }

    openConfirmationDialog(property: PropertyModel) {
        this.deleteDialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.deleteDialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(property);
            }
            this.deleteDialogRef = null;
        });
    }

    private delete(property: PropertyModel) {
        this.loader = true;
        this.propertyService.delete(property)
            .subscribe((data) => {
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Property has been deleted.');
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

