import { NgModule } from '@angular/core';

import { AuthenticationRoutingModule } from './authentication-routing.module';
import { LoginComponent } from './login/login.component';
import { ForgotPasswordComponent } from './forgot-password/forgot-password.component';
import { ConfirmEmailComponent } from './confirm-email/confirm-email.component';
import { Error404Component } from './404/error-404.component';
import { Error500Component } from './500/error-500.component';
import { StoreModule } from '@ngrx/store';
import * as fromAuth from './auth.reducer';
import * as fromAuthentication from './authentication.reducer';
import { EffectsModule } from '@ngrx/effects';
import { AuthEffects } from './auth.effects';
import { ResetPasswordComponent } from './reset-password/reset-password.component';
import { SharedModule } from '../shared/shared.module';

@NgModule({
    imports: [
        AuthenticationRoutingModule,
        SharedModule,
        StoreModule.forFeature('auth', fromAuth.authReducer),
        StoreModule.forFeature('authentication', fromAuthentication.authenticationReducer),
        EffectsModule.forFeature([AuthEffects])
    ],
    declarations: [
        LoginComponent,
        ForgotPasswordComponent,
        ResetPasswordComponent,
        ConfirmEmailComponent,
        Error404Component,
        Error500Component
    ],
    providers: [],
})

export class AuthenticationModule {}
