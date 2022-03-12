import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject } from 'rxjs';
import { WaiverModel } from '../models/waiver-model';

@Injectable({ providedIn: 'root' })
export class WaiverService extends BaseService<WaiverModel> {
    private selectedWaiverSource = new BehaviorSubject<WaiverModel | null>(null);
    selectedWaiverChanges$ = this.selectedWaiverSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'waivers');
        this.localHttpClient = httpClient;
    }

    changeSelectedWaiver(selectedWaiver: WaiverModel | null ): void {
        this.selectedWaiverSource.next(selectedWaiver);
    }
}
