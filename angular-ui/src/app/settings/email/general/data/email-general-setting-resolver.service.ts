import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';
import { EmailGeneralSettingModel } from '../model/email-general-setting.model';
import { EmailGeneralService } from './email-general.service';

@Injectable({ providedIn: 'root' })
export class EmailGeneralSettingResolverService implements Resolve<EmailGeneralSettingModel> {

    constructor(private service: EmailGeneralService) {}

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<any> | EmailGeneralSettingModel {

        return this.service.getAll('', 1, 10);
    }
}
