import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { UnitTypeModel } from '../model/unit-type-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class UnitTypeService extends BaseService<UnitTypeModel> {
    private selectedUnitTypeSource = new BehaviorSubject<UnitTypeModel | null>(null);
    selectedUnitTypeChanges$ = this.selectedUnitTypeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'unit_types');
        this.localHttpClient = httpClient;
    }

    changeSelectedUnitType(selectedUnitType: UnitTypeModel | null ): void {
        this.selectedUnitTypeSource.next(selectedUnitType);
    }
}
