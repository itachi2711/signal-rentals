import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';
import { TenantGeneralSettingModel } from '../model/tenant-general-setting.model';

@Injectable({ providedIn: 'root' })
export class TenantGeneralService extends BaseService<TenantGeneralSettingModel> {
    private selectedTenantGeneralSource = new BehaviorSubject<TenantGeneralSettingModel | null>(null);
    selectedTenantGeneralChanges$ = this.selectedTenantGeneralSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'tenant_settings');
        this.localHttpClient = httpClient;
    }

    changeSelectedTenantGeneral(selectedTenantGeneral: TenantGeneralSettingModel | null ): void {
        this.selectedTenantGeneralSource.next(selectedTenantGeneral);
    }
}
