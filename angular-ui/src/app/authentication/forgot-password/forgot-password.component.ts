import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { UserSettingService } from '../../settings/user/data/user-setting.service';
import { UserSettingModel } from '../../settings/user/model/user-setting.model';
import { Router } from '@angular/router';

@Component({templateUrl: 'forgot-password.component.html'})
export class ForgotPasswordComponent implements OnInit {

    form: FormGroup;
    formErrors: any;
    loader = false;

    user: UserSettingModel;

    constructor(private fb: FormBuilder, private userService: UserSettingService,
                private notification: NotificationService, private router: Router ){
        this.form = this.fb.group({
            email: ['', [Validators.required,
                Validators.minLength(3)]]
        });
    }

    /**
     *
     */
    ngOnInit(): void {}

    /**
     *
     */
    requestPassword() {
        const body = Object.assign({}, this.user, this.form.value);

        this.loader = true;
        this.userService.forgotPassword(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.router.navigate(['reset-password']);
                    // notify success
                    this.notification.showNotification('info', 'Check your email for password reset code.');
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
