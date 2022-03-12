import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { LandlordProfileService } from './landlord-profile-service';

@Injectable({ providedIn: 'root' })
export class LandlordProfileResolverService implements Resolve<boolean> {

    constructor(private landlordProfileService: LandlordProfileService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> {
        return this.landlordProfileService.getAll('', 1, 1);
    }
}
