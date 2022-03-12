import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TenantTypeModel } from '../model/tenant-type-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class TenantTypeService extends BaseService<TenantTypeModel> {
    private selectedTenantTypeSource = new BehaviorSubject<TenantTypeModel | null>(null);
    selectedTenantTypeChanges$ = this.selectedTenantTypeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'tenant_types');
        this.localHttpClient = httpClient;
    }

    changeSelectedTenantType(selectedTenantType: TenantTypeModel | null ): void {
        this.selectedTenantTypeSource.next(selectedTenantType);
    }
}
