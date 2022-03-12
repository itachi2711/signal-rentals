import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { EmailGeneralSettingModel } from '../model/email-general-setting.model';
import { NotificationService } from '../../../../shared/notification.service';
import { EmailGeneralService } from '../data/email-general.service';

@Component({
    selector: 'robi-edit-email-general',
    styles: [],
    templateUrl: './edit-email-general.component.html'
})
export class EditEmailGeneralComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    communicationSetting: EmailGeneralSettingModel;

    loader = false;

    options = [
        {id: true, name: 'Yes'},
        {id: false, name: 'No'}
        ];

    emailTemplates: any = [];
    smsTemplates: any = [];

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private communicationGeneralSettingService: EmailGeneralService,
                private notification: NotificationService,
    private dialogRef: MatDialogRef<EditEmailGeneralComponent>) {

        this.communicationSetting = row.communicationSetting;
        this.emailTemplates = row.emailTemplates;
        this.smsTemplates = row.smsTemplates;

        this.form = fb.group({
			display_name: [{value: this.communicationSetting.display_name, disabled: true}, [Validators.required,
                Validators.minLength(3)]],
            send_email: [this.communicationSetting.send_email],
            send_sms: [this.communicationSetting.send_sms]
        });
    }

    ngOnInit() {
    }

    close() {
        this.dialogRef.close();
    }

    updateType() {
        const body = Object.assign({}, this.communicationSetting, this.form.value);

        this.loader = true;
        this.communicationGeneralSettingService.update(body)
            .subscribe((data) => {
                    this.loader = false;

                    this.dialogRef.close(this.form.value);

                    // notify success
                    this.notification.showNotification('success', 'Success !! Setting has been updated.');

                },
                (error) => {
                    this.loader = false;

                    if (error.type === 0) {
                        return;
                    }
                    // An array of all form errors as returned by server
                   this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.form) {
                                this.form.controls[prop]?.markAsTouched();
                                this.form.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }

}
