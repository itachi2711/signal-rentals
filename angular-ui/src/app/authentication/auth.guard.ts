import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from '@angular/router';
import { select, Store } from '@ngrx/store';
import { AppState } from '../reducers';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { selectorIsUserLoggedIn } from './authentication.selectors';

@Injectable({ providedIn: 'root' })
export class AuthGuard implements CanActivate {

    constructor(private store: Store<AppState>, private router: Router) { }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> {

        return this.store
            .pipe(
                select(selectorIsUserLoggedIn),
                tap(loggedIn => {
                    if (!loggedIn) {
                      //  this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
                        this.router.navigate(['/login']);
                    }
                })
            );
    }
}
