import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LeaseTypeModel } from '../model/lease-type-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class LeaseTypeService extends BaseService<LeaseTypeModel> {
    private selectedLeaseTypeSource = new BehaviorSubject<LeaseTypeModel | null>(null);
    selectedLeaseTypeChanges$ = this.selectedLeaseTypeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'lease_types');
        this.localHttpClient = httpClient;
    }

    changeSelectedLeaseType(selectedLeaseType: LeaseTypeModel | null ): void {
        this.selectedLeaseTypeSource.next(selectedLeaseType);
    }
}
