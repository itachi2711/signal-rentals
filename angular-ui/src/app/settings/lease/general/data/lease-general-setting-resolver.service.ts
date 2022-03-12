import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { LeaseGeneralSettingModel } from '../model/lease-general-setting.model';
import { LeaseSettingService } from './lease-setting.service';

@Injectable({ providedIn: 'root' })
export class LeaseGeneralSettingResolverService implements Resolve<LeaseGeneralSettingModel> {

    constructor(private service: LeaseSettingService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | LeaseGeneralSettingModel {

        return this.service.getAll('', 1, 1);
    }
}
