import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AmenityModel } from '../model/amenity-model';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class AmenityService extends BaseService<AmenityModel> {
    private selectedAmenitySource = new BehaviorSubject<AmenityModel | null>(null);
    selectedAmenityChanges$ = this.selectedAmenitySource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'amenities');
        this.localHttpClient = httpClient;
    }

    changeSelectedAmenity(selectedAmenity: AmenityModel | null ): void {
        this.selectedAmenitySource.next(selectedAmenity);
    }
}
