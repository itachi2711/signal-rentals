import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { EmailTemplateModel } from '../model/email-template-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class EmailTemplateService extends BaseService<EmailTemplateModel> {
    private selectedTenantTypeSource = new BehaviorSubject<EmailTemplateModel | null>(null);
    selectedTenantTypeChanges$ = this.selectedTenantTypeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'email_templates');
        this.localHttpClient = httpClient;
    }

    changeSelectedTenantType(selectedTenantType: EmailTemplateModel | null ): void {
        this.selectedTenantTypeSource.next(selectedTenantType);
    }
}
