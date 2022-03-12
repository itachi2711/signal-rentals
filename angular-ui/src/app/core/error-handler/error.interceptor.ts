import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { Store } from '@ngrx/store';
import { NotificationService } from '../../shared/notification.service';
import { AppState } from '../core.state';
import { AuthActions } from '../../authentication/action-types';

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
    constructor(private store: Store<AppState>, private notification: NotificationService) {}

    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        return next.handle(request).pipe(
            catchError((err) => {
            if (err.status === 400) {
            }
            if (err.status === 401) {
                this.store.dispatch(AuthActions.actionLogout());
            }
            if (err.status === 429) {
                this.notification.showNotification('warning', 'Too many server requests in the last minute.' +
                    ' Wait a moment before retying.');
            }
            if (err.status === 403) {
                this.notification.showNotification('warning', 'You do not have permission to access this resource..');
            }
            if (err.status === 500) {
                    return throwError(err);
            }
            if (err.status === 422) {
                const errorData = err?.error?.errors;
                return throwError(errorData);
            }
            return throwError(err);

        }));
    }
}
