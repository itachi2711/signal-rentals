import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { PropertyGeneralSettingModel } from '../model/property-general-setting.model';
import { PropertyGeneralService } from './property-general.service';

@Injectable({ providedIn: 'root' })
export class PropertyGeneralSettingResolverService implements Resolve<PropertyGeneralSettingModel> {

    constructor(private service: PropertyGeneralService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | PropertyGeneralSettingModel {

        return this.service.getAll('', 1, 1);
    }
}
