import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { PropertyModel } from '../models/property-model';
import { BaseService } from '../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class PropertyLeaseService extends BaseService<PropertyModel> {

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient, propertyID) {
        super( httpClient, 'properties/' + propertyID + '/leases');
        this.localHttpClient = httpClient;
    }
}
