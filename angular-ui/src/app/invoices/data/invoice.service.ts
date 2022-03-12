import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { InvoiceModel } from '../models/invoice-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class InvoiceService extends BaseService<InvoiceModel> {
    private selectedInvoiceSource = new BehaviorSubject<InvoiceModel | null>(null);
    selectedInvoiceChanges$ = this.selectedInvoiceSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'invoices');
        this.localHttpClient = httpClient;
    }

    changeSelectedInvoice(selectedInvoice: InvoiceModel | null ): void {
        this.selectedInvoiceSource.next(selectedInvoice);
    }

    /**
     *
     * @param item
     */
    public downloadInvoice(item: any): Observable<any> {
        const itemUrl = 'download';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<InvoiceModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
    }
}
