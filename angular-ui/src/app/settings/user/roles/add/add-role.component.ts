import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { RoleSettingModel } from '../../model/role-setting-model';
import { RoleSettingService } from '../../data/role-setting.service';
import { NotificationService } from '../../../../shared/notification.service';

@Component({
    selector: 'robi-add-role',
    styles: [],
    templateUrl: './add-role.component.html'
})
export class AddRoleComponent implements OnInit  {

    form: FormGroup;

    formErrors: any;

    role: RoleSettingModel;

    loader = false;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private roleService: RoleSettingService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<AddRoleComponent>) {

    }

    ngOnInit() {
        this.form = this.fb.group({
            name: ['', [Validators.required,
                Validators.minLength(3)]],
            display_name: [''],
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
    create() {

        const body = Object.assign({}, this.role, this.form.value);

        this.loader = true;

        this.roleService.create(body)
            .subscribe((data) => {
                    this.onSaveComplete();
                    this.notification.showNotification('success', 'Success !! New role created.');
                },
                (error) => {
                    this.loader = false;
                    if (error.role === 0) {
                        this.notification.showNotification('danger', 'Connection Error !! Nothing created.' +
                            ' Check your connection and retry.');
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

    /**
     *
     */
    public onSaveComplete(): void {
        this.loader = false;
        this.form.reset();
        this.dialogRef.close(this.form.value);
    }

}
