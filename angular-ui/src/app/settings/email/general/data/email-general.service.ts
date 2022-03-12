import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';
import { EmailGeneralSettingModel } from '../model/email-general-setting.model';

@Injectable({ providedIn: 'root' })
export class EmailGeneralService extends BaseService<EmailGeneralSettingModel> {
    private selectedEmailGeneralSource = new BehaviorSubject<EmailGeneralSettingModel | null>(null);
    selectedEmailGeneralChanges$ = this.selectedEmailGeneralSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'system_notifications');
        this.localHttpClient = httpClient;
    }

    changeSelectedEmailGeneral(selectedEmailGeneral: EmailGeneralSettingModel | null ): void {
        this.selectedEmailGeneralSource.next(selectedEmailGeneral);
    }
}
