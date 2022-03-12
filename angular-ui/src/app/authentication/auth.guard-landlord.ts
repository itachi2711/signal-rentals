import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from '@angular/router';
import { select, Store } from '@ngrx/store';
import { AppState } from '../reducers';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { selectorIsLandlord } from './authentication.selectors';
import { AuthenticationActions } from './action-types';

@Injectable({ providedIn: 'root' })
export class AuthGuardLandlord implements CanActivate {

    constructor(private store: Store<AppState>, private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {

        return this.store
            .pipe(
                select(selectorIsLandlord),
                tap(isLandlord => {
                    if (!isLandlord) {
                        this.store.dispatch(AuthenticationActions.actionLogout());
                        this.router.navigate(['/login']);
                    }
                })
            );
    }
}
