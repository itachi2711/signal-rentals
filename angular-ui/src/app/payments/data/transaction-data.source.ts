import { TransactionService } from './transaction.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class TransactionDataSource extends BaseDataSource {
    constructor(service: TransactionService) {
        super(service);
    }
}
