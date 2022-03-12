import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';
import { UnitModel } from '../model/unit-model';

@Injectable({ providedIn: 'root' })
export class VacantUnitService extends BaseService<UnitModel> {
    private selectedVacantUnitSource = new BehaviorSubject<UnitModel | null>(null);
    selectedVacantUnitChanges$ = this.selectedVacantUnitSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'units/vacants');
        this.localHttpClient = httpClient;
    }

    changeSelectedVacantUnit(selectedVacantUnit: UnitModel | null ): void {
        this.selectedVacantUnitSource.next(selectedVacantUnit);
    }

    /**
     *
     * @param item
     */
    search(item: any): Observable<any> {
        const endpoint = 'search';
        const url =  `${super.getResourceUrl()}/${endpoint}`;
        return this.localHttpClient.post<any>(url, {filter: item});
    }
}
