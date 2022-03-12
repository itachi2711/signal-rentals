import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BehaviorSubject } from 'rxjs';
import { BaseService } from '../../shared/base-service';
import { UserProfileModel } from '../model/user-profile.model';

@Injectable({ providedIn: 'root' })
export class UserProfileService extends BaseService<UserProfileModel> {
    private selectedUserProfileSource = new BehaviorSubject<UserProfileModel | null>(null);
    selectedUserProfileChanges$ = this.selectedUserProfileSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'user_profile');
        this.localHttpClient = httpClient;
    }

    changeSelectedUserProfile(selectedUserProfile: UserProfileModel | null ): void {
        this.selectedUserProfileSource.next(selectedUserProfile);
    }
}
