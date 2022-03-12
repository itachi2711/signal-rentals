import { PaymentService } from './payment.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class PaymentDataSource extends BaseDataSource {
    constructor(service: PaymentService) {
        super(service);
    }
}
