import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { TenantSummaryModel } from '../model/tenant-summary-model';
import { TenantSummaryService } from './tenant-summary.service';

@Injectable({ providedIn: 'root' })
export class TenantDashResolverService implements Resolve<TenantSummaryModel> {

    constructor(private service: TenantSummaryService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | TenantSummaryModel {

        return this.service.getAll('', 1, 1);
    }
}
