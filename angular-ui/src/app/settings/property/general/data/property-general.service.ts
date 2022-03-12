import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../../../shared/base-service';
import { PropertyGeneralSettingModel } from '../model/property-general-setting.model';

@Injectable({ providedIn: 'root' })
export class PropertyGeneralService extends BaseService<PropertyGeneralSettingModel> {
    private selectedPropertyGeneralSource = new BehaviorSubject<PropertyGeneralSettingModel | null>(null);
    selectedPropertyGeneralChanges$ = this.selectedPropertyGeneralSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'property_settings');
        this.localHttpClient = httpClient;
    }

    changeSelectedPropertyGeneral(selectedPropertyGeneral: PropertyGeneralSettingModel | null ): void {
        this.selectedPropertyGeneralSource.next(selectedPropertyGeneral);
    }
}
