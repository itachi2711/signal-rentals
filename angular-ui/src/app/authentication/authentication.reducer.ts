import { User } from './model/user.model';
import { createReducer, on } from '@ngrx/store';
import { AuthenticationActions } from './action-types';

export interface AuthenticationState {
    user: User
}

export const initialAuthenticationState: AuthenticationState = {
    user: undefined
};

export const authenticationReducer = createReducer(
    initialAuthenticationState,
    on(AuthenticationActions.actionLogin, (state, action) => {
        return {
            user: action.user
        }
    }),
    on(AuthenticationActions.actionLogout, state => {
        return {
            user: undefined
        }
    })
);
