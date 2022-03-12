import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { UserProfileService } from './user-profile-service';

@Injectable({ providedIn: 'root' })
export class UserProfileResolverService implements Resolve<boolean> {

    constructor(private userProfileService: UserProfileService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> {
        return this.userProfileService.getAll('', 1, 1);
    }
}
