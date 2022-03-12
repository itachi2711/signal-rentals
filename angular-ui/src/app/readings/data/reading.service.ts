import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ReadingModel } from '../models/reading-model';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ReadingService extends BaseService<ReadingModel> {
    private selectedReadingSource = new BehaviorSubject<ReadingModel | null>(null);
    selectedReadingChanges$ = this.selectedReadingSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'readings');
        this.localHttpClient = httpClient;
    }

    changeSelectedReading(selectedReading: ReadingModel | null ): void {
        this.selectedReadingSource.next(selectedReading);
    }

    /**
     *
     * @param item
     */
    public previousReading(item: any): Observable<any> {
        const itemUrl = 'previous';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }

    /**
     * @param item
     */
    public csvTemplate(item: any) {
        const itemUrl = 'csv_template';
      //  return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }

    /**
     * @param item
     */
    public excelTemplate(item: any) {
        const itemUrl = 'excel_template';
       // return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item, { responseType: 'blob' as 'json'});
    }

    /**
     * Create a new resource
     * @param item
     */
    public uploadReadings(item: any): Observable<ReadingModel> {
        const itemUrl = 'upload_readings';
        return this.localHttpClient.post<any>(`${super.getResourceUrl()}/${itemUrl}`, item);
    }
}
