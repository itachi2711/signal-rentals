import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { LeaseModel } from '../models/lease-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class LeaseService extends BaseService<LeaseModel> {
    private selectedLeaseSource = new BehaviorSubject<LeaseModel | null>(null);
    selectedLeaseChanges$ = this.selectedLeaseSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'leases');
        this.localHttpClient = httpClient;
    }

    changeSelectedLease(selectedLease: LeaseModel | null ): void {
        this.selectedLeaseSource.next(selectedLease);
    }

    /**
     * Create a new resource
     * @param item
     */
    public terminate(item: any): Observable<LeaseModel> {
        const endPoint = 'terminate';
        const url =  `${super.getResourceUrl()}/${endPoint}`;
        return this.localHttpClient.post<any>(url, item);
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<LeaseModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
    }

    /**
     *
     * @param file_path
     */
    getImage(file_path: any): Observable<File> {

        const imageUrl = 'profile_pic';

        const url =  `${super.getResourceUrl()}/${imageUrl}`;

        return this.localHttpClient.post<any>(url, {file_path}, { responseType: 'blob' as 'json'});
    }

    getImagePath(file_path: any): any {

        const imageUrl = 'profile_pic';

        const url =  `${super.getResourceUrl()}/${imageUrl}`;

        return this.localHttpClient.post<any>(url, {file_path}, {});
    }

    /**
     *
     * @param file_path
     */
    public fetchTenantshipForm(file_path: any): Observable<any> {
        const imageUrl = 'membership_form';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {file_path}, { responseType: 'blob' as 'json'});
    }

    /**
     * Create a new resource
     * @param item
     */
    public uploadPhoto(item: any): any {
        const itemUrl = 'upload_photo';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param file_path
     */
    public fetchPhoto(file_path: any): Observable<File> {
        const imageUrl = 'fetch_photo';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {file_path}, { responseType: 'blob' as 'json'});
    }
}
