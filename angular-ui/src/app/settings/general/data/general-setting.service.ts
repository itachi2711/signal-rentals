import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../../shared/base-service';
import { GeneralSettingModel } from '../model/general-setting.model';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class GeneralSettingService extends BaseService<GeneralSettingModel> {
    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'general_settings');
        this.localHttpClient = httpClient;
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: any): Observable<GeneralSettingModel> {
        return this.localHttpClient.post<any>(super.getResourceUrl(), item);
    }

    /**
     * Create a new resource
     * @param item
     */
    public update(item: any): Observable<GeneralSettingModel> {
        return this.localHttpClient.put<any>(super.getItemUrl(item.id), item);
    }

    /**
     * Create a new resource
     * @param item
     */
    public updateLogo(item: any): Observable<GeneralSettingModel> {
        const itemUrl = 'upload_logo';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     * @param file_path
     */
    public fetchLogo(file_path: any): Observable<File> {
        const imageUrl = 'fetch_logo';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {file_path}, { responseType: 'blob' as 'json'});
    }



    /**
     * Create a new resource
     * @param item
     */
    public updatePhoto(item: any): Observable<any> {
        const itemUrl = 'upload_logo';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     *
     */
    public fetchPhoto(id: string): Observable<File> {
        const imageUrl = 'fetch_logo';
        const url =  `${super.getResourceUrl()}/${imageUrl}`;
        return this.localHttpClient.post<any>(url, {id}, { responseType: 'blob' as 'json'});
    }

}
