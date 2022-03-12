import { AfterViewInit, Component, EventEmitter, Inject, OnInit, Output, ViewChild } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormArray, FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

import { BehaviorSubject } from 'rxjs';
import { InvoiceService } from '../../data/invoice.service';
import { CheckboxItem } from './check-box-item';

@Component({
    selector: 'robi-add-member',
    templateUrl: './utility-bill-unit-details.component.html',
    styleUrls: ['./utility-bill-unit-details.component.scss']
})
export class UtilityBillUnitDetailsComponent implements OnInit, AfterViewInit  {

    form: FormGroup;

    formErrors: any;
    // formError$: Observable<boolean>;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    member: any;

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

    mode: 'add' | 'edit';
    landlord: any;
    unitValue: any;

    public selectedUnitType: string;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;


    panelOpenState = false;
    disableAnimation = true;

    fromDialog: string;

    utilities$: any;
    amenities$: any;

  //  amenities: any;
    amenities = Array<CheckboxItem>();
    optionsAmenity = Array<CheckboxItem>();
    optionsUtility = Array<CheckboxItem>();
    selectedValuesAmenity: string[];
    selectedValuesUtility: string[];
    @Output() toggle = new EventEmitter<any[]>();

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private memberService: InvoiceService,
                private landlordEntityService: InvoiceService,
                private notification: InvoiceService,
                private dialogRef: MatDialogRef<UtilityBillUnitDetailsComponent>) {
            this.unitValue = row.unitValue;
            this.utilities$ = row.utilities;
            this.amenities$ = row.amenities;
            this.amenities = row.amenitiesData;
            this.optionsAmenity = row.amenityOptions;
            this.optionsUtility = row.utilityOptions;

      //  this.selectedValues = this.role.permissions.map(x => x['id']);
    }

    ngOnInit() {

        this.selectedValuesAmenity = this.unitValue?.selected_amenities ? this.unitValue?.selected_amenities : [];
        this.selectedValuesUtility = this.unitValue?.selected_utilities ? this.unitValue?.selected_utilities : [];

        this.selectedUnitType = this.unitValue ? this.unitValue?.unit_type : 'residential';

            this.form = this.fb.group({
                unit_type: [this.selectedUnitType],
                unit_name: [this.unitValue?.unit_name, [Validators.required,
                    Validators.minLength(1)]],
                bed_rooms: [this.unitValue?.bed_rooms],
                bath_rooms: [this.unitValue?.bath_rooms],
                square_foot: [this.unitValue?.square_foot, [Validators.required,
                    Validators.minLength(1)]],
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
        console.log(data);
        data.selected_amenities = this.selectedAmenities();
        data.selected_utilities = this.selectedUtilities();
        console.log('Data with selected amenities', data);
        this.dialogRef.close({ event: 'close', data: data });
    }

    holdUnitDetails() {

    }



    /**
     *
     * @param event
     */
    detectFiles(event) {
        this.urls = [];
        const files = event.target.files;
        if (files) {
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = (e: any) => {
                    this.urls.push(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
    }

    /**
     *
     * @param file
     */
    handleFileInput(file: FileList) {
        this.profilePicFileToUpload = file.item(0);

        const reader = new FileReader();

        reader.onload = (event: any) => {
            this.profilePicUrl = event.target.result;
        };

        reader.readAsDataURL(this.profilePicFileToUpload);
    }

    /**
     *
     * @param file
     */
    onProfilePicSelect(file: FileList) {

        if (file.length > 0) {
            this.profilePicFileToUpload = file.item(0);

            const reader = new FileReader();

            reader.onload = (event: any) => {
                this.profilePicUrl = event.target.result;
            };
            reader.readAsDataURL(this.profilePicFileToUpload);
        }
    }

    /**
     *
     * @param file
     */
    onMembershipFormInputSelect(file: FileList) {

        if (file.length > 0) {
            this.membershipFormFileToUpload = file.item(0);

            const reader = new FileReader();

            reader.onload = (event: any) => {
                this.membershipFormUrl = event.target.result;
            };

            reader.readAsDataURL(this.membershipFormFileToUpload);
        }
    }

    /**
     *
     * @param event
     */
    onFileSelect(event) {
        if (event.target.files.length > 0) {
            const file = event.target.files[0];
            this.form.get('passport_photo').setValue(file);
        }
    }

    /**
     *
     * @param file
     */
    membershipFormUpload(file: FileList) {

        if (file.length > 0) {
            this.membershipFormToUpload = file.item(0);

            const reader = new FileReader();

            reader.onload = (event: any) => {
                this.membershipFormUrl = event.target.result;
            };
            reader.readAsDataURL(this.membershipFormToUpload);
        }
    }

    /**
     * Create member
     */
  /*  create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.landlord, this.form.value);

        const formData = new FormData();
        if (this.profilePicFileToUpload != null) {
            formData.append('passport_photo', this.profilePicFileToUpload);
        }
        if (this.membershipFormToUpload != null) {
            formData.append('membership_form', this.membershipFormToUpload);
        }

        for (const key in body) {
            if (body.hasOwnProperty(key)) {
                formData.append(key, body[key]);
            }
        }
        this.loader = true;

        this.landlordEntityService.add(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! Landlord created.');
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
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
                        }
                    }
                }

            });
    }*/

    /**
     *
     */
   /* update() {
        const body = Object.assign({}, this.landlord, this.form.value);
        delete body.membership_form;

        this.loader = true;
        this.errorInForm.next(false);

        this.landlordEntityService.update(body).subscribe((data) => {
                this.loader = false;

                this.dialogRef.close(this.form.value);

                // notify success
                this.notification.showNotification('success', 'Success !! Landlord has been updated.');

            },
            (error) => {
                this.loader = false;
                this.errorInForm.next(true);
               // this.formError$.subscribe(subscriber => {subscriber.next(true)});

                if (error.landlord === 0) {
                    // notify error
                    return;
                }
                // An array of all form errors as returned by server
                this.formErrors = error?.error;
              //  this.formErrors = error.error.error.errors;

                if (this.formErrors) {
                    // loop through from fields, If has an error, mark as invalid so mat-error can show
                    for (const prop in this.formErrors) {
                        if (this.form) {
                            this.form.controls[prop]?.markAsTouched();
                            this.form.controls[prop]?.setErrors({incorrect: true});
                        }
                    }
                }
            });
    }*/

    /**
     * Create or Update Data
     */
    createOrUpdate() {
        switch (this.mode) {
            case 'edit' : {
               // this.update();
            }
                break;
            case 'add' : {
              //  this.create();
            }
                break;
            default :
                break;
        }
       // this.dialogRef.close(this.form.value);
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

}

