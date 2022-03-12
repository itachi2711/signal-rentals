import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { PermissionSettingModel } from '../model/permission-setting-model';
import { BaseService } from '../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class PermissionSettingService extends BaseService<PermissionSettingModel> {
    constructor(httpClient: HttpClient) {
        super( httpClient, 'permissions');
    }
}
