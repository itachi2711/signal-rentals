import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ExtraChargeModel } from '../model/extra-charge-model';
import { BaseService } from '../../../../shared/base-service';

@Injectable({ providedIn: 'root' })
export class ExtraChargeService extends BaseService<ExtraChargeModel> {
    constructor(httpClient: HttpClient) {
        super( httpClient, 'extra_charges');
    }
}
