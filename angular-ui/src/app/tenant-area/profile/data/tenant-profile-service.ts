import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../../shared/base-service';
import { TenantProfileModel } from '../model/tenant-profile.model';

@Injectable({ providedIn: 'root' })
export class TenantProfileService extends BaseService<TenantProfileModel> {
    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'tenant_profile');
        this.localHttpClient = httpClient;
    }
}
