import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject, Observable } from 'rxjs';
import { DocumentModel } from '../model/document-model';

@Injectable({ providedIn: 'root' })
export class DocumentService extends BaseService<DocumentModel> {
    private selectedDocumentSource = new BehaviorSubject<DocumentModel | null>(null);
    selectedDocumentChanges$ = this.selectedDocumentSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'documents');
        this.localHttpClient = httpClient;
    }

    changeSelectedDocument(selectedDocument: DocumentModel | null ): void {
        this.selectedDocumentSource.next(selectedDocument);
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
}
