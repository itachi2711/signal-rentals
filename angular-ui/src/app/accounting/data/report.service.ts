import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AccountingModel } from '../models/accounting-model';
import { BaseService } from '../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class ReportService extends BaseService<AccountingModel> {
    constructor(httpClient: HttpClient) {
        super( httpClient, 'reports');
    }
}
