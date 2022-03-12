import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { AppRoutingModule } from './app.routing';
import { ComponentsModule } from './components/components.module';
import { AppComponent } from './app.component';
import { AdminLayoutComponent } from './layouts/admin-layout/admin-layout.component';
import { HTTP_INTERCEPTORS, HttpClient } from '@angular/common/http';
import { TranslateHttpLoader } from '@ngx-translate/http-loader';
import { StoreDevtoolsModule } from '@ngrx/store-devtools';
import { environment } from '../environments/environment';
import { CoreModule } from './core/core.module';
import { SharedModule } from './shared/shared.module';
import { ConfirmationDialogComponent } from './shared/delete/confirmation-dialog-component';
import { MAT_DATE_LOCALE } from '@angular/material/core';
import {
  DefaultDataServiceConfig,
  EntityDataModule,
  HttpUrlGenerator,
} from '@ngrx/data';
import { JwtInterceptor } from './core/jwt.interceptor';
import { AppHttpUrlGenerator } from './core/app-http-url-generator';
import { ErrorInterceptor } from './core/error-handler/error.interceptor';
import { AuthenticationModule } from './authentication/authentication.module';
import { STEPPER_GLOBAL_OPTIONS } from '@angular/cdk/stepper';
import { StatementComponent } from './accounting/statement/statement.component';
import { PdfStatementComponent } from './accounting/pdf-statement/pdf-statement.component';

export function createTranslateLoader(http: HttpClient) {
  return new TranslateHttpLoader(http);
}

export const defaultDataServiceConfig: DefaultDataServiceConfig = {
  root: 'http://localhost/2020/Rental/api/public/api/v1',
  timeout: 1000 * 60,
};

@NgModule({
  imports: [
    BrowserAnimationsModule,
    SharedModule,
    CoreModule,
    ComponentsModule,
    RouterModule,
    AppRoutingModule,
    AuthenticationModule,
    StoreDevtoolsModule.instrument({ maxAge: 25, logOnly: environment.production }),
    EntityDataModule.forRoot({})
  ],
  declarations: [
    AppComponent,
    AdminLayoutComponent,
    ConfirmationDialogComponent,
    StatementComponent,
    PdfStatementComponent
  ],
  providers: [
    {
      provide: STEPPER_GLOBAL_OPTIONS,
      useValue: { showError: true }
    },
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    { provide: HTTP_INTERCEPTORS, useClass: ErrorInterceptor, multi: true },
    { provide: HttpUrlGenerator, useClass: AppHttpUrlGenerator },
    {provide: MAT_DATE_LOCALE, useValue: 'en-GB'},
    { provide: DefaultDataServiceConfig, useValue: defaultDataServiceConfig }
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor () {
  }
}
