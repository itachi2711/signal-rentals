import { BaseModel } from './models/base-model';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { API_VERSION } from '../../assets/config/api-version';

export class BaseService<T extends BaseModel> {

    private readonly apiUrl: string;
    private readonly resourceUrl: string;

    private protocol = 'https://';
    private readonly version: any;

    constructor( private httpClient: HttpClient, private endpoint: string) {
        this.version = environment.production ?  API_VERSION.prod : API_VERSION.dev;
        const parsedUrl = new URL(window.location.href);

        this.apiUrl = this.protocol + parsedUrl.hostname + this.version;

        const href = parsedUrl.href;
        const productionHost = href.substring(0, href.indexOf('#'));

        if ((parsedUrl.protocol) === 'https:') {
            this.apiUrl = 'https://' + parsedUrl.hostname + this.version;
        } else {
            this.apiUrl = 'http://' + parsedUrl.hostname + this.version;
        }

        if (environment.production) {
            this.apiUrl = productionHost + this.version;
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
    protected getItemUrl(uuid: string): string {
        return `${this.resourceUrl}/${uuid}`;
    }

    private getUrl(uuid?: string) {
        if (uuid !== null) {
            return `${this.resourceUrl}/${uuid}`;
        }
        return this.resourceUrl;
    }

    /**
     *
     * @param fieldName
     */
    list(fieldName: any): Observable<{}> {
        return this.httpClient.get(this.getResourceUrl(), {
            params: new HttpParams()
                .set('list', fieldName)
        });
    }

    /**
     * Fetch resources from remote API
     * @param filter
     * @param page
     * @param limit
     * @param whereField
     * @param whereValue
     * @param sortField
     * @param sortDirection
     */
    getAll(filter: string, page: number, limit: number, sortField: string = '', sortDirection: string = '',
           whereField: string = '', whereValue: string = ''): Observable<{}> {
        return this.httpClient.get(this.getResourceUrl(), {
            params: new HttpParams()
                .set('filter', filter)
                .set('page', page.toString())
                .set('limit', limit.toString())
                .set('sortField', sortField)
                .set('sortDirection', sortDirection)
                .set('whereField', whereField)
                .set('whereValue', whereValue)
        });
    }

    getNested(url: string, filter: string, page: number, limit: number, sortField: string = '', sortDirection: string = '',
           whereField: string = '', whereValue: string = ''): Observable<{}> {
        return this.httpClient.get(url, {
            params: new HttpParams()
                .set('filter', filter)
                .set('page', page.toString())
                .set('limit', limit.toString())
                .set('sortField', sortField)
                .set('sortDirection', sortDirection)
                .set('whereField', whereField)
                .set('whereValue', whereValue)
        });
    }

    fetchBranches(page = 0, limit = 4, sortField: string = '', sortDirection: string = ''): Observable<{}> {
        return this.httpClient.get(this.getResourceUrl(), {
            params: new HttpParams()
                .set('filter', '')
                .set('page', page.toString())
                .set('limit', limit.toString())
                .set('sortField', sortField)
                .set('sortDirection', sortDirection)
        });
    }

    /**
     * Fetch single item by specified id
     * @param uuid
     */
    getById(uuid: string): Observable<T> {
        return this.httpClient
            .get<T>(this.getItemUrl(uuid));
    }

    /**
     * @param url
     */
    getNestedById(url: string): Observable<any> {
        return this.httpClient
            .get<T>(url);
    }

    /**
     * Create a new resource
     * @param item
     */
    public create(item: T): Observable<T> {
        return this.httpClient.post<T>(this.getResourceUrl(), item);
    }

    /**
     * Update an existing resource
     * @param item
     */
    public update(item: T): Observable<T> {
        return this.httpClient.put<T>(this.getItemUrl(item.id), item);
    }

    /**
     * Remove a record from db
     * @param item
     */
    public delete(item: T) {
        return this.httpClient.delete(this.getItemUrl(item.id));
    }

    /**
     * @param baseEntityID
     */
    public nestedLeasesUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/leases`;
    }

    public nestedLeaseUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/leases/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedTenantsUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/tenants`;
    }

    public nestedTenantUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/tenants/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedInvoicesUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/invoices`;
    }

    public nestedInvoiceUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/invoices/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedNoticesUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/notices`;
    }

    public nestedNoticeUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/notices/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedPaymentsUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/payments`;
    }

    public nestedPaymentUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/payments/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedDocumentsUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/documents`;
    }

    public nestedDocumentUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/documents/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedPropertiesUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/properties`;
    }

    public nestedPropertyUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/properties/${itemID}`;
    }

    /**
     * @param baseEntityID
     */
    public nestedUnitsUrl(baseEntityID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/units`;
    }

    public nestedUnitUrl(baseEntityID: string, itemID: string) {
        return `${this.getResourceUrl()}/${baseEntityID}/units/${itemID}`;
    }
}
