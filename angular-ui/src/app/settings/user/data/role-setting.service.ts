import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { RoleSettingModel } from '../model/role-setting-model';
import { BaseService } from '../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class RoleSettingService extends BaseService<RoleSettingModel> {
    constructor(httpClient: HttpClient) {
        super( httpClient, 'roles');
    }
}
