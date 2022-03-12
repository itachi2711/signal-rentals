import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { TenantGeneralSettingModel } from '../model/tenant-general-setting.model';
import { TenantGeneralService } from './tenant-general.service';

@Injectable({ providedIn: 'root' })
export class TenantGeneralSettingResolverService implements Resolve<TenantGeneralSettingModel> {

    constructor(private service: TenantGeneralService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | TenantGeneralSettingModel {

        return this.service.getAll('', 1, 1);
    }
}
