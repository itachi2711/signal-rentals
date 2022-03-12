import { AfterViewInit, Component, EventEmitter, Inject, OnInit, Output, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

import { BehaviorSubject, Observable } from 'rxjs';
import { CheckboxItem } from '../../shared/check-box-item';
import { BILLING_FREQUENCIES } from '../../shared/enums/billing-frequency-enum';
import { UnitModel } from '../model/unit-model';
import { NotificationService } from '../../shared/notification.service';
import { UnitService } from '../data/unit.service';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-add-unit',
    templateUrl: './add-unit.component.html',
    styleUrls: ['./add-unit.component.scss']
})
export class AddUnitComponent implements OnInit, AfterViewInit  {

    form: FormGroup;
    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();
    loader = false;

    formGroup: FormGroup;
    unitValue: any;

    public selectedUnitType: string;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;

    panelOpenState = false;
    disableAnimation = true;

    billingFrequencies: any;

    utilities$: any;
    amenities$: any;
    unitTypes$: any;
    unit: UnitModel;
    isAdd: boolean;
    unitID: string;
    propertyID: string;

    amenities = Array<CheckboxItem>();
    optionsAmenity = Array<CheckboxItem>();
    optionsUtility = Array<CheckboxItem>();
    selectedValuesAmenity: string[];
    selectedValuesUtility: string[];
    @Output() toggle = new EventEmitter<any[]>();
    isAdmin$: Observable<boolean>;
    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private notification: NotificationService,
                private unitService: UnitService,
                private authenticationService: AuthenticationService,
                private dialogRef: MatDialogRef<AddUnitComponent>) {
        this.isAdmin$ = this.authenticationService.isAdmin();
            this.unitValue = row.unit;
            this.utilities$ = row.utilities;
            this.amenities$ = row.amenities$;
            this.unitTypes$ = row.unitTypes$;
            this.amenities = row.amenitiesData;
            this.optionsAmenity = row.amenityOptions;
            this.optionsUtility = row.utilityOptions;
            this.isAdd = row.isAdd;
            this.unitID = row?.unit?.id;
            this.propertyID = row?.propertyID;
            this.billingFrequencies = BILLING_FREQUENCIES;
    }

    ngOnInit() {
        this.selectedValuesAmenity = this.unitValue?.selected_amenities ? this.unitValue?.selected_amenities : [];
        this.selectedValuesUtility = this.unitValue?.selected_utilities ? this.unitValue?.selected_utilities : [];

        this.selectedUnitType = this.unitValue ? this.unitValue?.unit_mode : 'residential';

            this.form = this.fb.group({
                unit_mode: [this.selectedUnitType],
                unit_type_id: [this.unitValue?.unit_type_id, [Validators.required,
                    Validators.minLength(1)]],
                unit_name: [this.unitValue?.unit_name, [Validators.required,
                    Validators.minLength(1)]],
                unit_floor: [this.unitValue?.unit_floor],
                rent_amount: [this.unitValue?.rent_amount],
               // billing_frequency: [this.unitValue?.billing_frequency],
                bed_rooms: [this.unitValue?.bed_rooms],
                bath_rooms: [this.unitValue?.bath_rooms],
                square_foot: [this.unitValue?.square_foot],
                total_rooms: [this.unitValue?.total_rooms],
                utilityFields: new FormArray([]),
                amenityFields: new FormArray([]),
            });

        this.form.valueChanges.subscribe(value => {
            const optionsChecked = new Array<any>();
            for (let index = 0; index < this.itemsAmenities.length; index++) {
                const isOptionChecked =
                    this.itemsAmenities.get(index.toString()).value;
                if (isOptionChecked) {
                    const currentOptionValue =
                        this.optionsAmenity[index].value;
                    optionsChecked.push(currentOptionValue);
                }
            }
            this.toggle.emit(optionsChecked);
        });

        this.form.valueChanges.subscribe(value => {
            const optionsChecked = new Array<any>();
            for (let index = 0; index < this.itemsUtilities.length; index++) {
                const isOptionChecked =
                    this.itemsUtilities.get(index.toString()).value;
                if (isOptionChecked) {
                    const currentOptionValue =
                        this.optionsUtility[index].value;
                    optionsChecked.push(currentOptionValue);
                }
            }
            this.toggle.emit(optionsChecked);
        });



        if (this.itemsAmenities.length === 0) {
            this.optionsAmenity.forEach(x => {
                this.itemsAmenities.push(new FormControl(false));
            });
        }

        if (this.itemsUtilities.length === 0) {
            this.optionsUtility.forEach(x => {
                this.itemsUtilities.push(new FormControl(false));
            });
        }

        this.selectedValuesAmenity.forEach(value => {
            const index: number =
                this.optionsAmenity.findIndex(opt => opt.value === value);
            if (index >= 0) {
                this.itemsAmenities.get(index.toString()).setValue(true);
            }
        });

        this.selectedValuesUtility.forEach(value => {
            const index: number =
                this.optionsUtility.findIndex(opt => opt.value === value);
            if (index >= 0) {
                this.itemsUtilities.get(index.toString()).setValue(true);
            }
        });
    }

    /**
     *
     */
    private selectedAmenities() {
        return this.form.value.amenityFields
            .map((v, i) => v ? this.optionsAmenity[i].value : null)
            .filter(v => v !== null);
    }

    /**
     *
     */
    get itemsAmenities(): FormArray {
        return this.form.get('amenityFields') as FormArray;
    }

    /**
     *
     */
    private selectedUtilities() {
        return this.form.value.utilityFields
            .map((v, i) => v ? this.optionsUtility[i].value : null)
            .filter(v => v !== null);
    }

    /**
     *
     */
    get itemsUtilities(): FormArray {
        return this.form.get('utilityFields') as FormArray;
    }

    // Workaround for angular component issue #13870
    ngAfterViewInit(): void {
        // timeout required to avoid the dreaded 'ExpressionChangedAfterItHasBeenCheckedError'
        setTimeout(() => this.disableAnimation = false);
    }

    /**
     * For mat-button-toggle-group to select either commercial or residential property unit
     * @param val
     */
    public onToggleChange(val: string) {
        this.selectedUnitType = val;
    }

    /**
     * Send data back
     * @param data
     */
    closeDialog(data) {
        console.log('xxxxDATxxxx', data);
        data.selected_amenities = this.selectedAmenities();
        data.selected_utilities = this.selectedUtilities();
        console.log('Data with selected amenities', data);
        this.dialogRef.close({ event: 'close', data: data });
    }

    close() {
        this.dialogRef.close();
    }

    /**
     *
     */
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.dialogRef.close(this.form.value);
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);
        const body = Object.assign({}, this.unit, this.form.value);
        body.property_id = this.propertyID;
        console.log('unit create', body);

        this.loader = true;
        this.unitService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Unit created.');
            },
            (error) => {
                this.errorInForm.next(true);
                this.loader = false;
                if (error.member === 0) {
                    this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                        ' Check your connection and retry.');
                    return;
                }
                this.formErrors = error;
                if (this.formErrors) {
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
                        }
                    }
                }
            });
    }

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.unit, this.form.value);
        body.id = this.unitID;
        this.loader = true;
        this.errorInForm.next(false);
        this.unitService.update(body).subscribe((data) => {
                this.loader = false;
                this.dialogRef.close(this.form.value);
                this.notification.showNotification('success', 'Success !! Unit has been updated.');
            },
            (error) => {
                this.loader = false;
                this.errorInForm.next(true);
                if (error.utility === 0) {
                    return;
                }
                this.formErrors = error?.error;
                if (this.formErrors) {
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
                        }
                    }
                }
            });
    }

    /**
     * Create or Update Data
     */
    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    deleting() {
        this.dialogRef.close('deleting')
    }
}

