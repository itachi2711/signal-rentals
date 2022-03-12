import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from '@angular/router';
import { select, Store } from '@ngrx/store';
import { AppState } from '../reducers';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { selectorIsAgent } from './authentication.selectors';
import { AuthenticationActions } from './action-types';

@Injectable({ providedIn: 'root' })
export class AuthGuardAdmin implements CanActivate {

    constructor(private store: Store<AppState>, private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {

        return this.store
            .pipe(
                select(selectorIsAgent),
                tap(isAgent => {
                    if (!isAgent) {
                        this.store.dispatch(AuthenticationActions.actionLogout());
                        this.router.navigate(['/login']);
                    }
                })
            );
    }
}
