import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { PropertyService } from '../../properties/data/property.service';

@Injectable({ providedIn: 'root' })
export class CreateLeaseResolverService implements Resolve<boolean> {

    constructor(private propertyService: PropertyService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> {

        return this.propertyService.list(['property_code', 'property_name', 'location']);
    }
}
