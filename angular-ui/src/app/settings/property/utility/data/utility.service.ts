import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { UtilityModel } from '../model/utility-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class UtilityService extends BaseService<UtilityModel> {
    private selectedUtilitySource = new BehaviorSubject<UtilityModel | null>(null);
    selectedUtilityChanges$ = this.selectedUtilitySource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'utilities');
        this.localHttpClient = httpClient;
    }

    changeSelectedUtility(selectedUtility: UtilityModel | null ): void {
        this.selectedUtilitySource.next(selectedUtility);
    }
}
