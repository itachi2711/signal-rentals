import { Component, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { MatStepper } from '@angular/material/stepper';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { LandlordModel } from '../models/landlord-model';
import { LandlordService } from '../data/landlord.service';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject, Observable } from 'rxjs';
import { ConfirmationDialogComponent } from '../../shared/delete/confirmation-dialog-component';
import { ActivatedRoute, Router } from '@angular/router';
import { LeaseModel } from '../../leases/models/lease-model';
import { AuthenticationService } from '../../authentication/authentication.service';

@Component({
    selector: 'robi-add-landlord',
    styles: [],
    templateUrl: './add-landlord.component.html'
})
export class AddLandlordComponent implements OnInit  {

    form: FormGroup;
    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();
    loader = false;
    formGroup: FormGroup;

    isAdd = true;
    landlord: LandlordModel;
    landlordID: string;

    @ViewChild('stepper', {static: true }) stepper: MatStepper;
    deleteDialogRef: MatDialogRef<ConfirmationDialogComponent>;
    isAdmin$: Observable<boolean>;
    constructor(private fb: FormBuilder,
                private dialog: MatDialog,
                private landlordService: LandlordService,
                private notification: NotificationService,
                private authenticationService: AuthenticationService,
                private router: Router, private route: ActivatedRoute) {
        this.isAdmin$ = this.authenticationService.isAdmin();
        this.form = this.fb.group({
            first_name: ['', [Validators.required,
                Validators.minLength(2)]],
            middle_name: [''],
            last_name: ['', [Validators.required,
                Validators.minLength(2)]],
            country: [''],
            id_number: [''],
            passport_number: [''],
            phone: [''],
            email: [''],
            registration_date: [''],
            postal_address: [''],
            physical_address: [''],
            residential_address: [''],
            county: [''],
            city: [''],
            state: [''],
            status_id: [''],
            password: [''],
            password_confirmation: ['']
        });
    }

    ngOnInit() {
        this.landlordID = this.route.snapshot.paramMap.get('id');
        if (this.landlordID) {
            this.isAdd = false;

            this.landlordService.selectedLandlordChanges$.subscribe(landlord => {
                if (landlord) {
                    this.landlord = landlord;
                    this.populateForm(landlord);
                }
                if (!landlord) {
                    this.landlordService.getById(this.landlordID).subscribe(data => {
                        this.landlord = data;
                        this.landlordService.changeSelectedLandlord(data);
                        this.populateForm(data);
                    });
                }
            });
        }
    }

    populateForm(landlord: LandlordModel) {
        this.form.patchValue({
            first_name: landlord?.first_name,
            middle_name: landlord?.middle_name,
            last_name: landlord?.last_name,
            country: landlord?.country,
            id_number: landlord?.id_number,
            passport_number: landlord?.passport_number,
            phone: landlord?.phone,
            email: landlord?.email,
            registration_date: landlord?.registration_date,
            postal_address: landlord?.postal_address,
            residential_address: landlord?.residential_address,
            physical_address: landlord?.physical_address,
            county: landlord?.county,
            city: landlord?.city,
            state: landlord?.state,
            status_id: landlord?.status_id
        });
    }

    /**
     * Create landlord
     */
    create() {
        this.errorInForm.next(false);

        const body = Object.assign({}, this.landlord, this.form.value);

        const formData = new FormData();

        for (const key in body) {
            if (body.hasOwnProperty(key)) {
                formData.append(key, body[key]);
            }
        }
        this.loader = true;

        this.landlordService.create(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! New Landlord created.');
                    this.onSaveComplete();
                },
                (error) => {
                    this.loader = false;
                    if (error.landlord === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
                        return;
                    }
                    this.formErrors = error;

                    if (this.formErrors) {
                        for (const prop in this.formErrors) {
                            if (this.form) {
                                this.form.controls[prop]?.markAsTouched();
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.landlord, this.form.value);
        this.loader = true;
        this.errorInForm.next(false);

        this.landlordService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.notification.showNotification('success', 'Success !! Landlord has been updated.');
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
                            if (this.form) {
                                this.form.controls[prop]?.markAsTouched();
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }

    openConfirmationDialog(landlord: LandlordModel) {
        this.deleteDialogRef = this.dialog.open(ConfirmationDialogComponent, {
            disableClose: true
        });
        this.deleteDialogRef.afterClosed().subscribe((result) => {
            if (result) {
                this.delete(landlord);
            }
            this.deleteDialogRef = null;
        });
    }

    private delete(landlord: LandlordModel) {
        this.loader = true;
        this.landlordService.delete(landlord)
            .subscribe((data) => {
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Landlord has been deleted.');
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

   createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    close() {
      //  this.dialogRef.close();
    }

    /**
     *
     */
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.router.navigate(['/landlords']);
    }

}

