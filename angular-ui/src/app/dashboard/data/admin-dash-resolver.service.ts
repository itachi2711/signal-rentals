import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { AdminSummaryModel } from '../model/admin-summary-model';
import { AdminSummaryService } from './admin-summary.service';

@Injectable({ providedIn: 'root' })
export class AdminDashResolverService implements Resolve<AdminSummaryModel> {

    constructor(private service: AdminSummaryService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | AdminSummaryModel {

        return this.service.getAll('', 1, 1);
    }
}
