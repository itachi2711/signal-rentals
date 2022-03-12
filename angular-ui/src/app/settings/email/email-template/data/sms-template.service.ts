import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { SmsTemplateModel } from '../model/sms-template-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class SmsTemplateService extends BaseService<SmsTemplateModel> {
    private selectedTenantTypeSource = new BehaviorSubject<SmsTemplateModel | null>(null);
    selectedTenantTypeChanges$ = this.selectedTenantTypeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'sms_templates');
        this.localHttpClient = httpClient;
    }

    changeSelectedTenantType(selectedTenantType: SmsTemplateModel | null ): void {
        this.selectedTenantTypeSource.next(selectedTenantType);
    }
}
