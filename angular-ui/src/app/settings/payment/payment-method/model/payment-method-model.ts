import { BaseModel } from '../../../../shared/models/base-model';

export class PaymentMethodModel extends BaseModel {
    payment_method_name: string;
    payment_method_display_name: string;
    payment_method_description: string;
}
