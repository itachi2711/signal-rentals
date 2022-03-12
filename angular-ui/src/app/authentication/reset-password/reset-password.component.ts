import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { UserSettingService } from '../../settings/user/data/user-setting.service';
import { ResetPasswordModel } from './reset-password.model';
import { Router } from '@angular/router';

@Component({templateUrl: 'reset-password.component.html'})
export class ResetPasswordComponent implements OnInit {

    form: FormGroup;
    formErrors: any;
    loader = false;

    resetModel: ResetPasswordModel;

    constructor(private fb: FormBuilder, private userService: UserSettingService,
                private notification: NotificationService, private router: Router ){
        this.form = this.fb.group({
            email: ['', [Validators.required,
                Validators.minLength(3)]],
            token: ['', [Validators.required,
                Validators.minLength(3)]],
            password: ['', [Validators.required,
                Validators.minLength(3)]],
            password_confirmation: ['', [Validators.required,
                Validators.minLength(3)]],
        });
    }

    /**
     *
     */
    ngOnInit(): void {}

    /**
     *
     */
    resetPassword() {
        const body = Object.assign({}, this.resetModel, this.form.value);

        console.log(body);

        this.loader = true;
        this.userService.resetPassword(body)
            .subscribe((data) => {
                    this.loader = false;
                    // navigate to login
                    this.notification.showNotification('success', 'Success !! Password reset is complete.');
                    this.notification.showNotification('success', 'You may now login with your new password.');
                    this.router.navigate(['login']);
                },
                (error) => {
                    this.loader = false;

                    if (error.payment === 0) {
                        // notify error
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.form) {
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }
}
