import { CollectionViewer, DataSource } from '@angular/cdk/collections';
import { BehaviorSubject, Observable, of } from 'rxjs';
import { catchError, finalize  } from 'rxjs/operators';

export class BaseDataSource implements DataSource<any> {

    private dataSubject = new BehaviorSubject<any[]>([]);

    private loadingSubject = new BehaviorSubject<boolean>(false);
    public loading$ = this.loadingSubject.asObservable();

    private metaSubject = new BehaviorSubject({});
    public meta$ = this.metaSubject.asObservable();

    constructor(private service: any) {}

    /**
     * Load paginated data
     * @param filter
     * @param page
     * @param limit
     * @param sortField
     * @param sortDirection
     * @param whereField
     * @param whereValue
     */
    load(filter: string, page: number, limit: number, sortField: string = '',
         sortDirection: string = '', whereField: string = '', whereValue: string = '') {

        this.loadingSubject.next(true);

        this.service.getAll(filter, page, limit, sortField, sortDirection, whereField, whereValue).pipe(
            catchError(() => of([])),
            finalize(() => this.loadingSubject.next(false))
        )
            .subscribe((res) => {
                this.dataSubject.next(res['data']);
                this.metaSubject.next(res['meta']);
            } );
    }

    /**
     * @param url
     * @param filter
     * @param page
     * @param limit
     * @param sortField
     * @param sortDirection
     * @param whereField
     * @param whereValue
     */
    loadNested(url: string, filter: string, page: number, limit: number, sortField: string = '',
         sortDirection: string = '', whereField: string = '', whereValue: string = '') {

        this.loadingSubject.next(true);

        this.service.getNested(url, filter, page, limit, sortField, sortDirection, whereField, whereValue)
            .pipe(
            catchError(() => of([])),
            finalize(() => this.loadingSubject.next(false))
        ).subscribe((res) => {
                this.dataSubject.next(res['data']);
                this.metaSubject.next(res['meta']);
            });
    }

    /**
     *
     * @param collectionViewer
     */
    connect(collectionViewer: CollectionViewer): Observable<any[]> {
        return this.dataSubject.asObservable();
    }

    /**
     *
     * @param collectionViewer
     */
    disconnect(collectionViewer: CollectionViewer): void {
        this.dataSubject.complete();
        this.loadingSubject.complete();
    }

}
