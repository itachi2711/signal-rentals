import { createAction, props } from '@ngrx/store';
import { User } from './model/user.model';

export const actionLogin = createAction(
   '[Login Page] Login Action',
    props<{user: User}>()
);

export const actionSettings = createAction(
   '[Login Page] Settings Action',
    props<{user: User}>()
);

export const actionLogout = createAction(
    '[Side Menu] Logout Action'
);
