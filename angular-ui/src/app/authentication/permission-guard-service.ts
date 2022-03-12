import { CanLoad, Route, Router } from '@angular/router';
import { Injectable } from '@angular/core';
import { select, Store } from '@ngrx/store';
import { AppState } from '../reducers';
import { selectorUserScopes } from './authentication.selectors';

@Injectable({ providedIn: 'root' })
export class PermissionGuardService implements CanLoad  {

    private userScopes;
    private permissions = [];

    constructor(private store: Store<AppState>, private router: Router) { }

    canLoad(route: Route): boolean {
        // permissions will be passed from the route config on the data property
        this.permissions = route.data.permissions;

        // Fetch all scopes from redux store
        this.store.pipe(select(selectorUserScopes)).subscribe(scopes => {
            this.userScopes = scopes;
        });

        if (this.checkPermission()) {
            return true;
        }
       // this.store.dispatch(AuthActions.actionLogout());
        return false;
    }


    /**
     *
     */
    private checkPermission() {
        let hasPermission = false;
        if (this.userScopes && this.permissions !== undefined ) {
            for (const checkPermission of this.permissions) {
                const permissionFound = this.userScopes.find(x => x.toUpperCase() === checkPermission.toUpperCase());
                if (permissionFound) {
                    hasPermission = true;
                }
            }
        }

        return hasPermission;
    }

}
