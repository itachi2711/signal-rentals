import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../../shared/base-service';
import { LandlordProfileModel } from '../model/landlord-profile.model';

@Injectable({ providedIn: 'root' })
export class LandlordProfileService extends BaseService<LandlordProfileModel> {
    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'landlord_profile');
        this.localHttpClient = httpClient;
    }
}
