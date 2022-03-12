import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { PropertyTypeModel } from '../model/property-type-model';
import { BehaviorSubject, Observable } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class PropertyTypeService extends BaseService<PropertyTypeModel> {
    private selectedMemberSource = new BehaviorSubject<PropertyTypeModel | null>(null);
    selectedMemberChanges$ = this.selectedMemberSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'property_types');
        this.localHttpClient = httpClient;
    }

    changeSelectedMember(selectedMember: PropertyTypeModel | null ): void {
        this.selectedMemberSource.next(selectedMember);
    }
}
