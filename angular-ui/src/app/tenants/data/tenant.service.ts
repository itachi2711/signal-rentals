import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TenantModel } from '../models/tenant-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';
import { select, Store } from '@ngrx/store';
import { selectorIsLandlord, selectorIsTenant, selectorUserID } from '../../authentication/authentication.selectors';
import { AppState } from '../../reducers';

@Injectable({ providedIn: 'root' })
export class TenantService extends BaseService<TenantModel> {
    private selectedTenantSource = new BehaviorSubject<TenantModel | null>(null);
    selectedTenantChanges$ = this.selectedTenantSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient, private store: Store<AppState>) {
        super( httpClient, 'tenants');
        this.localHttpClient = httpClient;
    }

    changeSelectedTenant(selectedTenant: TenantModel | null ): void {
        this.selectedTenantSource.next(selectedTenant);
    }

    /**
     *
     * @param item
     */
    search(item: any): Observable<any> {
        const imageUrl = 'search';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {filter: item});
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<TenantModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
    }
}
