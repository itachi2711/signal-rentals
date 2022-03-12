import { DefaultDataService, QueryParams } from '@ngrx/data';
import { HttpClient, HttpParams } from '@angular/common/http';
import { AppHttpUrlGenerator } from '../../core/app-http-url-generator';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { API_VERSION } from '../../../assets/config/api-version';
import { Update } from '@ngrx/entity';
import { BaseModel } from '../models/base-model';

export class BaseDataService<T extends BaseModel>  extends DefaultDataService<T> {

    private readonly apiUrl: string;
    private readonly resourceUrl: string;

    private protocol = 'https://';
    private readonly version: any;

    constructor(entity: string, private endpoint: string, http?: HttpClient, httpUrlGenerator?: AppHttpUrlGenerator) {
        super(entity, http, httpUrlGenerator);

        this.version = environment.production ?  API_VERSION.prod : API_VERSION.dev;
        const parsedUrl = new URL(window.location.href);

        this.apiUrl = this.protocol + parsedUrl.hostname + this.version;

        if ((parsedUrl.protocol) === 'https:') {
            this.apiUrl = 'https://' + parsedUrl.hostname + this.version;
        } else {
            this.apiUrl = 'http://' + parsedUrl.hostname + this.version;
        }
        this.resourceUrl = this.apiUrl + `/` + this.endpoint;
    }

    /**
     *
     */
    protected getApiUrl(): string {
        return this.apiUrl;
    }

    /**
     *
     */
    protected getResourceUrl(): string {
        return this.resourceUrl;
    }

    /**
     * API item url
     * @param uuid
     */
    protected getItemUrl(uuid: string | number): string {
        return `${this.resourceUrl}/${uuid}`;
    }

    getById(key: number | string): Observable<any> {
        return this.http.get(this.getItemUrl(key))
            .pipe(
                map(res => res['data'])
            );
    };


    getAll(): Observable<any> {
        /*return this.http.get('http://localhost/2020/rental/api/public/api/v1/landlords')
            .pipe(
                map(res => res['data'])
            );*/
        return this.http.get(this.getResourceUrl())
            .pipe(
               // map(res => res['data'])
                map((res: any) => {
                    const containers = res.data;
                    containers.meta = res.meta;
                    return containers as any;
                })
            );
    }


/*    fetchAll(filter?: '', page?: 1, limit?: 3,
             sortField?: '', sortDirection?: '', whereField?: '',
             whereValue?: '') {
        this.getWithQuery({
            params: new HttpParams()
                .set('filter', filter)
                .set('page', page.toString())
                .set('limit', limit.toString())
                .set('sortField', sortField)
                .set('sortDirection', sortDirection)
                .set('whereField', whereField)
                .set('whereValue', whereValue)
        })

    }*/

    /**
     *
     * @param queryParams
     */
    getWithQueryxx(queryParams): Observable<any> {

        const httpOptions = {
            headers: { 'Content-Type': 'application/json' },
            params: { ...queryParams}
        };

        return this.http.get(this.getResourceUrl(), httpOptions)
            .pipe(
                map(res => res['data'])
            );
    }

    /**
     * Fetch paginated data from API
     * @param queryParams
     */
    getWithQuery(queryParams): Observable<any> {
        console.log('gikure ... getWithQuery');
        const httpOptions = {
            headers: { 'Content-Type': 'application/json' },
            params: { ...queryParams}
        };
       // const pageIndex = queryParams['page'] || '1';
        return this.http.get(this.getResourceUrl(), httpOptions)
            .pipe(map((res: any) => {
            const containers = res.data;
            containers.meta = res.meta;
            return containers as any;
        }));
    }

    add(entity: any): Observable<any> {
        return this.http.post(this.getResourceUrl(), entity)
            .pipe(
                map(res => res)
            );
    }

    update(update: Update<any>): Observable<any> {
        return this.http.put(this.getItemUrl(update.id), update['changes'])
            .pipe(
                map(res => res)
            );
    }

    delete(key: number | string): Observable<any> {
        return this.http.delete(this.getItemUrl(key))
            .pipe(
                map(res => res)
            );
    }




   // add(entity: T): Observable<T>;
   // delete(key: number | string): Observable<number | string>;
    // getAll(): Observable<T[]>;
   // getById(key: number | string): Observable<T>;
  //  getWithQuery(queryParams: QueryParams | string): Observable<T[]>;
  //  update(update: Update<T>): Observable<T>;
  //  upsert(entity: T): Observable<T>;
}
