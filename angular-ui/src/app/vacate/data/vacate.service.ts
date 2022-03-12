import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { VacateModel } from '../models/vacate-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class VacateService extends BaseService<VacateModel> {
    private selectedVacateNoticeSource = new BehaviorSubject<VacateModel | null>(null);
    selectedVacateNoticeChanges$ = this.selectedVacateNoticeSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'vacation_notices');
        this.localHttpClient = httpClient;
    }

    changeSelectedVacateNotice(selectedVacateNotice: VacateModel | null ): void {
        this.selectedVacateNoticeSource.next(selectedVacateNotice);
    }
}
