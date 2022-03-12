import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AccountingModel } from '../models/accounting-model';
import { BaseService } from '../../shared/base-service';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class AccountingService extends BaseService<AccountingModel> {

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'accounts');
        this.localHttpClient = httpClient;
    }

    /**
     *
     * @param item
     */
    public getDepositAccountStatement(item: any): Observable<AccountingModel> {
        const itemUrl = 'member';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param item
     */
    public downloadMemberAccountStatement(item: any): Observable<any> {
        const itemUrl = 'member';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }

    /**
     *
     * @param item
     */
    public getLeaseAccountStatement(item: any): Observable<AccountingModel> {
        const itemUrl = 'lease';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param item
     */
    public downloadLeaseAccountStatement(item: any): Observable<any> {
        const itemUrl = 'lease';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }

    /**
     *
     * @param item
     */
    public getGeneralAccountStatement(item: any): Observable<AccountingModel> {
        const itemUrl = 'general';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param item
     */
    public downloadGeneralAccountStatement(item: any): Observable<any> {
        const itemUrl = 'general';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }
}
