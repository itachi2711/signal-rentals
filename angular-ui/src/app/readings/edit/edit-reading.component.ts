import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NotificationService } from '../../shared/notification.service';
import { BehaviorSubject } from 'rxjs';
import { ReadingModel } from '../models/reading-model';
import { ReadingService } from '../data/reading.service';

@Component({
    selector: 'robi-edit-reading',
    styles: [],
    templateUrl: './edit-reading.component.html'
})
export class EditReadingComponent implements OnInit  {

    form: FormGroup;
    formErrors: any;
    private errorInForm = new BehaviorSubject<boolean>(false);
    formError$ = this.errorInForm.asObservable();
    loader = false;
    reading: ReadingModel;
    isAdd: boolean;

    constructor(@Inject(MAT_DIALOG_DATA) row: any,
                private fb: FormBuilder,
                private readingService: ReadingService,
                private notification: NotificationService,
                private dialogRef: MatDialogRef<EditReadingComponent>) {
        this.isAdd = row?.isAdd;
        this.reading = row?.reading;
    }

    ngOnInit() {
        if (this.isAdd) {
            this.form = this.fb.group({
                current_reading: ['', [Validators.required,
                    Validators.minLength(1)]],
                reading_date: ['']
            });
        }

        if (!this.isAdd) {
            const property = this.reading?.property?.property_name +
                ' (' + this.reading?.property?.property_code + ')' + ' - ' + this.reading?.property?.location;
            this.form = this.fb.group({
                current_reading: [this.reading?.current_reading, [Validators.required,
                    Validators.minLength(1)]],
                reading_date: [this.reading?.reading_date],
                property: [{value: property, disabled: true}],
                utility: [{value: this.reading?.utility?.utility_display_name, disabled: true}],
                unit: [{value: this.reading?.unit?.unit_name, disabled: true}],
            });
        }
    }

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.reading, this.form.value);
        this.loader = true;
        this.errorInForm.next(false);
        this.readingService.update(body)
            .subscribe((data) => {
                    this.loader = false;
                    this.dialogRef.close(this.form.value);
                    this.notification.showNotification('success', 'Success !! reading has been updated.');
                },
                (error) => {
                    this.loader = false;
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
