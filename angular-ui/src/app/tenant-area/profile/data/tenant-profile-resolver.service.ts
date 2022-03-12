import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { TenantProfileService } from './tenant-profile-service';

@Injectable({ providedIn: 'root' })
export class TenantProfileResolverService implements Resolve<boolean> {

    constructor(private tenantProfileService: TenantProfileService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> {
        return this.tenantProfileService.getAll('', 1, 1);
    }
}
