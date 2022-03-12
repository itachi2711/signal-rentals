import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { LandlordService } from '../../landlords/data/landlord.service';

@Injectable({ providedIn: 'root' })
export class CreatePropertyResolverService implements Resolve<boolean> {

    constructor(private landlordService: LandlordService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> {

       return this.landlordService.list(['first_name', 'middle_name', 'last_name']);
    }
}
