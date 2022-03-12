import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { LandlordModel } from '../models/landlord-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { selectorIsLandlord, selectorUserID } from '../../authentication/authentication.selectors';

@Injectable({ providedIn: 'root' })
export class LandlordService extends BaseService<LandlordModel> {
    private selectedLandlordSource = new BehaviorSubject<LandlordModel | null>(null);
    selectedLandlordChanges$ = this.selectedLandlordSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient, private store: Store<AppState>) {
        super( httpClient, 'landlords');
        this.localHttpClient = httpClient;
    }

    changeSelectedLandlord(selectedLandlord: LandlordModel | null ): void {
        this.selectedLandlordSource.next(selectedLandlord);
    }

    /**
     * Check if logged in user is a landlord
     */
    isLandlord(): boolean {
        let landlord = false;
        this.store.pipe(select(selectorIsLandlord)).subscribe(isLandlord => landlord = isLandlord);
        return landlord;
    }

    /**
     * Give ID of currently logged in landlord
     */
    getLoggedInLandlordID(): string | null {
        let ID = null;
        if (this.isLandlord()) {
            this.store.pipe(select(selectorUserID)).subscribe(userID => ID = userID);
        }
        return ID;
    }

    /**
     *
     * @param item
     */
    search(item: any): Observable<any> {
        const imageUrl = 'search';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {filter: item});
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<LandlordModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
    }
}
