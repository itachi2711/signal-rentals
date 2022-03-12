import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../../shared/notification.service';
import { LeaseModel } from '../../models/lease-model';
import { RoleSettingService } from '../../../settings/user/data/role-setting.service';
import { LeaseService } from '../../data/lease.service';
import { Router } from '@angular/router';
import { BehaviorSubject } from 'rxjs';
import { TerminateModel } from '../../models/terminate-model';

@Component({
    selector: 'robi-terminate-lease',
    styles: [],
    templateUrl: './terminate-lease.component.html'
})
export class TerminateLeaseComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    private terminationErrorMessage = new BehaviorSubject<string>('');
    terminationError$ = this.terminationErrorMessage.asObservable();

    terminateModel: TerminateModel;

    loader = false;
    leaseNumber: string;
    leaseID: string;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private leaseService: LeaseService,
                private notification: NotificationService,
                private router: Router,
                private dialogRef: MatDialogRef<TerminateLeaseComponent>) {
        this.leaseNumber = row.leaseNumber;
        this.leaseID = row.leaseID;
    }

    ngOnInit() {
        this.form = this.fb.group({
            end_date: [(new Date()).toISOString().substring(0, 10), [Validators.required]],
            termination_reason: ['', [Validators.required]],
        });
    }

    save() {
        this.dialogRef.close(this.form.value);
    }

    close() {
        this.dialogRef.close();
    }

    /**
     * Create a resource
     */
    terminate() {
        this.errorInForm.next(false);
        this.loader = true;

        const body = Object.assign({}, this.terminateModel, this.form.value);
        body.lease_id = this.leaseID;

        this.leaseService.terminate(body)
            .subscribe((data) => {
                console.log(data);
                    this.loader = false;
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! Lease is terminated.');
                },
                (error) => {
                    this.errorInForm.next(true);
                    this.loader = false;
                    this.terminationErrorMessage.next(error.error.message);

                    if (error.role === 0) {
                        this.notification.showNotification('danger', 'Connection Error !!' +
                            ' Check your connection and retry.');
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.form && this.form.controls[prop]) {
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }

                });
    }

    /**
     *
     */
    public onSaveComplete(): void {
        this.form.reset();
        this.dialogRef.close(this.form.value);
        this.router.navigate(['/leases']);
    }

}
