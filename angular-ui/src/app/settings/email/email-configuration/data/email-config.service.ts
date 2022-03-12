import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';
import { EmailConfigSettingModel } from '../model/email-config-setting.model';

@Injectable({ providedIn: 'root' })
export class EmailConfigService extends BaseService<EmailConfigSettingModel> {
    private selectedEmailConfigurationSource = new BehaviorSubject<EmailConfigSettingModel | null>(null);
    selectedEmailConfigurationChanges$ = this.selectedEmailConfigurationSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'email_configuration_settings');
        this.localHttpClient = httpClient;
    }

    changeSelectedEmailConfiguration(selectedEmailConfiguration: EmailConfigSettingModel | null ): void {
        this.selectedEmailConfigurationSource.next(selectedEmailConfiguration);
    }
}
