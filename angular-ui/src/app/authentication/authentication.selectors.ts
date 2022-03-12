import { createSelector } from '@ngrx/store';
import * as jwt_decode from 'jwt-decode';
import { USER_SCOPES } from '../shared/enums/user-scopes.enum';

export const selectAuthenticationState = state => state.authentication;

export const selectorAuthenticatedUser = createSelector(
    selectAuthenticationState,
    state => state.user
);

export const selectorUserAccessToken = createSelector(
    selectorAuthenticatedUser,
    user => user?.access_token
);

export const selectorIsUserLoggedIn = createSelector(
    selectorUserAccessToken,
    access_token => !!access_token
);

export const selectorUserID = createSelector(
    selectorUserAccessToken,
    access_token => {
        if (access_token) {
            return jwt_decode(access_token)?.sub;
        }
    }
);

export const selectorUserFirstAndLastNames = createSelector(
    selectorAuthenticatedUser,
    user => {
        if (user) {
            return user?.first_name + ' ' + user?.last_name;
        }
    }
);

export const selectorUserScopes = createSelector(
    selectorUserAccessToken,
    access_token => {
        if (access_token) {
            return jwt_decode(access_token)?.scopes;
        }
    }
);

export const selectorIsAgent = createSelector(
    selectorUserAccessToken,
    access_token => {
        if (access_token) {
            const landlordTenantPermissions = [USER_SCOPES.LANDLORD, USER_SCOPES.TENANT];
            const scopes = jwt_decode(access_token)?.scopes;
            return !landlordTenantPermissions.some( perm => scopes.includes(perm) );
        }
    }
);

export const selectorIsLandlord = createSelector(
    selectorUserAccessToken,
    access_token => {
        if (access_token) {
            const scopes = jwt_decode(access_token)?.scopes;
            return scopes.includes('am-landlord');
        }
    }
);

export const selectorIsTenant = createSelector(
    selectorUserAccessToken,
    access_token => {
        if (access_token) {
            const scopes = jwt_decode(access_token)?.scopes;
            return scopes.includes('am-tenant');
        }
    }
);

export const selectorUserGeneralSettings = createSelector(
    selectorAuthenticatedUser,
    user => {
        if (user) {
            return user?.g_settings;
        }
    }
);

export const selectorCompanyName = createSelector(
    selectorAuthenticatedUser,
    user => {
        if (user) {
            return user?.g_settings?.company_name;
        }
    }
);
