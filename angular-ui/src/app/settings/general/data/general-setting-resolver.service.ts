import { Injectable } from '@angular/core';
import { GeneralSettingModel } from '../model/general-setting.model';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { GeneralSettingService } from './general-setting.service';

@Injectable({ providedIn: 'root' })
export class GeneralSettingResolverService implements Resolve<GeneralSettingModel> {

    constructor(private service: GeneralSettingService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | GeneralSettingModel {

        return this.service.getAll('', 1, 1);
    }
}
