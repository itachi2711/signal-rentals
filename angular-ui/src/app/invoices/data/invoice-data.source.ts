import { InvoiceService } from './invoice.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class InvoiceDataSource extends BaseDataSource {
    constructor(service: InvoiceService) {
        super(service);
    }
}
