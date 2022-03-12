import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { InvoiceModel } from '../models/invoice-model';
import { BaseService } from '../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class PeriodService extends BaseService<InvoiceModel> {

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'periods');
        this.localHttpClient = httpClient;
    }
}
