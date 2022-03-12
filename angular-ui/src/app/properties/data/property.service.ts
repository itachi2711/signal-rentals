import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { PropertyModel } from '../models/property-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PropertyService extends BaseService<PropertyModel> {
    private selectedPropertySource = new BehaviorSubject<PropertyModel | null>(null);
    selectedPropertyChanges$ = this.selectedPropertySource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'properties');
        this.localHttpClient = httpClient;
    }

    changeSelectedProperty(selectedProperty: PropertyModel | null ): void {
        this.selectedPropertySource.next(selectedProperty);
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<PropertyModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
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

    /**
     *
     * @param item
     */
    periods(item: any): Observable<any> {
        const endpoint = 'periods';
        const url =  `${super.getResourceUrl()}/${endpoint}`;
        return this.localHttpClient.post<any>(url, {id: item});
    }

    /**
     *
     * @param item
     */
    report(item: any): Observable<any> {
        const endpoint = 'report';
        const url =  `${super.getResourceUrl()}/${endpoint}`;
        return this.localHttpClient.post<any>(url, item, { responseType: 'blob' as 'json'});
    }
}
