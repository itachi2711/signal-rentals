import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { UserSettingModel } from '../../model/user-setting.model';
import { UserSettingService } from '../../data/user-setting.service';
import { NotificationService } from '../../../../shared/notification.service';
import { BehaviorSubject } from 'rxjs';
import { AuthActions } from '../../../../authentication/action-types';
import { Store } from '@ngrx/store';
import { AppState } from '../../../../reducers';

@Component({
    selector: 'robi-add-user',
    styles: [],
    templateUrl: './add-user.component.html'
})

export class AddUserComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();

    user: UserSettingModel;

    loader = false;

    roles: any = [];
    employees: any = [];
    branches: any = [];
    isAdd: boolean;


    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private store: Store<AppState>,
                private fb: FormBuilder,
                private userService: UserSettingService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddUserComponent>) {
        this.roles = row.roles;
        this.employees = row.employees;
        this.branches = row.branches;
        this.isAdd = row.isAdd;
        this.user = row.user;
    }

    ngOnInit() {

        if (this.isAdd) {
            this.form = this.fb.group({
                first_name: ['', [Validators.required,
                    Validators.minLength(3)]],
                middle_name: [''],
                last_name: ['', [Validators.required,
                    Validators.minLength(3)]],
                role_id: [''],
                email: [''],
                password: [''],
                password_confirmation: [''],
            });
        }

        if (!this.isAdd) {
            this.form = this.fb.group({
                first_name: [this.user?.first_name, [Validators.required,
                    Validators.minLength(3)]],
                middle_name: [this.user?.middle_name],
                last_name: [this.user?.last_name, [Validators.required,
                    Validators.minLength(3)]],
                role_id: [this.user?.role_id],
                email: [this.user?.email],
                current_password: [''],
                password: [''],
                password_confirmation: [''],
            });
        }
    }

    /**
     * Create member
     */
    create() {
        this.errorInForm.next(false);
        const body = Object.assign({}, this.user, this.form.value);
        this.loader = true;
        this.userService.create(body).subscribe((data) => {
                this.onSaveComplete();
                this.notification.showNotification('success', 'Success !! User created.');
            },
            (error) => {
                this.errorInForm.next(true);
                this.loader = false;
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
        const body = Object.assign({}, this.user, this.form.value);
        this.loader = true;
        this.errorInForm.next(false);
        this.userService.update(body).subscribe((data) => {
                this.loader = false;
                this.dialogRef.close(this.form.value);
                this.notification.showNotification('success', 'Success !! User has been updated.');
                this.store.dispatch(AuthActions.actionLogout());
            },
            (error) => {
                this.loader = false;
                this.errorInForm.next(true);
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
     * Create or Update Data
     */
    createOrUpdate() {
        this.isAdd ? this.create() : this.update();
    }

    save() {
        this.dialogRef.close(this.form.value);
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
