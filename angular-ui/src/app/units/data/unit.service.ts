import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';
import { UnitModel } from '../model/unit-model';

@Injectable({ providedIn: 'root' })
export class UnitService extends BaseService<UnitModel> {
    private selectedUnitSource = new BehaviorSubject<UnitModel | null>(null);
    selectedUnitChanges$ = this.selectedUnitSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'units');
        this.localHttpClient = httpClient;
    }

    changeSelectedUnit(selectedUnit: UnitModel | null ): void {
        this.selectedUnitSource.next(selectedUnit);
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
