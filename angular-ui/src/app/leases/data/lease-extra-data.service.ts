import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { LeaseModel } from '../models/lease-model';
import { BaseService } from '../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class LeaseExtraDataService extends BaseService<LeaseModel> {

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'lease_support_data');
        this.localHttpClient = httpClient;
    }

    fetch() {
        return this.localHttpClient.get<any>(super.getResourceUrl());
    }
}
