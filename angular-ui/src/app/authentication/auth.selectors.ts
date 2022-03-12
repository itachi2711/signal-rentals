import { createSelector } from '@ngrx/store';
import * as jwt_decode from 'jwt-decode';

export const selectAuthState = state => state.auth;

export const selectorAccessToken = createSelector(
    selectAuthState,
    auth => auth['access_token']
);

export const selectorIsLoggedIn = createSelector(
    selectAuthState,
    auth => !!auth.user?.access_token
);

export const selectorUserId = createSelector(
    selectAuthState,
    auth => {
        if (auth.user) {
            return jwt_decode(auth?.user?.access_token)?.sub;
        }
    }
);

export const selectorScopes = createSelector(
    selectAuthState,
    auth => {
        if (auth.user) {
            return jwt_decode(auth?.user?.access_token)?.scopes;
          //  return auth.user?.access_token;
        }
    }
);

export const settings = createSelector(
    selectAuthState,
    auth => {
        if (auth.user) {
            return auth.user.settings;
        }
    }
);

export const generalSettings = createSelector(
    selectAuthState,
    auth => {
        if (auth) {
            return auth?.g_settings;
        }
    }
);

export const loggedInUserNames = createSelector(
    selectAuthState,
    auth => {
        if (auth) {
            return auth?.first_name + ' ' + auth?.last_name;
        }
    }
);

export const activeUser = createSelector(
    selectAuthState,
    auth => {
        if (auth.user) {
            return auth.user.first_name + ' ' + auth.user.last_name;
        }
    }
);
