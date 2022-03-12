import { Injectable } from '@angular/core';
import { Actions, createEffect, Effect, ofType } from '@ngrx/effects';
import { tap } from 'rxjs/operators';
import { Router } from '@angular/router';
import { defer, of } from 'rxjs';
import { LocalStorageService } from '../core/local-storage/local-storage.service';
import { AuthActions } from './action-types';

export const AUTH_KEY = 'AUTH';

@Injectable()
export class AuthEffects {

    storageKey = 'xbe3295963d1091720c8513f78f83c216332190ff714a5239c8b49190443be288';

    login = createEffect(
        () =>
            this.actions$.pipe(
                ofType(AuthActions.actionLogin),
                tap((action) =>
                    this.localStorageService.setItem(AUTH_KEY, action.user)
                )
            ),
        { dispatch: false }
    );

    logout = createEffect(
        () =>
            this.actions$.pipe(
                ofType(AuthActions.actionLogout),
                tap(() => {
                    this.localStorageService.setItem(AUTH_KEY, false);
                    this.router.navigate(['/login']);
                })
            ),
        { dispatch: false }
    );

    @Effect()
    init$ = defer(() => {
        const user = this.localStorageService.getItem(AUTH_KEY);
        console.log('authData', user);
        if (user) {
            return of(AuthActions.actionLogin({user: user}));
        } else {
            // return of(new Logout());
        }
    });

    /*  @Effect({dispatch: false})
      login$ = this.actions$.pipe(
          ofType<Login>(AuthActionTypes.LoginAction),
          tap(action => localStorage.setItem(this.storageKey, JSON.stringify(action.payload.user)))
      );

      @Effect({dispatch: false})
      logout$ = this.actions$.pipe(
          ofType<Logout>(AuthActionTypes.LogoutAction),
          tap(() => {
            localStorage.removeItem(this.storageKey);
            this.router.navigateByUrl('/login');
          })
      );

      @Effect()
      init$ = defer(() => {
          const userData = JSON.parse(localStorage.getItem(this.storageKey));
          if (userData) {
            return of(new Login({user: userData}));
          } else {
           // return of(new Logout());
          }
      });*/

    constructor(
        private actions$: Actions,
        private localStorageService: LocalStorageService,
        private router: Router
    ) {}

}
