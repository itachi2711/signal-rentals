import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { FinanceStatementModel } from '../models/finance-statement.model';
import { BaseService } from '../../shared/base-service';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class FinanceStatementService extends BaseService<FinanceStatementModel> {

    private  localHttpClient: HttpClient;

    constructor(httpClient: HttpClient) {
        super( httpClient, 'finance_statements');
        this.localHttpClient = httpClient;
    }

    /**
     *
     * @param item
     */
    public downloadReport(item: any): Observable<any> {
        const itemUrl = 'report';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }
}
