import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';
import { LeaseGeneralSettingModel } from '../model/lease-general-setting.model';

@Injectable({ providedIn: 'root' })
export class LeaseSettingService extends BaseService<LeaseGeneralSettingModel> {
    private selectedLeaseSettingSource = new BehaviorSubject<LeaseGeneralSettingModel | null>(null);
    selectedLeaseSettingChanges$ = this.selectedLeaseSettingSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'lease_settings');
        this.localHttpClient = httpClient;
    }

    changeSelectedLeaseSetting(selectedLeaseSetting: LeaseGeneralSettingModel | null ): void {
        this.selectedLeaseSettingSource.next(selectedLeaseSetting);
    }
}
