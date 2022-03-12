import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../../shared/base-service';
import { UserSettingModel } from '../model/user-setting.model';
import { Observable } from 'rxjs';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../../reducers';
import { selectorIsAgent, selectorIsLandlord, selectorIsTenant, selectorUserID } from '../../../authentication/authentication.selectors';
import { USER_SCOPES } from '../../../shared/enums/user-scopes.enum';

@Injectable({ providedIn: 'root' })
export class UserSettingService extends BaseService<UserSettingModel> {
    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient, private store: Store<AppState>) {
        super( httpClient, 'users');
        this.localHttpClient = httpClient;
    }

    /**
     * Will show whether we have an agent, landlord or tenant user
     */
    getActiveUser() {
        let landlord = false;
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => landlord = isLandlord);

        let tenant = false;
        this.store.pipe(select(selectorIsTenant)).subscribe(isTenant => tenant = isTenant);

        let admin = false;
        this.store.pipe(select(selectorIsAgent)).subscribe(isAdmin => admin = isAdmin);

        let userType: string;

        if (admin) {
            userType = USER_SCOPES.ADMIN
        } else if (landlord) {
            userType = USER_SCOPES.LANDLORD
        } else if (tenant) {
            userType = USER_SCOPES.TENANT
        }

        let userID = '';
        this.store.pipe(select(selectorUserID)).subscribe(id => userID = id);

        return {
            userType: userType,
            userID: userID
        };
    }

    /**
     *
     * @param email
     */
    public forgotPassword(email: any): Observable<any> {
        const itemUrl = 'forgot_password';
        return this.localHttpClient.post<any>(`${super.getApiUrl()}/${itemUrl}`, email);
    }

    /**
     *
     * @param item
     */
    public resetPassword(item: any): Observable<any> {
        const itemUrl = 'reset_password';
        return this.localHttpClient.post<any>(`${super.getApiUrl()}/${itemUrl}`, item);
    }
}
