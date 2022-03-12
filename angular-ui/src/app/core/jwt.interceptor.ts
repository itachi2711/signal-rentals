import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Store } from '@ngrx/store';
import { LocalStorageService } from './local-storage/local-storage.service';
import { AUTH_KEY } from '../authentication/auth.effects';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {

    storageKey = 'be3295963d1091720c8513f78f83c216332190ff714a5239c8b49190443be288';
    token: string;
    constructor( private store: Store, private localStorageService: LocalStorageService) {}
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        const userData = this.localStorageService.getItem(AUTH_KEY);
            request = request.clone({
                setHeaders: {
                    Authorization: `Bearer ${userData?.access_token}`
                }
            });

        return next.handle(request);
    }
}
