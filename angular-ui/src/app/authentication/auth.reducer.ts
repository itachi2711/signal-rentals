import { User } from './model/user.model';
import { createReducer, on } from '@ngrx/store';
import { AuthActions } from './action-types';

export interface AuthState {
  user: User
}

export const initialAuthState: AuthState = {
  user: undefined
};

export const authReducer = createReducer(
    initialAuthState,
    on(AuthActions.actionLogin, (state, action) => {
      return {
        user: action.user
      }
    }),
    on(AuthActions.actionSettings, (state, action) => {
        return {
            user: action.user
        }
    }),
    on(AuthActions.actionLogout, state => {
        return {
            user: undefined
        }
    })
);
