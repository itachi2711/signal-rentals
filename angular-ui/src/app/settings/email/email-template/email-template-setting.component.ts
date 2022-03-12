import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { ConfirmationDialogComponent } from '../../../shared/delete/confirmation-dialog-component';
import { NotificationService } from '../../../shared/notification.service';
import { EmailTemplateModel } from './model/email-template-model';
import { EmailTemplateDataSource } from './data/email-template-data.source';
import { EmailTemplateService } from './data/email-template.service';
import { ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SmsTemplateModel } from './model/sms-template-model';
import { SmsTemplateService } from './data/sms-template.service';
import { AngularEditorConfig } from '@kolkov/angular-editor';

@Component({
    selector: 'robi-email-template-setting',
    templateUrl: './email-template-setting.component.html',
    styleUrls: ['./email-template-setting.component.scss']
})
export class EmailTemplateSettingComponent implements OnInit {
    displayedColumns = [
        'tenant_type_name',
        'tenant_type_display_name',
        'tenant_type_description',
        'actions'
    ];

    loader = false;

    dialogRef: MatDialogRef<ConfirmationDialogComponent>;

    dataSource: EmailTemplateDataSource;

    // Search field
    @ViewChild('search') search: ElementRef;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;

    form: FormGroup;
    smsForm: FormGroup;
    smsTemplate: SmsTemplateModel;

    emailTemplate: EmailTemplateModel;
    formErrors: any;

    templates: any;
    smsTemplates: any;
    templateId: any;
    name: any;
    subject: any;
    body: any;
    tags: any;

    smsTemplateId: any;
    smsName: any;
    smsBody: any;
    smsTags: any;
    editorConfig: AngularEditorConfig = {
        editable: true,
        spellcheck: true,
        height: 'auto',
        minHeight: '0',
        maxHeight: 'auto',
        width: 'auto',
        minWidth: '0',
        translate: 'yes',
        enableToolbar: true,
        showToolbar: true,
        placeholder: 'Enter text here...',
        defaultParagraphSeparator: '',
        defaultFontName: '',
        defaultFontSize: '',
        fonts: [
            {class: 'arial', name: 'Arial'},
            {class: 'times-new-roman', name: 'Times New Roman'},
            {class: 'calibri', name: 'Calibri'},
            {class: 'comic-sans-ms', name: 'Comic Sans MS'}
        ],
        customClasses: [
            {
                name: 'quote',
                class: 'quote',
            },
            {
                name: 'redText',
                class: 'redText'
            },
            {
                name: 'titleText',
                class: 'titleText',
                tag: 'h1',
            },
        ],
        uploadUrl: 'v1/image',
        uploadWithCredentials: false,
        sanitize: true,
        toolbarPosition: 'top',
        /*toolbarHiddenButtons: [
            ['bold', 'italic'],
            ['fontSize']
        ]*/
    };
    constructor(private fb: FormBuilder, private route: ActivatedRoute,
                private notification: NotificationService,
                private smsTemplateService: SmsTemplateService,
                private emailTemplateService: EmailTemplateService,
                private dialog: MatDialog) {
        this.form = this.fb.group({
            name: [{value: '', disabled: true}],
            template: ['', [Validators.required,
                Validators.minLength(2)]],
            subject: ['', [Validators.required,
                Validators.minLength(2)]],
            body: [''],
            tags: [{value: '', disabled: true}]
        });

        this.smsForm = this.fb.group({
            name: [{value: '', disabled: true}],
            template: ['', [Validators.required,
                Validators.minLength(2)]],
            body: [''],
            tags: [{value: '', disabled: true}]
        });
    }

    /**
     *
     */
    ngOnInit(): void {
        this.fetchEmailTemplates();
        this.fetchSmsTemplates();
    }

    /**
     *
     */
    fetchEmailTemplates() {
        this.emailTemplateService.list(['name', 'display_name', 'subject', 'body', 'tags'])
            .subscribe((res) => this.templates = res,
                () => this.templates = []
            );
    }

    /**
     *
     */
    fetchSmsTemplates() {
        this.smsTemplateService.list(['name', 'display_name', 'body', 'tags'])
            .subscribe((res) => this.smsTemplates = res,
                () => this.smsTemplates = []
            );
    }

    /**
     * Update supporting fields when Template drop down changes content
     * @param value
     */
    onTemplateItemChange(value) {
        this.templateId = this.templates.find((item: any) => item.id === value)?.id;
        this.name = this.templates.find((item: any) => item.id === value)?.name;
        this.subject = this.templates.find((item: any) => item.id === value)?.subject;
        this.body = this.templates.find((item: any) => item.id === value)?.body;
        this.tags = this.templates.find((item: any) => item.id === value)?.tags;

        this.form.patchValue({
            name: this.name,
            subject: this.subject,
            body: this.body,
            tags: this.tags
        });
    }

    /**
     * Update supporting fields when Template drop down changes content
     * @param value
     */
    onSMSTemplateItemChange(value) {
        this.smsTemplateId = this.smsTemplates.find((item: any) => item.id === value)?.id;
        this.smsName = this.smsTemplates.find((item: any) => item.id === value)?.name;
        this.smsBody = this.smsTemplates.find((item: any) => item.id === value)?.body;
        this.smsTags = this.smsTemplates.find((item: any) => item.id === value)?.tags;

        this.smsForm.patchValue({
            name: this.smsName,
            body: this.smsBody,
            tags: this.smsTags
        });
    }

    /**
     *
     */
    update() {
        const body = Object.assign({}, this.emailTemplate, this.form.value);
        body.id = this.templateId;

        this.loader = true;
        this.emailTemplateService.update(body)
            .subscribe((data) => {
                    this.loader = false;

                    // notify success
                    this.notification.showNotification('success', 'Success !!  Email Template has been updated.');
                    this.fetchEmailTemplates();
                },
                (error) => {
                    this.loader = false;

                    if (error.payment === 0) {
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

    /**
     *
     */
    updateSms() {
        const body = Object.assign({}, this.smsTemplate, this.smsForm.value);
        body.id = this.smsTemplateId;
        this.loader = true;
        this.smsTemplateService.update(body)
            .subscribe((data) => {
                    //  console.log('Update email template: ', data);
                    this.loader = false;

                    // notify success
                    this.notification.showNotification('success', 'Success !!  Sms Template has been updated.');
                    this.fetchSmsTemplates();
                },
                (error) => {
                    this.loader = false;

                    if (error.payment === 0) {
                        return;
                    }
                    // An array of all form errors as returned by server
                    this.formErrors = error;

                    if (this.formErrors) {
                        // loop through from fields, If has an error, mark as invalid so mat-error can show
                        for (const prop in this.formErrors) {
                            if (this.smsForm) {
                                this.smsForm.controls[prop]?.markAsTouched();
                                this.smsForm.controls[prop].setErrors({incorrect: true});
                            }
                        }
                    }
                });
    }
}
