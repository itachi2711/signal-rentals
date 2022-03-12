import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { EmailConfigSettingModel } from '../model/email-config-setting.model';
import { EmailConfigService } from './email-config.service';

@Injectable({ providedIn: 'root' })
export class EmailConfigSettingResolverService implements Resolve<EmailConfigSettingModel> {

    constructor(private service: EmailConfigService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | EmailConfigSettingModel {

        return this.service.getAll('', 1, 1);
    }
}
