import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { LandlordSummaryModel } from '../model/landlord-summary-model';
import { LandlordSummaryService } from './landlord-summary.service';

@Injectable({ providedIn: 'root' })
export class LandlordDashResolverService implements Resolve<LandlordSummaryModel> {

    constructor(private service: LandlordSummaryService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | LandlordSummaryModel {

        return this.service.getAll('', 1, 1);
    }
}
