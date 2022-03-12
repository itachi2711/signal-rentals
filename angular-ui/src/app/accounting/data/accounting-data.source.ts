import { AccountingService } from './accounting.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class AccountingDataSource extends BaseDataSource {
    constructor(service: AccountingService) {
        super(service);
    }
}
