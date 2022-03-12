import { PaymentMethodService } from './payment-method.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class PaymentMethodDataSource extends BaseDataSource {
    constructor(service: PaymentMethodService) {
        super(service);
    }
}
