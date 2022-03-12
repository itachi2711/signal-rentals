import { Component, OnInit } from '@angular/core';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../authentication.service';
import { tap } from 'rxjs/operators';
import { Observable, of } from 'rxjs';
import { selectEffectiveTheme } from '../../core/settings/settings.selectors';
import { AuthenticationActions } from '../action-types';
import { selectorIsLoggedIn, selectorScopes } from '../auth.selectors';
import { selectorCompanyName, selectorUserScopes } from '../authentication.selectors';

@Component({templateUrl: 'login.component.html'})
export class LoginComponent implements OnInit {

    loginForm: FormGroup;
    returnUrl: string;
    loginError = '';
    loader = false;
    token$: any;

    theme$: Observable<string>;

    loginScopes: any;
    companyName: string;
    constructor(private fb: FormBuilder, private store: Store, private route: ActivatedRoute,
                private router: Router, private authenticationService: AuthenticationService) {
        this.loginForm = fb.group({
            email: ['', [Validators.required, Validators.email]],
            password: ['', Validators.required]
        });
        this.store.pipe(select(selectorCompanyName)).subscribe(name => this.companyName = name);
    }

    ngOnInit() {
        this.theme$ = this.store.pipe(select(selectEffectiveTheme));

        this.store.dispatch(AuthenticationActions.actionLogout());
        this.returnUrl = '/';
    }

    /**
     * Fetch email field
     */
    get email() {
        return this.loginForm.get('email');
    }

    /**
     * Fetch password field
     */
    get password() {
        return this.loginForm.get('password');
    }

    /**
     * Login user against api
     */
    login() {
        // this.loginError = '';
        // this.loader = true;

        // this.authenticationService.login(this.email.value, this.password.value)
        //     .pipe(tap(
        //         user => {
        //             this.loader = false;
        //          //   console.log('AuthActions.actionLogin({user}');
        //           //  console.log(user);

        //             this.store.dispatch(AuthenticationActions.actionLogin({user}));

        //             this.store.pipe(select(selectorUserScopes)).subscribe(scopes => {
        //                 this.loginScopes = scopes;
        //                 // We have a landlord
        //                 if (scopes?.find(x => x === 'am-landlord')) {
                            this.returnUrl = '/landlord/dashboard';
                        // }
                        // // We have a tenant
                        // if (scopes?.find(x => x === 'am-tenant')) {
                            // this.returnUrl = '/tenant/dashboard';
            //             } else {
            //                 //
            //             }
            //         });

                    this.router.navigate([this.returnUrl]);
            //     }
            // ))
            // .subscribe(
            //     () => {},
            //     (error) => {
            //        // console.log(error);
            //         if (error.error.message) {
            //             this.loginError = error.error.message;
            //         } else {
            //             this.loginError = 'Server Error. Please try again later.';
            //         }
            //         this.loader = false;
            //     });
    }
}
