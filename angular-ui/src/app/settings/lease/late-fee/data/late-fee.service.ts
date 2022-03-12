import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { LateFeeModel } from '../model/late-fee-model';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class LateFeeService extends BaseService<LateFeeModel> {
    constructor(httpClient: HttpClient) {
        super( httpClient, 'late_fees');
    }
}
