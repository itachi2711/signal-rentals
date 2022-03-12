import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { PermissionSettingService } from '../../data/permission-setting.service';
import { PermissionSettingModel } from '../../model/permission-setting-model';
import { NotificationService } from '../../../../shared/notification.service';

@Component({
    selector: 'robi-edit-permission',
    styles: [],
    templateUrl: './edit-permission.component.html'
})
export class EditPermissionComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    permission: PermissionSettingModel;

    loader = false;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private permissionService: PermissionSettingService,
                private notification: NotificationService,
    private dialogRef: MatDialogRef<EditPermissionComponent>) {

        this.permission = row.permission;
    }

    ngOnInit() {
        this.form = this.fb.group({
            name: [{value: this.permission.name, disabled: true}, [Validators.required,
                Validators.minLength(3)]],
            display_name: [this.permission.display_name, [Validators.required,
                Validators.minLength(3)]],
            description: [this.permission.description, [Validators.required,
                Validators.minLength(3)]],
        });
    }

    close() {
        this.dialogRef.close();
    }

    update() {
        const body = Object.assign({}, this.permission, this.form.value);

        this.loader = true;
        this.permissionService.update(body)
            .subscribe((data) => {
                    this.loader = false;

                    this.dialogRef.close(this.form.value);

                    // notify success
                    this.notification.showNotification('success', 'Success !! Permission has been updated.');

                },
                (error) => {
                    this.loader = false;

                    if (error.permission === 0) {
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
