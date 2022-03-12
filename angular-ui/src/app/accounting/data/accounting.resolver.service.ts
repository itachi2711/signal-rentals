import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { AccountingModel } from '../models/accounting-model';
import { AccountingService } from './accounting.service';
import { AccountingDataSource } from './accounting-data.source';

@Injectable({ providedIn: 'root' })
export class AccountingResolverService implements Resolve<AccountingModel> {

    dataSource: AccountingDataSource;

    constructor(private service: AccountingService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | AccountingModel {

        return this.service.getAll('', 0, 0);
    }
}
