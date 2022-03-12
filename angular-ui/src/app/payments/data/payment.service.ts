import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { PaymentModel } from '../models/payment-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PaymentService extends BaseService<PaymentModel> {
    private selectedPaymentSource = new BehaviorSubject<PaymentModel | null>(null);
    selectedPaymentChanges$ = this.selectedPaymentSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'payments');
        this.localHttpClient = httpClient;
    }

    changeSelectedPayment(selectedPayment: PaymentModel | null ): void {
        this.selectedPaymentSource.next(selectedPayment);
    }

    /**
     * Create a new resource
     * @param item
     */
    public approve(item: any): Observable<PaymentModel> {
        const itemUrl = 'approve';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     * Create a new resource
     * @param item
     */
    public cancel(item: any): Observable<PaymentModel> {
        const itemUrl = 'cancel';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param item
     */
    public downloadReceipt(item: any): Observable<any> {
        const itemUrl = 'receipt';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }
}
