import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { PropertyModel } from '../models/property-model';

@Injectable({ providedIn: 'root' })
export class PropertyExtraDataService extends BaseService<PropertyModel> {

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'property_support_data');
        this.localHttpClient = httpClient;
    }

    fetch() {
        return this.localHttpClient.get<any>(super.getResourceUrl());
    }
}
